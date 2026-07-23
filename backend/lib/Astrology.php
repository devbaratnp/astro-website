<?php

class Astrology {
    private const YUGA = 1577917828.0;
    private const KALI_JD = 588465.5;
    private const RAD = 0.017453292519943295;
    private const DEG = 57.29577951308232;

    private static array $rashiNames = ['मेष', 'वृष', 'मिथुन', 'कर्कट', 'सिंह', 'कन्या', 'तुला', 'वृश्चिक', 'धनु', 'मकर', 'कुम्भ', 'मीन'];
    private static array $rashiLords = ['मंगल', 'शुक्र', 'बुध', 'चन्द्र', 'सूर्य', 'बुध', 'शुक्र', 'मंगल', 'गुरु', 'शनि', 'शनि', 'गुरु'];
    private static array $nk = ['अश्विनी', 'भरणी', 'कृत्तिका', 'रोहिणी', 'मृगशिरा', 'आद्रा', 'पुनर्वसु', 'पुष्य', 'आश्लेषा', 'मघा', 'पू.फाल्गुनी', 'उ.फाल्गुनी', 'हस्त', 'चित्रा', 'स्वाती', 'विशाखा', 'अनुराधा', 'ज्येष्ठा', 'मूल', 'पू.षाढा', 'उ.षाढा', 'श्रवण', 'धनिष्ठा', 'शतभिषा', 'पू.भाद्रपदा', 'उ.भाद्रपदा', 'रेवती'];
    private static array $nl = ['केतु', 'शुक्र', 'सूर्य', 'चन्द्र', 'मंगल', 'राहु', 'गुरु', 'शनि', 'बुध', 'केतु', 'शुक्र', 'सूर्य', 'चन्द्र', 'मंगल', 'राहु', 'गुरु', 'शनि', 'बुध', 'केतु', 'शुक्र', 'सूर्य', 'चन्द्र', 'मंगल', 'राहु', 'गुरु', 'शनि', 'बुध'];
    private static array $grahaNames = ['su' => 'सूर्य', 'mo' => 'चन्द्र', 'ma' => 'मंगल', 'me' => 'बुध', 'ju' => 'गुरु', 've' => 'शुक्र', 'sa' => 'शनि', 'ra' => 'राहु', 'ke' => 'केतु', 'la' => 'लग्न'];

    private ?DateTime $birthDate;
    private ?string $birthPlace;
    private float $lat;
    private float $lon;

    public function __construct(?string $date, ?string $time, ?string $place = null, float $lat = 27.7172, float $lon = 85.3240) {
        if ($date && $time) {
            $this->birthDate = new DateTime("{$date} {$time}", new DateTimeZone('Asia/Kathmandu'));
        } elseif ($date) {
            $this->birthDate = new DateTime($date, new DateTimeZone('Asia/Kathmandu'));
        } else {
            $this->birthDate = null;
        }
        $this->birthPlace = $place ?: 'काठमाडौँ';
        $this->lat = $lat;
        $this->lon = $lon;
    }

    private static function n360(float $d): float {
        $d = fmod($d, 360);
        return $d < 0 ? $d + 360 : $d;
    }

    private static function kR(float $d): float {
        $d = self::n360($d);
        if ($d >= 90 && $d < 180) return 180 - $d;
        if ($d >= 180 && $d < 270) return $d - 180;
        if ($d >= 270) return 360 - $d;
        return $d;
    }

    private static function findSuryaPasta(float $ah): array {
        $ms = fmod((4320000 * $ah), self::YUGA) / self::YUGA * 360;
        $apogee = (($ah / self::YUGA) * 0.387 * 360) + 77.13;
        $d3 = self::n360($apogee - $ms);
        $d4 = self::kR($d3);
        $d5 = $d4 * self::RAD;
        $sinD5 = sin($d5);
        $epi = (14 - $sinD5 / 3) / 360;
        $abs = abs(asin($epi * sin($d3 * self::RAD)) * self::DEG);
        $madyamKgati = 0.9856026545889309;
        $gati = $madyamKgati;
        if ($d3 > 90 || $d3 < 270) {
            $gati = $madyamKgati + ((($epi) * cos($d5) / 360) / cos($abs * self::RAD)) * $madyamKgati;
        }
        if ($d3 < 90 || $d3 > 270) {
            $gati = $madyamKgati - ((($epi) * cos($d5) / 360) / cos($abs * self::RAD)) * $madyamKgati;
        }
        $pos = $d3 > 180 ? $ms - $abs : $ms + $abs;
        return ['pos' => self::n360($pos), 'gati' => $gati, 'madyam' => $ms];
    }

