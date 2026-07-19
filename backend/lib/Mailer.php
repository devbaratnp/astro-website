<?php

class Mailer {
    private string $fromEmail;
    private string $fromName;
    private string $smtpHost;
    private int $smtpPort;
    private string $smtpUser;
    private string $smtpPass;
    private bool $useSmtp;

    public function __construct() {
        $this->fromEmail = defined('SMTP_FROM') ? SMTP_FROM : 'noreply@astroshreehari.com';
        $this->fromName = defined('SITE_NAME_EN') ? SITE_NAME_EN : 'Astro Shree Hari';
        $this->smtpHost = defined('SMTP_HOST') ? SMTP_HOST : '';
        $this->smtpPort = defined('SMTP_PORT') ? (int)SMTP_PORT : 587;
        $this->smtpUser = defined('SMTP_USER') ? SMTP_USER : '';
        $this->smtpPass = defined('SMTP_PASS') ? SMTP_PASS : '';
        $this->useSmtp = !empty($this->smtpHost) && !empty($this->smtpUser);
    }

    public function send(string $to, string $subject, string $htmlBody): bool {
        $boundary = 'boundary_' . bin2hex(random_bytes(16));
        $headers = [
            'MIME-Version: 1.0',
            'Content-Type: multipart/alternative; boundary="' . $boundary . '"',
            'From: ' . $this->encodeHeader($this->fromName) . ' <' . $this->fromEmail . '>',
            'Reply-To: ' . (defined('ADMIN_EMAIL') ? ADMIN_EMAIL : $this->fromEmail),
            'X-Mailer: AstroShreeHari/1.0',
        ];

        $plainText = strip_tags(str_replace(['<br>', '<br/>', '<br />', '</p>', '</div>', '</li>'], "\n", $htmlBody));
        $plainText = preg_replace('/\n{3,}/', "\n\n", $plainText);

        $body = "--{$boundary}\r\n"
              . "Content-Type: text/plain; charset=UTF-8\r\n"
              . "Content-Transfer-Encoding: 8bit\r\n\r\n"
              . $plainText . "\r\n\r\n"
              . "--{$boundary}\r\n"
              . "Content-Type: text/html; charset=UTF-8\r\n"
              . "Content-Transfer-Encoding: 8bit\r\n\r\n"
              . $htmlBody . "\r\n\r\n"
              . "--{$boundary}--";

        if ($this->useSmtp) {
            return $this->sendSmtp($to, $subject, $headers, $body);
        }

        return mail($to, $this->encodeHeader($subject), $body, implode("\r\n", $headers), "-f {$this->fromEmail}");
    }

    public static function clientConfirmation(array $d): string {
        extract(self::labels($d));
        $vr = $meetingUrl
            ? '<tr><td class="l" style="width:110px">भिडियो लिङ्क</td><td class="v"><a href="' . $meetingUrl . '" style="color:#671817">' . $meetingUrl . '</a></td></tr>'
            : '';

        return self::doc(
            self::header('परामर्श अनुरोध पुष्टि') . '
<tr><td class="c">
<h2 class="h">तपाईंको अनुरोध प्राप्त भयो</h2>
<p class="p">धन्यवाद ' . $name . ' ज्यू। तपाईंको परामर्श अनुरोध सफलतापूर्वक प्राप्त भएको छ। हामी २४ घण्टाभित्र सम्पर्क गर्नेछौं।</p>
<table class="t"><tr><td class="l" style="width:110px">सेवा</td><td class="v">' . $serviceLabel . '</td></tr><tr><td class="l">मिति</td><td class="v">' . $date . '</td></tr><tr><td class="l">समय</td><td class="v">' . $time . '</td></tr><tr><td class="l">माध्यम</td><td class="v">' . $modeLabel . '</td></tr>' . $vr . '</table>
<div class="nb"><strong>सम्पर्क:</strong> +977 9844639228 &nbsp;|&nbsp; <strong>इमेल:</strong> astroshreehari3m@gmail.com</div>
</td></tr>' . self::footer()
        );
    }

