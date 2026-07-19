<?php

class GoogleCalendar {
    private string $calendarId;
    private array $serviceAccount;
    private string $tokenCacheFile;
    private array $config;

    private const TOKEN_URI = 'https://oauth2.googleapis.com/token';
    private const CALENDAR_API = 'https://www.googleapis.com/calendar/v3';
    private const SCOPE = 'https://www.googleapis.com/auth/calendar';

    public function __construct(?string $calendarId = null, ?array $serviceAccount = null) {
        $this->calendarId = $calendarId ?: (defined('GCAL_CALENDAR_ID') ? GCAL_CALENDAR_ID : '');
        $this->tokenCacheFile = sys_get_temp_dir() . '/gcal_access_token.json';

        $credPath = defined('GCAL_CREDENTIALS_PATH') ? GCAL_CREDENTIALS_PATH : '';
        if ($serviceAccount) {
            $this->serviceAccount = $serviceAccount;
        } elseif ($credPath && file_exists($credPath)) {
            $this->serviceAccount = json_decode(file_get_contents($credPath), true);
        } else {
            $this->serviceAccount = [];
        }

        $this->config = [
            'working_hours_start' => 9,
            'working_hours_end' => 20,
            'slot_duration_minutes' => 30,
            'buffer_minutes' => 15,
            'timezone' => 'Asia/Kathmandu',
        ];
    }

    public function isConfigured(): bool {
        return !empty($this->serviceAccount['client_email']) && !empty($this->calendarId);
    }

    public function getAvailableSlots(string $date): array {
        if (!$this->isConfigured()) {
            return [];
        }

        $timezone = new DateTimeZone($this->config['timezone']);
        $start = new DateTime($date . ' 00:00:00', $timezone);
        $end = (clone $start)->modify('+1 day');

        $busy = $this->fetchBusyIntervals($start, $end);
        $allSlots = $this->generateSlots($start, $end);
        $available = [];

        foreach ($allSlots as $slot) {
            if (!$this->isOverlapping($slot['start'], $slot['end'], $busy)) {
                $available[] = $slot['label'];
            }
        }

        return $available;
    }

    public function createEvent(array $details): ?string {
        if (!$this->isConfigured()) {
            return null;
        }

        $accessToken = $this->getAccessToken();
        if (!$accessToken) {
            return null;
        }

        $timezone = $this->config['timezone'];
        $startTime = $details['start_time']; // 'HH:MM'
        $date = $details['date'];

        $eventData = [
            'summary' => $details['title'] ?? 'परामर्श',
            'description' => $details['description'] ?? '',
            'start' => [
                'dateTime' => "{$date}T{$startTime}:00",
                'timeZone' => $timezone,
            ],
            'end' => [
                'dateTime' => "{$date}T{$details['end_time']}:00",
                'timeZone' => $timezone,
            ],
        ];

        if (!empty($details['attendees'])) {
            $eventData['attendees'] = $details['attendees'];
        }

        $eventData['conferenceData'] = [
            'createRequest' => [
                'requestId' => uniqid('astro-', true),
                'conferenceSolutionKey' => [
                    'type' => 'hangoutsMeet',
                ],
            ],
        ];

        $result = $this->apiRequest(
            'POST',
            "/calendars/" . urlencode($this->calendarId) . "/events?conferenceDataVersion=1&sendUpdates=all",
            $eventData
        );

        if (!$result) {
            unset($eventData['conferenceData']);
            $result = $this->apiRequest(
                'POST',
                "/calendars/" . urlencode($this->calendarId) . "/events?sendUpdates=all",
                $eventData
            );
        }

        if (!$result) {
            return null;
        }

        $meetUrl = '';

        if (!empty($result['conferenceData']['entryPoints'])) {
            foreach ($result['conferenceData']['entryPoints'] as $entry) {
                if ($entry['entryPointType'] === 'video') {
                    $meetUrl = $entry['uri'];
                    break;
                }
            }
        }

        return $meetUrl ?: ($result['htmlLink'] ?? null);
    }