    private static function findChandraPasta(float $ah): array {
        $ms = fmod((57753336 * $ah), self::YUGA) / self::YUGA * 360;
        $apRaw = (fmod(($ah * 488199), self::YUGA) / self::YUGA) * 360 + 90;
        if ($apRaw >= 360) $apRaw -= 360;
        $d4 = self::n360($apRaw - $ms);
        $d5 = self::kR($d4);
        $d6 = $d5 * self::RAD;
        $sinD6 = sin($d6);
        $epi = (32 - $sinD6 / 3) / 360;
        $abs = abs(asin($epi * sin($d4 * self::RAD)) * self::DEG);
        $pos = $d4 > 180 ? $ms - $abs : $ms + $abs;
        $abs2 = abs((($epi) * cos($d6) / 360) / cos($abs * self::RAD) * 13.17635214652);
        $gati = ($d4 < 90 || $d4 > 270) ? 13.17635214652 - $abs2 : $abs2 + 13.17635214652;
        return ['pos' => self::n360($pos), 'gati' => $gati, 'madyam' => $ms];
    }

    private static function findMangalSS(float $ah, float $ms): float {
        $d2 = fmod((2296832 * $ah), self::YUGA) / self::YUGA * 360;
        $d3 = self::n360($ms - $d2);
        $d4 = self::kR($d3);
        $d5 = $d4 * self::RAD;
        $sin1 = sin($d5);
        $sinVal = ((235 - $sin1 * 3) / 360) * $sin1;
        $cosVal = ((235 - $sin1 * 3) / 360) * cos($d5);
        $d6 = ($d3 > 90 || $d3 < 270) ? 1 - $cosVal : 0;
        if ($d3 < 90 || $d3 > 270) $d6 = $cosVal + 1;
        $asinVal = (asin($sinVal / sqrt($sinVal * $sinVal + $d6 * $d6)) / 2 / M_PI) * 180;
        $d8 = $d3 > 180 ? $d2 - $asinVal : $d2 + $asinVal;
        $shA = (($ah / self::YUGA) * 0.204 * 360) + 129.96;
        $d10 = self::n360($shA - $d8);
        $d11 = self::kR($d10);
        $d12 = $d11 * self::RAD;
        $sinD12 = sin($d12);
        $abs1 = abs(asin(((75 - $sinD12 * 3) / 360) * $sinD12) * self::DEG / 2);
        $tmp = $d10 > 180 ? $d8 - $abs1 : $d8 + $abs1;
        $d13 = self::n360($shA - $tmp);
        $d14 = self::kR($d13);
        $d15 = $d14 * self::RAD;
        $sinD15 = sin($d15);
        $sin3 = ((75 - $sinD15 * 3) / 360) * $sinD15;
        $abs2 = abs(asin($sin3) * self::DEG);
        $pp = $d13 > 180 ? $d2 - $abs2 : $d2 + $abs2;
        $d16 = self::n360($ms - $pp);
        $d17 = self::kR($d16);
        $d18 = $d17 * self::RAD;
        $sinD18 = sin($d18);
        $sin5 = ((235 - $sinD18 * 3) / 360) * $sinD18;
        $cos2 = ((235 - $sinD18 * 3) / 360) * cos($d18);
        $d6b = ($d16 > 90 || $d16 < 270) ? 1 - $cos2 : 0;
        if ($d16 < 90 || $d16 > 270) $d6b = $cos2 + 1;
        $sqrt = sqrt($sin5 * $sin5 + $d6b * $d6b);
        $asin2 = (asin($sin5 / $sqrt) / M_PI) * 180;
        if ($d16 <= 180) $pp += $asin2;
        if ($d16 > 180) $pp -= $asin2;
        return self::n360($pp);
    }