    public static function adminNotification(array $d): string {
        extract(self::labels($d));
        $vr = $meetingUrl
            ? '<tr><td class="l">भिडियो लिङ्क</td><td class="v"><a href="' . $meetingUrl . '" style="color:#671817">' . $meetingUrl . '</a></td></tr>'
            : '';

        $bb = ($birthDate || $birthPlace) ? '
<h3 class="sh">जन्म विवरण</h3>
<table class="t"><tr><td class="l">मिति</td><td class="v">' . $birthDate . '</td></tr><tr><td class="l">समय</td><td class="v">' . $birthTime . '</td></tr><tr><td class="l">स्थान</td><td class="v">' . $birthPlace . '</td></tr></table>' : '';

        return self::doc(
            self::header('नयाँ परामर्श अनुरोध') . '
<tr><td class="c">
<div class="alert"><strong>कारबाही आवश्यक:</strong> ' . $name . ' ले "' . $modeLabel . '" माध्यमबाट परामर्श अनुरोध गरेको। कृपया चाँडै सम्पर्क गर्नुहोस्।</div>
<h3 class="sh">सम्पर्क विवरण</h3>
<table class="t"><tr><td class="l">नाम</td><td class="v">' . $name . '</td></tr><tr><td class="l">फोन</td><td class="v"><a href="tel:' . $phone . '" style="color:#671817;text-decoration:none">' . $phone . '</a></td></tr><tr><td class="l">इमेल</td><td class="v">' . ($email ?: '—') . '</td></tr><tr><td class="l">सेवा</td><td class="v">' . $serviceLabel . '</td></tr><tr><td class="l">मिति</td><td class="v">' . $date . '</td></tr><tr><td class="l">समय</td><td class="v">' . $time . '</td></tr><tr><td class="l">माध्यम</td><td class="v">' . $modeLabel . '</td></tr>' . $vr . '</table>' . $bb . '
<h3 class="sh">प्रश्न / समस्या</h3>
<div class="msg">' . $message . '</div>
<div class="ok"><strong>✅ Google Calendar</strong> मा इभेन्ट सिर्जना गरिएको छ। <a href="https://calendar.google.com" style="color:#0b8d4e">Calendar खोल्नुहोस् →</a></div>
</td></tr>' . self::footer()
        );
    }

    private static function labels(array $d): array {
        $sl = ['kundali'=>'जन्मकुण्डली विश्लेषण','marriage'=>'विवाह तथा गुण मिलान','grahadasha'=>'ग्रहदशा परामर्श','vastu'=>'वास्तु परामर्श','pooja'=>'वैदिक कर्मकाण्ड','general'=>'सामान्य परामर्श'];
        $ml = ['phone'=>'फोन','whatsapp'=>'WhatsApp','video'=>'भिडियो परामर्श','inperson'=>'प्रत्यक्ष'];
        return [
            'name' => htmlspecialchars($d['name'] ?? '', ENT_QUOTES, 'UTF-8'),
            'phone' => htmlspecialchars($d['phone'] ?? '', ENT_QUOTES, 'UTF-8'),
            'email' => htmlspecialchars($d['email'] ?? '', ENT_QUOTES, 'UTF-8'),
            'date' => htmlspecialchars($d['preferred_date'] ?? '', ENT_QUOTES, 'UTF-8'),
            'time' => htmlspecialchars($d['preferred_time'] ?? '', ENT_QUOTES, 'UTF-8'),
            'mode' => htmlspecialchars($d['consultation_mode'] ?? '', ENT_QUOTES, 'UTF-8'),
            'message' => htmlspecialchars($d['message'] ?? '', ENT_QUOTES, 'UTF-8'),
            'meetingUrl' => htmlspecialchars($d['meeting_url'] ?? '', ENT_QUOTES, 'UTF-8'),
            'birthDate' => htmlspecialchars($d['birth_date'] ?? '', ENT_QUOTES, 'UTF-8'),
            'birthTime' => htmlspecialchars($d['birth_time'] ?? '', ENT_QUOTES, 'UTF-8'),
            'birthPlace' => htmlspecialchars($d['birth_place'] ?? '', ENT_QUOTES, 'UTF-8'),
            'serviceLabel' => $sl[$d['service_type'] ?? ''] ?? ($d['service_type'] ?? ''),
            'modeLabel' => $ml[$d['consultation_mode'] ?? ''] ?? ($d['consultation_mode'] ?? ''),
        ];
    }

    private static function header(string $title): string {
        return '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" class="hdr"><tr><td>
<img src="https://www.astroshreehari.com/assets/shreehari-icon-192.png" alt="" width="48" height="48" style="border-radius:50%;border:2px solid #d8a443;margin-bottom:6px">
<h1 class="site">श्रीहरि ज्योतिष परामर्श केन्द्र</h1>
<p class="tag">' . $title . '</p>
</td></tr></table>';
    }