    private function fetchBusyIntervals(DateTime $start, DateTime $end): array {
        $accessToken = $this->getAccessToken();
        if (!$accessToken) {
            return [];
        }

        $payload = [
            'timeMin' => $start->format(DateTime::RFC3339),
            'timeMax' => $end->format(DateTime::RFC3339),
            'timeZone' => $this->config['timezone'],
            'items' => [
                ['id' => $this->calendarId],
            ],
        ];

        $ch = curl_init(self::CALENDAR_API . '/freeBusy');
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $accessToken,
                'Content-Type: application/json',
            ],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 10,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            error_log("Google Calendar freeBusy failed: HTTP $httpCode - $response");
            return [];
        }

        $data = json_decode($response, true);
        $busy = [];

        foreach ($data['calendars'][$this->calendarId]['busy'] ?? [] as $b) {
            $busy[] = [
                'start' => new DateTime($b['start']),
                'end' => new DateTime($b['end']),
            ];
        }

        return $busy;
    }

    private function generateSlots(DateTime $dayStart, DateTime $dayEnd): array {
        $timezone = new DateTimeZone($this->config['timezone']);
        $workStart = (clone $dayStart)->setTime($this->config['working_hours_start'], 0);
        $workEnd = (clone $dayStart)->setTime($this->config['working_hours_end'], 0);
        $slotDuration = $this->config['slot_duration_minutes'];
        $buffer = $this->config['buffer_minutes'];
        $slots = [];
        $cursor = clone $workStart;

        while ($cursor < $workEnd) {
            $slotEnd = (clone $cursor)->modify("+{$slotDuration} minutes");
            if ($slotEnd > $workEnd) break;

            $slots[] = [
                'start' => clone $cursor,
                'end' => clone $slotEnd,
                'label' => $cursor->format('H:i'),
            ];

            $cursor = (clone $slotEnd)->modify("+{$buffer} minutes");
        }

        return $slots;
    }

    private function isOverlapping(DateTime $slotStart, DateTime $slotEnd, array $busy): bool {
        foreach ($busy as $b) {
            if ($slotStart < $b['end'] && $slotEnd > $b['start']) {
                return true;
            }
        }
        return false;
    }

    private function getAccessToken(): ?string {
        if (!$this->isConfigured()) {
            return null;
        }

        $cached = $this->getCachedToken();
        if ($cached && $cached['expires_at'] > time() + 60) {
            return $cached['access_token'];
        }

        $jwt = $this->createJwt();
        if (!$jwt) {
            return null;
        }

        $ch = curl_init(self::TOKEN_URI);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query([
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion' => $jwt,
            ]),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 10,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            error_log("Google Calendar token exchange failed: HTTP $httpCode - $response");
            return null;
        }

        $data = json_decode($response, true);
        if (empty($data['access_token'])) {
            error_log("Google Calendar: no access_token in response");
            return null;
        }

        $this->cacheToken([
            'access_token' => $data['access_token'],
            'expires_at' => time() + (int)($data['expires_in'] ?? 3600) - 120,
        ]);

        return $data['access_token'];
    }

    private function createJwt(): ?string {
        $sa = $this->serviceAccount;
        $header = self::base64UrlEncode(json_encode(['alg' => 'RS256', 'typ' => 'JWT']));
        $now = time();
        $payload = self::base64UrlEncode(json_encode([
            'iss' => $sa['client_email'],
            'scope' => self::SCOPE,
            'aud' => self::TOKEN_URI,
            'exp' => $now + 3600,
            'iat' => $now,
        ]));

        $signature = '';
        if (!openssl_sign("{$header}.{$payload}", $signature, $sa['private_key'], 'sha256WithRSAEncryption')) {
            error_log("Google Calendar: openssl_sign failed");
            return null;
        }

        return "{$header}.{$payload}." . self::base64UrlEncode($signature);
    }

    private function getCachedToken(): ?array {
        if (!file_exists($this->tokenCacheFile)) {
            return null;
        }
        $data = json_decode(file_get_contents($this->tokenCacheFile), true);
        return is_array($data) ? $data : null;
    }

    private function cacheToken(array $data): void {
        file_put_contents($this->tokenCacheFile, json_encode($data), LOCK_EX);
    }

    private static function base64UrlEncode(string $data): string {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private function apiRequest(string $method, string $path, ?array $body = null): ?array {
        $accessToken = $this->getAccessToken();
        if (!$accessToken) {
            return null;
        }

        $ch = curl_init(self::CALENDAR_API . $path);
        $options = [
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $accessToken,
                'Content-Type: application/json',
            ],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 15,
        ];

        if ($method === 'POST') {
            $options[CURLOPT_POST] = true;
            if ($body) {
                $options[CURLOPT_POSTFIELDS] = json_encode($body);
            }
        }

        curl_setopt_array($ch, $options);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode < 200 || $httpCode >= 300) {
            error_log("Google Calendar API {$method} {$path} failed: HTTP {$httpCode} - {$response}");
            return null;
        }

        return json_decode($response, true) ?: null;
    }
}