    private static function findBudhaSS(float $ah, float $ms): float {
        $mb = fmod((17937076 * $ah), self::YUGA) / self::YUGA * 360;
        $d3 = self::n360($mb - $ms);
        $d4 = self::kR($d3);
        $d5 = $d4 * self::RAD;
        $sin1 = sin($d5);
        $sinVal = ((133 - $sin1) / 360) * $sin1;
        $cosVal = ((133 - $sin1) / 360) * cos($d5);
        $d6 = ($d3 > 90 || $d3 < 270) ? 1 - $cosVal : 0;
        if ($d3 < 90 || $d3 > 270) $d6 = $cosVal + 1;
        $asinVal = (asin($sinVal / sqrt($sinVal * $sinVal + $d6 * $d6)) / 2 / M_PI) * 180;
        $d8 = $d3 > 180 ? $ms - $asinVal : $ms + $asinVal;
        $shA = (($ah / self::YUGA) * 0.368 * 360) + 220.32;
        $d10 = self::n360($shA - $d8);
        $d11 = self::kR($d10);
        $d12 = $d11 * self::RAD;
        $sinD12 = sin($d12);
        $abs1 = abs((asin(((30 - $sinD12 * 2) / 360) * $sinD12) / 2) * self::DEG);
        $tmp = $d10 > 180 ? $d8 - $abs1 : $d8 + $abs1;
        $d13 = self::n360($shA - $tmp);
        $d14 = self::kR($d13);
        $d15 = $d14 * self::RAD;
        $sinD15 = sin($d15);
        $sin3 = ((30 - $sinD15 * 2) / 360) * $sinD15;
        $abs2 = abs(asin($sin3) * self::DEG);
        $pp = $d13 > 180 ? $ms - $abs2 : $ms + $abs2;
        $d15b = self::n360($mb - $pp);
        $d16 = self::kR($d15b);
        $d17 = $d16 * self::RAD;
        $sinD17 = sin($d17);
        $sin5 = ((133 - $sinD17) / 360) * $sinD17;
        $cos2 = ((133 - $sinD17) / 360) * cos($d17);
        $d6b = ($d15b > 90 || $d15b < 270) ? 1 - $cos2 : 0;
        if ($d15b < 90 || $d15b > 270) $d6b = $cos2 + 1;
        $sqrt = sqrt($sin5 * $sin5 + $d6b * $d6b);
        $asin2 = (asin($sin5 / $sqrt) / M_PI) * 180;
        if ($d15b <= 180) $pp += $asin2;
        if ($d15b > 180) $pp -= $asin2;
        return self::n360($pp);
    }

    private static function findGuruSS(float $ah, float $ms): float {
        $mg = fmod((364212 * $ah), self::YUGA) / self::YUGA * 360;
        $d3 = self::n360($ms - $mg);
        $d4 = self::kR($d3);
        $d5 = $d4 * self::RAD;
        $sinD5 = sin($d5);
        $sinVal = (($sinD5 * 2 + 70) / 360) * $sinD5;
        $cosVal = (($sinD5 * 2 + 70) / 360) * cos($d5);
        $d6 = ($d3 > 90 || $d3 < 270) ? 1 - $cosVal : 0;
        if ($d3 < 90 || $d3 > 270) $d6 = $cosVal + 1;
        $asinVal = (asin($sinVal / sqrt($sinVal * $sinVal + $d6 * $d6)) / 2 / M_PI) * 180;
        $d8 = $d3 > 180 ? $mg - $asinVal : $mg + $asinVal;
        $shA = (($ah / self::YUGA) * 0.9 * 360) + 171;
        $d10 = self::n360($shA - $d8);
        $d11 = self::kR($d10);
        $d12 = $d11 * self::RAD;
        $sinD12 = sin($d12);
        $abs1 = abs((asin(((33 - $sinD12) / 360) * $sinD12) / 2) * self::DEG);
        $tmp = $d10 > 180 ? $d8 - $abs1 : $d8 + $abs1;
        $d13 = self::n360($shA - $tmp);
        $d14 = self::kR($d13);
        $d15 = $d14 * self::RAD;
        $sinD15 = sin($d15);
        $sin3 = ((33 - $sinD15) / 360) * $sinD15;
        $abs2 = abs(asin($sin3) * self::DEG);
        $pp = $d13 > 180 ? $mg - $abs2 : $mg + $abs2;
        $d16 = self::n360($ms - $pp);
        $d17 = self::kR($d16);
        $d18 = $d17 * self::RAD;
        $sinD18 = sin($d18);
        $sin5 = (($sinD18 * 2 + 70) / 360) * $sinD18;
        $cos2 = (($sinD18 * 2 + 70) / 360) * cos($d18);
        $d6b = ($d16 > 90 || $d16 < 270) ? 1 - $cos2 : 0;
        if ($d16 < 90 || $d16 > 270) $d6b = $cos2 + 1;
        $sqrt = sqrt($sin5 * $sin5 + $d6b * $d6b);
        $asin2 = (asin($sin5 / $sqrt) / M_PI) * 180;
        if ($d16 <= 180) $pp += $asin2;
        if ($d16 > 180) $pp -= $asin2;
        return self::n360($pp);
    }