    private static function footer(): string {
        return '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" class="ftr"><tr><td>
<p class="f1">श्रीहरि ज्योतिष परामर्श केन्द्र &bull; कमल-३, केरखा, झापा</p>
<p class="f2">© 2026 <a href="https://www.astroshreehari.com" style="color:#d8a443;text-decoration:none">Astro Shree Hari</a></p>
</td></tr></table>';
    }

    private static function doc(string $body): string {
        return '<!DOCTYPE html>
<html lang="ne">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<style>
  body{margin:0;padding:0;background:#f9f0df;font-family:"Noto Sans Devanagari",system-ui,sans-serif}
  table{border-collapse:collapse;width:100%;max-width:600px;margin:0 auto}
  .hdr{background:linear-gradient(135deg,#3d0d0d,#671817);padding:28px 24px;text-align:center}
  .site{margin:6px 0 0;font-family:"Tiro Devanagari Sanskrit",serif;color:#d8a443;font-size:20px;font-weight:400}
  .tag{margin:2px 0 0;color:#f0c978;font-size:12px;opacity:.85}
  .c{padding:28px 24px;background:#fffaf1}
  .h{margin:0 0 4px;font-family:"Tiro Devanagari Sanskrit",serif;color:#671817;font-size:18px;font-weight:400}
  .p{margin:0 0 20px;color:#76665f;font-size:14px}
  .sh{margin:20px 0 6px;font-size:14px;color:#671817}
  .t{border:1px solid #e8c889;border-radius:10px;overflow:hidden;font-size:14px;width:100%}
  .l{padding:10px 16px;background:#f9f0df;font-weight:600;color:#2c1a16;white-space:nowrap;vertical-align:top}
  .v{padding:10px 16px;color:#2c1a16}
  .nb{margin:20px 0 0;padding:14px 18px;background:#f9f0df;border-radius:10px;border-left:4px solid #d8a443;font-size:13px;color:#76665f}
  .alert{margin:0 0 16px;padding:10px 14px;background:#fef2f2;border:1px solid #fecaca;border-radius:10px;font-size:13px;color:#991b1b}
  .msg{margin:0;padding:12px 16px;background:#f9f0df;border-radius:10px;border-left:4px solid #671817;font-size:13px;color:#2c1a16;white-space:pre-wrap}
  .ok{margin:20px 0 0;padding:12px 16px;background:#f0fdf4;border-radius:10px;border-left:4px solid #0b8d4e;font-size:13px;color:#166534}
  .ftr{background:linear-gradient(135deg,#671817,#3d0d0d);padding:20px 24px;text-align:center}
  .f1{margin:0 0 4px;color:#f0c978;font-size:13px}
  .f2{margin:0;color:#76665f;font-size:11px}
</style></head>
<body>
<table>' . $body . '</table>
</body>
</html>';
    }

    private function sendSmtp(string $to, string $subject, array $headers, string $body): bool {
        $errno = 0; $errstr = '';
        $socket = @fsockopen($this->smtpHost, $this->smtpPort, $errno, $errstr, 15);
        if (!$socket) {
            error_log("Mailer: SMTP connection failed ({$errno}) {$errstr}");
            return mail($to, $this->encodeHeader($subject), $body, implode("\r\n", $headers), "-f {$this->fromEmail}");
        }

        $r = function($s) { $o = ''; while ($l = @fgets($s, 512)) { $o .= $l; if (isset($l[3]) && $l[3] === ' ') break; } return $o; };
        $w = function($s, $d) { @fwrite($s, $d . "\r\n"); };

        $r($socket); $w($socket, "EHLO astroshreehari.com"); $r($socket);
        if ($this->smtpPort === 587) {
            $w($socket, "STARTTLS"); $r($socket);
            @stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
            $w($socket, "EHLO astroshreehari.com"); $r($socket);
        }
        $w($socket, "AUTH LOGIN"); $r($socket);
        $w($socket, base64_encode($this->smtpUser)); $r($socket);
        $w($socket, base64_encode($this->smtpPass)); $r($socket);
        $w($socket, "MAIL FROM:<{$this->fromEmail}>"); $r($socket);
        $w($socket, "RCPT TO:<{$to}>"); $r($socket);
        $w($socket, "DATA"); $r($socket);
        $w($socket, implode("\r\n", $headers) . "\r\nTo: {$to}\r\nSubject: {$subject}\r\n\r\n{$body}\r\n.");
        $r($socket);
        $w($socket, "QUIT");
        fclose($socket);
        return true;
    }

    private function encodeHeader(string $value): string {
        return '=?UTF-8?B?' . base64_encode($value) . '?=';
    }
}
