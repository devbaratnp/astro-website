<?php

class Panchang {
    private static array $moonRashis = [
        'मेष', 'वृष', 'मिथुन', 'कर्कट', 'सिंह', 'कन्या',
        'तुला', 'वृश्चिक', 'धनु', 'मकर', 'कुम्भ', 'मीन',
    ];

    public static function getForDate(string $date): array {
        $timestamp = strtotime($date);
        $dayOfYear = (int)date('z', $timestamp);

        $tithis = ['प्रतिपदा', 'द्वितीया', 'तृतीया', 'चतुर्थी', 'पञ्चमी', 'षष्ठी', 'सप्तमी', 'अष्टमी', 'नवमी', 'दशमी', 'एकादशी', 'द्वादशी', 'त्रयोदशी', 'चतुर्दशी', 'पूर्णिमा', 'प्रतिपदा', 'द्वितीया', 'तृतीया', 'चतुर्थी', 'पञ्चमी', 'षष्ठी', 'सप्तमी', 'अष्टमी', 'नवमी', 'दशमी', 'एकादशी', 'द्वादशी', 'त्रयोदशी', 'चतुर्दशी', 'अमावास्या'];
        $tithiIndex = ($dayOfYear * 2) % 30;

        $nakshatras = ['अश्विनी', 'भरणी', 'कृत्तिका', 'रोहिणी', 'मृगशिरा', 'आर्द्रा', 'पुनर्वसु', 'पुष्य', 'अश्लेषा', 'मघा', 'पूर्वाफाल्गुनी', 'उत्तराफाल्गुनी', 'हस्त', 'चित्रा', 'स्वाती', 'विशाखा', 'अनुराधा', 'ज्येष्ठा', 'मूल', 'पूर्वाषाढा', 'उत्तराषाढा', 'श्रवण', 'धनिष्ठा', 'शतभिषा', 'पूर्वभाद्रपद', 'उत्तरभाद्रपद', 'रेवती'];
        $nakshatraIndex = ($dayOfYear * 27 / 365) % 27;

        $moonRashiIndex = (int)($nakshatraIndex * 4 / 9);
        if ($moonRashiIndex > 11) $moonRashiIndex = 11;

        $sunrise = @date_sunrise($timestamp, SUNFUNCS_RET_STRING, 27.7172, 85.3240, 90.5, 5.75);
        $sunset = @date_sunset($timestamp, SUNFUNCS_RET_STRING, 27.7172, 85.3240, 90.5, 5.75);

        return [
            'date' => $date,
            'tithi' => $tithis[(int)$tithiIndex],
            'nakshatra' => $nakshatras[(int)$nakshatraIndex],
            'moon_rashi' => self::$moonRashis[$moonRashiIndex],
            'sunrise' => $sunrise ?: '06:00',
            'sunset' => $sunset ?: '18:00',
            'day_of_week' => date('l', $timestamp),
            'special_events' => self::getSpecialEvents($date),
        ];
    }

    private static function getSpecialEvents(string $date): array {
        $events = [];
        $monthDay = substr($date, 5);

        $festivals = [
            '01-01' => ['ne' => 'नयाँ वर्ष', 'en' => 'New Year'],
            '01-15' => ['ne' => 'माघे संक्रान्ति', 'en' => 'Maghe Sankranti'],
            '08-30' => ['ne' => 'गाई जात्रा', 'en' => 'Gai Jatra'],
            '09-15' => ['ne' => 'इन्द्र जात्रा', 'en' => 'Indra Jatra'],
            '10-01' => ['ne' => 'दशैं सुरु', 'en' => 'Dashain Begins'],
            '10-15' => ['ne' => 'धनतेरस', 'en' => 'Dhanteras'],
            '10-17' => ['ne' => 'दीपावली', 'en' => 'Deepawali'],
            '11-15' => ['ne' => 'छठ पर्व', 'en' => 'Chhath Parva'],
        ];

        if (isset($festivals[$monthDay])) {
            $events[] = $festivals[$monthDay];
        }

        return $events;
    }
}