    private static function findSaniSS(float $ah, float $ms): float {
        $mg = fmod((146580 * $ah), self::YUGA) / self::YUGA * 360;
        $d3 = self::n360($ms - $mg);
        $d4 = self::kR($d3);
        $d5 = $d4 * self::RAD;
        $sinD5 = sin($d5);
        $sinVal = (($sinD5 + 39) / 360) * $sinD5;
        $cosVal = (($sinD5 + 39) / 360) * cos($d5);
        $d6 = ($d3 > 90 || $d3 < 270) ? 1 - $cosVal : 0;
        if ($d3 < 90 || $d3 > 270) $d6 = $cosVal + 1;
        $asinVal = (asin($sinVal / sqrt($sinVal * $sinVal + $d6 * $d6)) / 2 / M_PI) * 180;
        $d8 = $d3 > 180 ? $mg - $asinVal : $mg + $asinVal;
        $shA = (($ah / self::YUGA) * 0.039 * 360) + 236.61;
        $d10 = self::n360($shA - $d8);
        $d11 = self::kR($d10);
        $d12 = $d11 * self::RAD;
        $sinD12 = sin($d12);
        $abs1 = abs((asin(((49 - $sinD12) / 360) * $sinD12) / 2) * self::DEG);
        $tmp = $d10 > 180 ? $d8 - $abs1 : $d8 + $abs1;
        $d13 = self::n360($shA - $tmp);
        $d14 = self::kR($d13);
        $d15 = $d14 * self::RAD;
        $sinD15 = sin($d15);
        $sin3 = ((49 - $sinD15) / 360) * $sinD15;
        $abs2 = abs(asin($sin3) * self::DEG);
        $pp = $d13 > 180 ? $mg - $abs2 : $mg + $abs2;
        $d16 = self::n360($ms - $pp);
        $d17 = self::kR($d16);
        $d18 = $d17 * self::RAD;
        $sinD18 = sin($d18);
        $sin5 = (($sinD18 + 39) / 360) * $sinD18;
        $cos2 = (($sinD18 + 39) / 360) * cos($d18);
        $d6b = ($d16 > 90 || $d16 < 270) ? 1 - $cos2 : 0;
        if ($d16 < 90 || $d16 > 270) $d6b = $cos2 + 1;
        $sqrt = sqrt($sin5 * $sin5 + $d6b * $d6b);
        $asin2 = (asin($sin5 / $sqrt) / M_PI) * 180;
        if ($d16 <= 180) $pp += $asin2;
        if ($d16 > 180) $pp -= $asin2;
        return self::n360($pp);
    }

    private static function findSukraSS(float $ah, float $ms): float {
        $mg = fmod((7022364 * $ah), self::YUGA) / self::YUGA * 360;
        $d2 = self::n360($mg - $ms);
        $d3 = self::kR($d2);
        $d4 = $d3 * self::RAD;
        $sinD4 = sin($d4);
        $sinVal = ((262 - $sinD4 * 2) / 360) * $sinD4;
        $cosVal = ((262 - $sinD4 * 2) / 360) * cos($d4);
        $d5 = ($d2 > 90 || $d2 < 270) ? 1 - $cosVal : 0;
        if ($d2 < 90 || $d2 > 270) $d5 = $cosVal + 1;
        $asinVal = (asin($sinVal / sqrt($sinVal * $sinVal + $d5 * $d5)) / 2 / M_PI) * 180;
        $d8 = $d2 > 180 ? $ms - $asinVal : $ms + $asinVal;
        $shA = (($ah / self::YUGA) * 0.535 * 360) + 79.65;
        $d9 = self::n360($shA - $d8);
        $d10 = self::kR($d9);
        $d11 = $d10 * self::RAD;
        $sinD11 = sin($d11);
        $abs1 = abs((asin(((12 - $sinD11) / 360) * $sinD11) / 2) * self::DEG);
        $tmp = $d9 > 180 ? $d8 - $abs1 : $d8 + $abs1;
        $d12 = self::n360($shA - $tmp);
        $d13 = self::kR($d12);
        $d14 = $d13 * self::RAD;
        $sinD14 = sin($d14);
        $sin3 = ((12 - $sinD14) / 360) * $sinD14;
        $abs2 = abs(asin($sin3) * self::DEG);
        $pp = $d12 > 180 ? $ms - $abs2 : $ms + $abs2;
        $d15 = self::n360($mg - $pp);
        $d16 = self::kR($d15);
        $d17 = $d16 * self::RAD;
        $sinD17 = sin($d17);
        $sin5 = ((262 - $sinD17 * 2) / 360) * $sinD17;
        $cos2 = ((262 - $sinD17 * 2) / 360) * cos($d17);
        $d5b = ($d15 > 90 || $d15 < 270) ? 1 - $cos2 : 0;
        if ($d15 < 90 || $d15 > 270) $d5b = $cos2 + 1;
        $sqrt = sqrt($sin5 * $sin5 + $d5b * $d5b);
        $asin2 = (asin($sin5 / $sqrt) / M_PI) * 180;
        if ($d15 <= 180) $pp += $asin2;
        if ($d15 > 180) $pp -= $asin2;
        return self::n360($pp);
    }

