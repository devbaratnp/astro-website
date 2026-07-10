<?php

class Astrology {
    private ?DateTime $birthDate;
    private ?string $birthPlace;

    public function __construct(?string $date, ?string $time, ?string $place = null) {
        if ($date && $time) {
            $this->birthDate = new DateTime("{$date} {$time}", new DateTimeZone('Asia/Kathmandu'));
        } elseif ($date) {
            $this->birthDate = new DateTime($date, new DateTimeZone('Asia/Kathmandu'));
        } else {
            $this->birthDate = null;
        }
        $this->birthPlace = $place ?: 'Kathmandu';
    }

    public function calculateRashi(): string {
        if (!$this->birthDate) return 'अज्ञात';
        $dayOfYear = (int)$this->birthDate->format('z');
        $rashis = ['मेष', 'वृष', 'मिथुन', 'कर्क', 'सिंह', 'कन्या', 'तुला', 'वृश्चिक', 'धनु', 'मकर', 'कुम्भ', 'मीन'];
        $index = intdiv($dayOfYear, 30) % 12;
        return $rashis[$index];
    }

    public function calculateNakshatra(): string {
        if (!$this->birthDate) return 'अज्ञात';
        $dayOfYear = (int)$this->birthDate->format('z');
        $nakshatras = ['अश्विनी', 'भरणी', 'कृत्तिका', 'रोहिणी', 'मृगशिरा', 'आर्द्रा', 'पुनर्वसु', 'पुष्य', 'अश्लेषा', 'मघा', 'पूर्वाफाल्गुनी', 'उत्तराफाल्गुनी', 'हस्त', 'चित्रा', 'स्वाती', 'विशाखा', 'अनुराधा', 'ज्येष्ठा', 'मूल', 'पूर्वाषाढा', 'उत्तराषाढा', 'श्रवण', 'धनिष्ठा', 'शतभिषा', 'पूर्वभाद्रपद', 'उत्तरभाद्रपद', 'रेवती'];
        $index = intdiv($dayOfYear * 27, 365) % 27;
        return $nakshatras[$index];
    }

    public function calculateLagna(): string {
        if (!$this->birthDate) return 'अज्ञात';
        $hour = (int)$this->birthDate->format('H');
        $rashis = ['मेष', 'वृष', 'मिथुन', 'कर्क', 'सिंह', 'कन्या', 'तुला', 'वृश्चिक', 'धनु', 'मकर', 'कुम्भ', 'मीन'];
        $index = intdiv($hour, 2) % 12;
        return $rashis[$index];
    }

    public function getBasicDetails(): array {
        if (!$this->birthDate) {
            return [
                'rashi' => 'कृपया जन्म मिति र समय प्रविष्ट गर्नुहोस्',
                'nakshatra' => '—',
                'lagna' => '—',
                'date' => null,
                'place' => $this->birthPlace,
            ];
        }
        return [
            'rashi' => $this->calculateRashi(),
            'nakshatra' => $this->calculateNakshatra(),
            'lagna' => $this->calculateLagna(),
            'date' => $this->birthDate->format('Y-m-d H:i'),
            'place' => $this->birthPlace,
        ];
    }
}