    private static function findRahuSS(float $ah, int $bsY): array {
        $d4 = floor(($ah - 1687850.2) / 4016);
        $d5 = fmod($ah - 1687850.2, 4016);
        if ($bsY < 2063) {
            $d2 = 212.83333333;
            $d = 27.6350901611;
        } else {
            $d2 = 212.8464233;
            $d = 27.646072227;
        }
        $d6 = 360 - (($d5 / 19) + ($d5 / 2700));
        $d8 = fmod($d2 * $d4, 360);
        $d9 = $d6 - $d8;
        $ra = $d9 < 0 ? $d9 + 360 : $d9;
        $ra += $d;
        if ($ra > 360) $ra -= 360;
        $ke = $ra + 180;
        if ($ke > 360) $ke -= 360;
        return ['ra' => self::n360($ra), 'ke' => self::n360($ke)];
    }

    private static function getB(float $bsYear): float {
        return (0.0163388 * ($bsYear - 2032)) + 23.7551194;
    }

    private static function get12Udayman(float $latDeg): array {
        $latRad = $latDeg * self::RAD;
        $tanLat = tan($latRad);
        $t1 = (int)floor($tanLat * 120);
        $t2 = (int)floor($tanLat * 96);
        $t3 = (int)floor($tanLat * 40);
        return [278 - $t1, 299 - $t2, 323 - $t3, 323 + $t3, 299 + $t2, 278 + $t1,
                278 + $t1, 299 + $t2, 323 + $t3, 323 - $t3, 299 - $t2, 278 - $t1];
    }

    private static function checkData(array &$eng, float $i, float $d, float $v0, float $v1, float $v2, float $v3, float $v4, float $v5): void {
        $i2 = $i;
        if ($i2 > 360) $i2 -= 360;
        $d8 = ($d < 0) ? $d + 360 : $d;
        if ($i2 <= 30) { $eng['Checkeast'] = (30 - $d8) * $v0 / 30; $eng['udayman'] = $v0; }
        elseif ($i2 <= 60) { $eng['Checkeast'] = (30 - $d8) * $v1 / 30; $eng['udayman'] = $v0; }
        elseif ($i2 <= 90) { $eng['Checkeast'] = (30 - $d8) * $v2 / 30; $eng['udayman'] = $v1; }
        elseif ($i2 <= 120) { $eng['Checkeast'] = (30 - $d8) * $v3 / 30; $eng['udayman'] = $v2; }
        elseif ($i2 <= 150) { $eng['Checkeast'] = (30 - $d8) * $v4 / 30; $eng['udayman'] = $v3; }
        elseif ($i2 <= 180) { $eng['Checkeast'] = (30 - $d8) * $v5 / 30; $eng['udayman'] = $v4; }
        elseif ($i2 <= 210) { $eng['Checkeast'] = (30 - $d8) * $v5 / 30; $eng['udayman'] = $v5; }
        elseif ($i2 <= 240) { $eng['Checkeast'] = (30 - $d8) * $v4 / 30; $eng['udayman'] = $v5; }
        elseif ($i2 <= 270) { $eng['Checkeast'] = (30 - $d8) * $v3 / 30; $eng['udayman'] = $v4; }
        elseif ($i2 <= 300) { $eng['Checkeast'] = (30 - $d8) * $v2 / 30; $eng['udayman'] = $v3; }
        elseif ($i2 <= 330) { $eng['Checkeast'] = (30 - $d8) * $v1 / 30; $eng['udayman'] = $v2; }
        else { $eng['Checkeast'] = (30 - $d8) * $v0 / 30; $eng['udayman'] = $v1; }
    }

    private static function myData(array &$eng, int $i, float $v0, float $v1, float $v2, float $v3, float $v4, float $v5): void {
        $i2 = ($i === 0) ? 12 : $i;
        if ($i2 > 12) $i2 -= 12;
        $mirror = [$v0, $v1, $v2, $v3, $v4, $v5, $v5, $v4, $v3, $v2, $v1, $v0];
        $rot = ($i2 + 1) % 12;
        $RD = [];
        $acc = 0;
        for ($k = 0; $k < 12; $k++) {
            $acc += $mirror[($rot + $k) % 12];
            $RD[] = $acc;
        }
        $eng['RD'] = $RD;
    }

    private static function findLagna(array $eng, float $d, float $d2): array {
        $R = $eng['RD'];
        $d3 = 0;
        $i = 0;
        if ($d >= $R[11]) { $d -= $R[11]; $d3 = $R[11] - $R[0]; $i = 12; }
        elseif ($d >= $R[10]) { $d3 = $R[11] - $R[10]; $d -= $R[10]; $i = 11; }
        elseif ($d >= $R[9]) { $d -= $R[9]; $d3 = $R[10] - $R[9]; $i = 10; }
        elseif ($d >= $R[8]) { $d3 = $R[9] - $R[8]; $d -= $R[8]; $i = 9; }
        elseif ($d >= $R[7]) { $d -= $R[7]; $d3 = $R[8] - $R[7]; $i = 8; }
        elseif ($d >= $R[6]) { $d3 = $R[7] - $R[6]; $d -= $R[6]; $i = 7; }
        elseif ($d >= $R[5]) { $d -= $R[5]; $d3 = $R[6] - $R[5]; $i = 6; }
        elseif ($d >= $R[4]) { $d3 = $R[5] - $R[4]; $d -= $R[4]; $i = 5; }
        elseif ($d >= $R[3]) { $d -= $R[3]; $d3 = $R[4] - $R[3]; $i = 4; }
        elseif ($d >= $R[2]) { $d3 = $R[3] - $R[2]; $d -= $R[2]; $i = 3; }
        elseif ($d >= $R[1]) { $d -= $R[1]; $d3 = $R[2] - $R[1]; $i = 2; }
        elseif ($d >= $R[0]) { $d3 = $R[1] - $R[0]; $d -= $R[0]; $i = 1; }
        else { $d3 = $R[0]; $i = 0; }
        $d17 = $i + $d2;
        if ($d17 >= 12) $d17 -= 12;
        $d18 = (floor($d17) + 1) * 30;
        $B = $eng['B'];
        $d19 = (($d18 + (($d * 30) / $d3)) - $B) / 30;
        $Crasi = fmod(fmod($d19, 12) + 12, 12);
        $CAnsa = ($Crasi - floor($Crasi)) * 30;
        return ['rashi' => $Crasi, 'degree' => $CAnsa];
    }

    private static function prathamLagna(float $pastsuryaNirayan, float $B, float $latDeg, float $eastkalMin): float {
        $latRad = $latDeg * self::RAD;
        $tanLat = tan($latRad);
        $t1 = (int)floor($tanLat * 120);
        $t2 = (int)floor($tanLat * 96);
        $t3 = (int)floor($tanLat * 40);
        $dv3 = 278 - $t1; $dv4 = 299 - $t2; $dv5 = 323 - $t3;
        $dv7 = 323 + $t3; $dv8 = 299 + $t2; $dv6 = 278 + $t1;
        $sayanaSurya = self::n360($pastsuryaNirayan + $B);
        $rasino = (int)floor($sayanaSurya / 30) % 12;
        $sunDegInRashi = fmod($sayanaSurya, 30);
        $ud12 = self::get12Udayman($latDeg);
        $udaymanCurr = $ud12[$rasino];
        $checkEast = (30 - $sunDegInRashi) * $udaymanCurr / 30;
        $eastkalPala = $eastkalMin * 2.5;
        $Crasi = 0;
        if ($eastkalPala > $checkEast) {
            $remaining = $eastkalPala - $checkEast;
            $nextRasi = ($rasino + 1) % 12;
            $foundRasi = $nextRasi;
            $foundPala = $remaining;
            for ($step = 0; $step < 12; $step++) {
                $r = ($nextRasi + $step) % 12;
                if ($foundPala <= $ud12[$r]) { $foundRasi = $r; break; }
                $foundPala -= $ud12[$r];
            }
            $ansha = ($foundPala * 30) / $ud12[$foundRasi];
            $sayanaLagna = $foundRasi * 30 + $ansha;
            $Crasi = self::n360($sayanaLagna - $B);
        } else {
            $degAdvance = ($eastkalPala * 30) / $udaymanCurr;
            $Crasi = self::n360($pastsuryaNirayan + $degAdvance);
        }
        return $Crasi;
    }

    private static function calcSunriseSS(float $ah, float $bsYear, float $latDeg, float $lonDeg, float $tzOffsetHr): array {
        $ah = floor($ah + $tzOffsetHr / 24 + 1e-6) - $tzOffsetHr / 24;
        $su = self::findSuryaPasta($ah);
        $ms = $su['madyam'];
        $ps = $su['pos'];
        $B = self::getB($bsYear);
        $sayanaSurya = self::n360($ps + $B);
        $madyamSayana = self::n360($ms + $B);
        $epsApprox = 23.44250556 - (($bsYear - 2032) * 0.00013004);
        $atanVal = rad2deg(atan(tan(deg2rad($sayanaSurya)) * cos(deg2rad($epsApprox))));
        if ($sayanaSurya < 90) $belantar = $atanVal - $madyamSayana;
        elseif ($sayanaSurya < 270) $belantar = ($atanVal - $madyamSayana) + 180;
        else $belantar = ($atanVal - $madyamSayana) + 360;
        if (abs($belantar) > 5) $belantar += 360;
        $decSin = sin(deg2rad($sayanaSurya)) * sin(deg2rad($epsApprox));
        $dec = asin($decSin);
        $latRad = deg2rad($latDeg);
        $cos90 = cos(deg2rad(90.5833));
        $num = ($cos90 - sin($dec) * sin($latRad)) * -1;
        $den = cos($dec) * cos($latRad);
        $charArg = $num / $den;
        if ($charArg > 1) $charArg = 1;
        if ($charArg < -1) $charArg = -1;
        $Char = asin($charArg) / (2 * M_PI);
        $dinman = (($Char * 60) + 15) * 2;
        $pdesval = $tzOffsetHr - $lonDeg / 15;
        $suryodayTime = (12 - ($dinman / 5)) + ($belantar / 15) + $pdesval;
        return ['riseHr' => $suryodayTime];
    }

    public function getBasicDetails(): array {
        if (!$this->birthDate) {
            return [
                'rashi' => 'कृपया जन्म मिति र समय प्रविष्ट गर्नुहोस्',
                'nakshatra' => '—', 'lagna' => '—',
                'date' => null, 'place' => $this->birthPlace,
            ];
        }
        $adDt = $this->birthDate;
        $y = (int)$adDt->format('Y');
        $m = (int)$adDt->format('m');
        $d = (int)$adDt->format('d');
        $h = (int)$adDt->format('H');
        $mn = (int)$adDt->format('i');
        $s = (int)$adDt->format('s');
        $inputHr = $h + $mn / 60 + $s / 3600;

        $jd = ($adDt->getTimestamp() / 86400) + 2440587.5;
        $ah = $jd - self::KALI_JD;
        $tzOffsetHr = 5.75;
        $bsY = $y + 57;
        if (($m < 4) || ($m == 4 && $d < 14)) $bsY--;

        $su = self::findSuryaPasta($ah);
        $mo = self::findChandraPasta($ah);
        $ms = $su['madyam'];
        $ma = self::findMangalSS($ah, $ms);
        $me = self::findBudhaSS($ah, $ms);
        $ju = self::findGuruSS($ah, $ms);
        $ve = self::findSukraSS($ah, $ms);
        $sa = self::findSaniSS($ah, $ms);
        $rk = self::findRahuSS($ah, $bsY);

        $B = self::getB($bsY);
        $sr = self::calcSunriseSS($ah, $bsY, $this->lat, $this->lon, $tzOffsetHr);
        $riseHr = $sr['riseHr'];
        $eastkalMin = ($inputHr - $riseHr) * 60;
        if ($eastkalMin < 0) $eastkalMin += 24 * 60;

        $lagDeg = self::prathamLagna($su['pos'], $B, $this->lat, $eastkalMin);
        $LAGNA_CORR = (11 * 60 + 45) / 3600;
        $lagDeg = self::n360($lagDeg - $LAGNA_CORR);

        $grahaCorr = ['su' => -(1*60+43)/3600, 'mo' => -(21*60+26)/3600, 'ma' => -(1*60+4)/3600, 'me' => -(2*60+27)/3600, 'ju' => -(0*60+1)/3600, 've' => -(2*60+8)/3600, 'sa' => -(5*60+9)/3600, 'ra' => (0*60+5)/3600, 'ke' => (0*60+5)/3600];
        $planets = [
            'su' => self::n360($su['pos'] + $grahaCorr['su']),
            'mo' => self::n360($mo['pos'] + $grahaCorr['mo']),
            'ma' => self::n360($ma + $grahaCorr['ma']),
            'me' => self::n360($me + $grahaCorr['me']),
            'ju' => self::n360($ju + $grahaCorr['ju']),
            've' => self::n360($ve + $grahaCorr['ve']),
            'sa' => self::n360($sa + $grahaCorr['sa']),
            'ra' => self::n360($rk['ra'] + $grahaCorr['ra']),
            'ke' => self::n360($rk['ke'] + $grahaCorr['ke']),
        ];

        $moPos = $planets['mo'];
        $nkS = 360 / 27;
        $moRashiIdx = (int)floor($moPos / 30) % 12;
        $moNakIdx = (int)floor($moPos / $nkS) % 27;
        $moNakPada = (int)floor(fmod($moPos, $nkS) / ($nkS / 4)) + 1;
        $moDegInRashi = fmod($moPos, 30);
        $lagRashiIdx = (int)floor($lagDeg / 30) % 12;
        $lagDegInRashi = fmod($lagDeg, 30);

        $planetList = [];
        foreach ($planets as $key => $deg) {
            $ri = (int)floor($deg / 30) % 12;
            $dInR = fmod($deg, 30);
            $rashiName = self::$rashiNames[$ri];
            $house = (($ri - $lagRashiIdx + 12) % 12) + 1;
            $planetList[$key] = [
                'name' => self::$grahaNames[$key],
                'deg' => round($deg, 2),
                'rashi' => $rashiName,
                'rashi_idx' => $ri,
                'deg_in_rashi' => round($dInR, 2),
                'house' => $house,
                'is_retro' => false,
            ];
        }

        $planetList['la'] = [
            'name' => 'लग्न',
            'deg' => round($lagDeg, 2),
            'rashi' => self::$rashiNames[$lagRashiIdx],
            'rashi_idx' => $lagRashiIdx,
            'deg_in_rashi' => round($lagDegInRashi, 2),
            'house' => 1,
            'is_retro' => false,
        ];

        $houses = [];
        for ($i = 0; $i < 12; $i++) {
            $hRashi = ($lagRashiIdx + $i) % 12;
            $houses[$i + 1] = [
                'rashi' => self::$rashiNames[$hRashi],
                'lord' => self::$rashiLords[$hRashi],
            ];
        }

        $maHouse = (int)floor(($planets['ma'] - $lagDeg) / 30) % 12 + 1;
        if ($maHouse < 1) $maHouse += 12;
        $mangalDosha = in_array($maHouse, [1, 4, 7, 8, 12]);

        $navRashi = (int)floor($moPos / 3.333333333) % 12;

        $nkS = 360 / 27;
        $yogIdx = (int)floor(self::n360($mo['pos'] + $su['pos']) / $nkS) % 27;

        return [
            'rashi' => self::$rashiNames[$moRashiIdx],
            'rashi_idx' => $moRashiIdx,
            'mo_deg' => round($moPos, 2),
            'nakshatra' => self::$nk[$moNakIdx],
            'nakshatra_pada' => $moNakPada,
            'nakshatra_lord' => self::$nl[$moNakIdx],
            'lagna' => self::$rashiNames[$lagRashiIdx],
            'lagna_idx' => $lagRashiIdx,
            'lagna_deg' => round($lagDeg, 2),
            'lagna_lord' => self::$rashiLords[$lagRashiIdx],
            'planets' => $planetList,
            'houses' => $houses,
            'mangal_dosha' => $mangalDosha,
            'mangal_house' => $maHouse,
            'navamsha_rashi' => self::$rashiNames[$navRashi],
            'date' => $adDt->format('Y-m-d H:i'),
            'place' => $this->birthPlace,
            'lat' => $this->lat,
            'lon' => $this->lon,
        ];
    }
}