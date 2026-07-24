<?php

class Panchang {
    private const YUGA = 1577917828.0;
    private const KALI_JD = 588465.5;
    private const RAD = 0.017453292519943295;
    private const DEG = 57.29577951308232;
    private const MADHYAM_GATI = 0.9856026545889309;

    private static array $tithiNames = ['प्रतिपदा','द्वितीया','तृतीया','चतुर्थी','पञ्चमी','षष्ठी','सप्तमी','अष्टमी','नवमी','दशमी','एकादशी','द्वादशी','त्रयोदशी','चतुर्दशी','पूर्णिमा'];

    private static array $nk = ['अश्विनी','भरणी','कृत्तिका','रोहिणी','मृगशिरा','आद्रा','पुनर्वसु','पुष्य','आश्लेषा','मघा','पू.फाल्गुनी','उ.फाल्गुनी','हस्त','चित्रा','स्वाती','विशाखा','अनुराधा','ज्येष्ठा','मूल','पू.षाढा','उ.षाढा','श्रवण','धनिष्ठा','शतभिषा','पू.भाद्रपदा','उ.भाद्रपदा','रेवती'];

    private static array $nl = ['केतु','शुक्र','सूर्य','चन्द्र','मंगल','राहु','गुरु','शनि','बुध','केतु','शुक्र','सूर्य','चन्द्र','मंगल','राहु','गुरु','शनि','बुध','केतु','शुक्र','सूर्य','चन्द्र','मंगल','राहु','गुरु','शनि','बुध'];

    private static array $yg = ['विष्कुम्भ','प्रीति','आयुष्मान','सौभाग्य','शोभन','अतिगण्ड','सुकर्मा','धृति','शूल','गण्ड','वृद्धि','ध्रुव','व्याघात','हर्षण','वज्र','सिद्धि','व्यतीपात','वरीयान्','परिघ','शिव','सिद्ध','साध्य','शुभ','शुक्ल','ब्रह्म','ऐन्द्र','वैधृति'];

    private static array $kc = ['बव','बालव','कौलव','तैतिल','गर','वणिज','विष्टि'];

    private static array $ks = ['शकुनि','चतुष्पद','नाग','किंस्तुघ्न'];

    private static array $rashi = ['मेष','वृष','मिथुन','कर्कट','सिंह','कन्या','तुला','वृश्चिक','धनु','मकर','कुम्भ','मीन'];

    private static array $bm = ['बैशाख','जेठ','असार','श्रावण','भाद्र','आश्विन','कार्तिक','मंसिर','पौष','माघ','फाल्गुन','चैत्र'];

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
        $gati = self::MADHYAM_GATI;
        if ($d3 > 90 || $d3 < 270) {
            $gati = self::MADHYAM_GATI + ((($epi) * cos($d5) / 360) / cos($abs * self::RAD)) * self::MADHYAM_GATI;
        }
        if ($d3 < 90 || $d3 > 270) {
            $gati = self::MADHYAM_GATI - ((($epi) * cos($d5) / 360) / cos($abs * self::RAD)) * self::MADHYAM_GATI;
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

    private static function getB(float $bsYear): float {
        return (0.0163388 * ($bsYear - 2032)) + 23.7551194;
    }

    private static function ayanamshaLahiri(float $jd): float {
        $T = ($jd - 2451545.0) / 36525.0;
        return 23.85676 + 50.2904 * $T / 3600 * 100;
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
        if ($sayanaSurya < 90) {
            $belantar = $atanVal - $madyamSayana;
        } elseif ($sayanaSurya < 270) {
            $belantar = ($atanVal - $madyamSayana) + 180;
        } else {
            $belantar = ($atanVal - $madyamSayana) + 360;
        }
        if (abs($belantar) > 5) $belantar += 360;

        $decSin = sin(deg2rad($sayanaSurya)) * sin(deg2rad($epsApprox));
        $dec = asin($decSin);
        $latRad = deg2rad($latDeg);
        $cos90_5833 = cos(deg2rad(90.5833));
        $num = ($cos90_5833 - sin($dec) * sin($latRad)) * -1;
        $den = cos($dec) * cos($latRad);
        $charArg = $num / $den;
        if ($charArg > 1) $charArg = 1;
        if ($charArg < -1) $charArg = -1;
        $Char = asin($charArg) / (2 * M_PI);

        $dinman = (($Char * 60) + 15) * 2;
        $pdesval = $tzOffsetHr - $lonDeg / 15;
        $suryodayTime = (12 - ($dinman / 5)) + ($belantar / 15) + $pdesval;
        $suryastTime = ($dinman / 2.5) + $suryodayTime;

        return [
            'r' => self::hrToStr($suryodayTime),
            's' => self::hrToStr($suryastTime),
            'riseHr' => $suryodayTime,
            'setHr' => $suryastTime,
            'dinman' => $dinman,
            'dec' => rad2deg($dec),
            'Belantar' => $belantar,
            'Char' => $Char,
            'B' => $B,
            'belantarGh' => ($lonDeg - 75.767) / 15 * 2.5,
        ];
    }

    private static function hrToStr(float $h): string {
        if ($h < 0) $h += 24;
        if ($h >= 24) $h -= 24;
        $totalSec = (int)round($h * 3600);
        $hr = (int)floor($totalSec / 3600) % 24;
        $mn = (int)floor(($totalSec % 3600) / 60);
        $sc = $totalSec % 60;
        return sprintf('%02d:%02d:%02d', $hr, $mn, $sc);
    }

    private static function angaUnit(string $kind): float {
        if ($kind === 'nak' || $kind === 'yoga') return 360 / 27;
        if ($kind === 'karan') return 6;
        return 12;
    }

    private static function angaPos(string $kind, float $a): float {
        $m = self::findChandraPasta($a)['pos'];
        $su = self::findSuryaPasta($a)['pos'];
        if ($kind === 'nak') return self::n360($m);
        if ($kind === 'yoga') return self::n360($m + $su);
        return self::n360($m - $su);
    }

    private static function angaRate(string $kind, float $a): float {
        $mg = self::findChandraPasta($a)['gati'];
        $sg = self::findSuryaPasta($a)['gati'];
        if ($kind === 'nak') return $mg;
        if ($kind === 'yoga') return $mg + $sg;
        $r = $mg - $sg;
        return $r <= 0 ? $r + 360 : $r;
    }

    private static function angaWrap(float $d): float {
        while ($d > 180) $d -= 360;
        while ($d <= -180) $d += 360;
        return $d;
    }

    private static function angaEndGhTo(string $kind, float $ahRef, float $targetDeg): float {
        $p0 = self::angaPos($kind, $ahRef);
        $d = self::angaWrap($targetDeg - $p0);
        $ahEnd = $ahRef + $d / self::angaRate($kind, $ahRef);
        for ($i = 0; $i < 10; $i++) {
            $err = self::angaWrap($targetDeg - self::angaPos($kind, $ahEnd));
            $ahEnd += $err / self::angaRate($kind, $ahEnd);
            if (abs($err) < 1e-9) break;
        }
        return ($ahEnd - $ahRef) * 60;
    }

    private static function angaEndGh(string $kind, float $ahRef): float {
        $unit = self::angaUnit($kind);
        $base = floor(self::angaPos($kind, $ahRef) / $unit);
        return self::angaEndGhTo($kind, $ahRef, ($base + 1) * $unit);
    }

    private static function angaWalkFromSunrise(string $kind, float $ahSr, float $ahBirth): array {
        $unit = self::angaUnit($kind);
        $nUnit = (int)round(360 / $unit);
        $idx = (int)floor(self::angaPos($kind, $ahSr) / $unit) % $nUnit;
        $ah = $ahSr;
        $acc = 0.0;
        $steps = 0;
        for ($i = 0; $i < 60; $i++) {
            $g = self::angaEndGhTo($kind, $ah, ($idx + 1) * $unit);
            $ahEnd = $ah + $g / 60;
            if ($ahEnd > $ahBirth) return ['endGh' => $acc + $g, 'steps' => $steps];
            $acc += $g;
            $ah = $ahEnd;
            $steps++;
            $idx = ($idx + 1) % $nUnit;
        }
        return ['endGh' => $acc, 'steps' => $steps];
    }

    private static function panchRefAh(float $ahSunriseReal, float $lon): float {
        return $ahSunriseReal - (($lon - 75.767) / 360 + 4 / 1440);
    }

    private static function panchAhOffset(float $ahSunriseReal, float $lon): float {
        return $ahSunriseReal - self::panchRefAh($ahSunriseReal, $lon);
    }

    private static function computeAngas(float $ahSunriseReal, float $ahIshtaReal, float $lon): array {
        $off = self::panchAhOffset($ahSunriseReal, $lon);
        $ahRef = $ahSunriseReal - $off;
        $ahBel = $ahIshtaReal - $off;
        $su = self::findSuryaPasta($ahBel);
        $mo = self::findChandraPasta($ahBel);
        $nkS = 360 / 27;
        $moPos = self::n360($mo['pos']);
        $tDiffRaw = self::n360($mo['pos'] - $su['pos']);
        $nakPassed = fmod($moPos, $nkS);

        $Wt = self::angaWalkFromSunrise('tithi', $ahRef, $ahBel);
        $Wn = self::angaWalkFromSunrise('nak', $ahRef, $ahBel);
        $Wy = self::angaWalkFromSunrise('yoga', $ahRef, $ahBel);
        $Wk = self::angaWalkFromSunrise('karan', $ahRef, $ahBel);

        $tIdx = (int)floor($tDiffRaw / 12);
        $karIdx = (int)floor($tDiffRaw / 6);
        $nakIdx = (int)floor($moPos / $nkS) % 27;
        $yogIdx = (int)floor(self::n360($mo['pos'] + $su['pos']) / $nkS) % 27;

        return [
            'off' => $off, 'ahRef' => $ahRef, 'ahBel' => $ahBel,
            'su' => $su, 'mo' => $mo,
            'moPos' => $moPos, 'tDiff' => $tDiffRaw, 'tDiffRaw' => $tDiffRaw,
            'tIdx' => $tIdx, 'karIdx' => $karIdx, 'nakIdx' => $nakIdx, 'yogIdx' => $yogIdx,
            'tEndGh' => $Wt['endGh'], 'nakEndGh' => $Wn['endGh'],
            'yogEndGh' => $Wy['endGh'], 'karEndGh' => $Wk['endGh'],
            'nakPassed' => $nakPassed,
            'bhayatGh' => ($nakPassed / $mo['gati']) * 60,
            'bhabhogGh' => ($nkS / $mo['gati']) * 60,
        ];
    }

    private static function ghToClockTimeX(float $gh, float $riseHr, float $nextRiseHr): string {
        if (!is_finite($gh)) return '--';
        if ($gh < 0) return '(भइसक्यो)';
        $rh = is_finite($riseHr) ? $riseHr : 6;
        $nr = is_finite($nextRiseHr) ? $nextRiseHr : $rh;
        $t = $rh + $gh * 24 / 60;
        $totalSec = (int)round($t * 3600);
        $h = (int)floor($totalSec / 3600);
        $mn = (int)floor(($totalSec % 3600) / 60);
        $sc = $totalSec % 60;
        return sprintf('%02d:%02d:%02d', $h, $mn, $sc);
    }

    private static function tithiNm(int $i): array {
        $i = (($i % 30) + 30) % 30;
        $pk = $i < 15 ? 'शुक्ल' : 'कृष्ण';
        $t = ($i % 15) + 1;
        $nm = self::$tithiNames;
        if ($i === 29) $nm[14] = 'औंसी';
        return ['pk' => $pk, 't' => $nm[$t - 1]];
    }

    private static function getKaranaName(int $k): string {
        $k = (($k % 60) + 60) % 60;
        if ($k === 0) return self::$ks[3];
        if ($k === 57) return self::$ks[0];
        if ($k === 58) return self::$ks[1];
        if ($k === 59) return self::$ks[2];
        return self::$kc[($k - 1) % 7];
    }

    private static function adToBs(int $year, int $month, int $day): array {
        $bsData = self::getBsData();
        $baseAd = gmmktime(0, 0, 0, 4, 13, 1918);
        $targetAd = gmmktime(0, 0, 0, $month, $day, $year);
        $diffDays = (int)(($targetAd - $baseAd) / 86400);

        $bsYear = 1975;
        if ($diffDays >= 0) {
            while (true) {
                $yd = is_array($bsData[$bsYear] ?? null) ? array_sum($bsData[$bsYear]) : 365;
                if ($diffDays < $yd) break;
                $diffDays -= $yd;
                $bsYear++;
            }
        } else {
            while ($diffDays < 0) {
                $bsYear--;
                $yd = is_array($bsData[$bsYear] ?? null) ? array_sum($bsData[$bsYear]) : 365;
                $diffDays += $yd;
            }
        }

        $months = $bsData[$bsYear] ?? [31, 31, 32, 32, 31, 30, 30, 29, 30, 29, 30, 30];
        $bsMonth = 1;
        foreach ($months as $md) {
            if ($diffDays < $md) break;
            $diffDays -= $md;
            $bsMonth++;
        }
        return ['y' => $bsYear, 'm' => $bsMonth, 'd' => $diffDays + 1];
    }

    public static function bsToAd(int $bsYear, int $bsMonth, int $bsDay): array {
        $bsData = self::getBsData();
        $baseAd = gmmktime(0, 0, 0, 4, 13, 1918);
        $totalDays = 0;
        $y = 1975;
        while ($y < $bsYear) {
            $totalDays += is_array($bsData[$y] ?? null) ? array_sum($bsData[$y]) : 365;
            $y++;
        }
        $months = $bsData[$bsYear] ?? [31, 31, 32, 32, 31, 30, 30, 29, 30, 29, 30, 30];
        for ($m = 1; $m < $bsMonth; $m++) {
            $totalDays += $months[$m - 1] ?? 30;
        }
        $totalDays += $bsDay - 1;
        $secs = $baseAd + $totalDays * 86400;
        return ['y' => (int)gmdate('Y', $secs), 'm' => (int)gmdate('m', $secs), 'd' => (int)gmdate('d', $secs)];
    }

    public static function getBsData(): array {
        return [
            1900=>[31,31,31,32,31,31,29,30,30,29,30,30],1901=>[31,31,32,31,31,31,30,29,30,29,30,30],
            1902=>[31,31,32,32,31,30,30,29,30,29,30,30],1903=>[31,32,31,32,31,30,30,30,29,29,30,31],
            1904=>[31,31,31,32,31,31,29,30,30,29,30,30],1905=>[31,31,32,31,31,31,30,29,30,29,30,30],
            1906=>[31,32,31,32,31,30,30,29,30,29,30,30],1907=>[31,32,31,32,31,30,30,30,29,29,30,31],
            1908=>[31,31,31,32,31,31,30,29,30,29,30,30],1909=>[31,31,32,31,31,31,30,29,30,29,30,30],
            1910=>[31,32,31,32,31,30,30,30,29,29,30,30],1911=>[31,32,31,32,31,30,30,30,29,30,29,31],
            1912=>[31,31,31,32,31,31,30,29,30,29,30,30],1913=>[31,31,32,31,31,31,30,29,30,29,30,30],
            1914=>[31,32,31,32,31,30,30,30,29,29,30,30],1915=>[31,32,31,32,31,30,30,30,29,30,29,31],
            1916=>[31,31,32,31,31,31,30,29,30,29,30,30],1917=>[31,31,32,31,32,30,30,29,30,29,30,30],
            1918=>[31,32,31,32,31,30,30,30,29,29,30,31],1919=>[30,32,31,32,31,30,30,30,29,30,29,31],
            1920=>[31,31,32,31,31,31,30,29,30,29,30,30],1921=>[31,31,32,32,31,30,30,29,30,29,30,30],
            1922=>[31,32,31,32,31,30,30,30,29,29,30,31],1923=>[30,32,31,32,31,31,29,30,29,30,29,31],
            1924=>[31,31,32,31,31,31,30,29,30,29,30,30],1925=>[31,31,32,32,31,30,30,29,30,29,30,30],
            1926=>[31,32,31,32,31,30,30,30,29,29,30,31],1927=>[31,31,31,32,31,31,29,30,30,29,29,31],
            1928=>[31,31,32,31,31,31,30,29,30,29,30,30],1929=>[31,31,32,32,31,30,30,29,30,29,30,30],
            1930=>[31,32,31,32,31,30,30,30,29,29,30,31],1931=>[31,31,31,32,31,31,29,30,30,29,30,30],
            1932=>[31,31,32,31,31,31,30,29,30,29,30,30],1933=>[31,32,31,32,31,30,30,29,30,29,30,30],
            1934=>[31,32,31,32,31,30,30,30,29,29,30,31],1935=>[31,31,31,32,31,31,30,29,30,29,30,30],
            1936=>[31,31,32,31,31,31,30,29,30,29,30,30],1937=>[31,32,31,32,31,30,30,30,29,29,30,30],
            1938=>[31,32,31,32,31,30,30,30,29,30,29,31],1939=>[31,31,31,32,31,31,30,29,30,29,30,30],
            1940=>[31,31,32,31,31,31,30,29,30,29,30,30],1941=>[31,32,31,32,31,30,30,30,29,29,30,30],
            1942=>[31,32,31,32,31,30,30,30,29,30,29,31],1943=>[31,31,32,31,31,31,30,29,30,29,30,30],
            1944=>[31,31,32,31,31,31,30,29,30,29,30,30],1945=>[31,32,31,32,31,30,30,30,29,29,30,31],
            1946=>[30,32,31,32,31,30,30,30,29,30,29,31],1947=>[31,31,32,31,31,31,30,29,30,29,30,30],
            1948=>[31,31,32,32,31,30,30,29,30,29,30,30],1949=>[31,32,31,32,31,30,30,30,29,29,30,31],
            1950=>[30,32,31,32,31,31,29,30,29,30,29,31],1951=>[31,31,32,31,31,31,30,29,30,29,30,30],
            1952=>[31,31,32,32,31,30,30,29,30,29,30,30],1953=>[31,32,31,32,31,30,30,30,29,29,30,31],
            1954=>[31,31,31,32,31,31,29,30,30,29,29,31],1955=>[31,31,32,31,31,31,30,29,30,29,30,30],
            1956=>[31,31,32,32,31,30,30,29,30,29,30,30],1957=>[31,32,31,32,31,30,30,30,29,29,30,31],
            1958=>[31,31,31,32,31,31,29,30,30,29,30,30],1959=>[31,31,32,31,31,31,30,29,30,29,30,30],
            1960=>[31,32,31,32,31,30,30,29,30,29,30,30],1961=>[31,32,31,32,31,30,30,30,29,29,30,31],
            1962=>[31,31,31,32,31,31,30,29,30,29,30,30],1963=>[31,31,32,31,31,31,30,29,30,29,30,30],
            1964=>[31,32,31,32,31,30,30,30,29,29,30,30],1965=>[31,32,31,32,31,30,30,30,29,30,29,31],
            1966=>[31,31,31,32,31,31,30,29,30,29,30,30],1967=>[31,31,32,31,31,31,30,29,30,29,30,30],
            1968=>[31,32,31,32,31,30,30,30,29,29,30,30],1969=>[31,32,31,32,31,30,30,30,29,30,29,31],
            1970=>[31,31,31,32,31,31,30,29,30,29,30,30],1971=>[31,31,32,31,31,31,30,29,30,29,30,30],
            1972=>[31,32,31,32,31,30,30,30,29,29,30,31],1973=>[30,32,31,32,31,30,30,30,29,30,29,31],
            1974=>[31,31,32,31,31,31,30,29,30,29,30,30],1975=>[31,31,32,32,31,30,30,29,30,29,30,30],
            1976=>[31,32,31,32,31,30,30,30,29,29,30,31],1977=>[30,32,31,32,31,31,29,30,29,30,29,31],
            1978=>[31,31,32,31,31,31,30,29,30,29,30,30],1979=>[31,31,32,32,31,30,30,29,30,29,30,30],
            1980=>[31,32,31,32,31,30,30,30,29,29,30,31],1981=>[31,31,31,32,31,31,29,30,30,29,29,31],
            1982=>[31,31,32,31,31,31,30,29,30,29,30,30],1983=>[31,31,32,32,31,30,30,29,30,29,30,30],
            1984=>[31,32,31,32,31,30,30,30,29,29,30,31],1985=>[31,31,31,32,31,31,29,30,30,29,30,30],
            1986=>[31,31,32,31,31,31,30,29,30,29,30,30],1987=>[31,32,31,32,31,30,30,29,30,29,30,30],
            1988=>[31,32,31,32,31,30,30,30,29,29,30,31],1989=>[31,31,31,32,31,31,30,29,30,29,30,30],
            1990=>[31,31,32,31,31,31,30,29,30,29,30,30],1991=>[31,32,31,32,31,30,30,30,29,29,30,30],
            1992=>[31,32,31,32,31,30,30,30,29,30,29,31],1993=>[31,31,31,32,31,31,30,29,30,29,30,30],
            1994=>[31,31,32,31,31,31,30,29,30,29,30,30],1995=>[31,32,31,32,31,30,30,30,29,29,30,30],
            1996=>[31,32,31,32,31,30,30,30,29,30,29,31],1997=>[31,31,32,31,31,31,30,29,30,29,30,30],
            1998=>[31,31,32,31,31,31,30,29,30,29,30,30],1999=>[31,32,31,32,31,30,30,30,29,29,30,31],
            2000=>[30,32,31,32,31,30,30,30,29,30,29,31],2001=>[31,31,32,31,31,31,30,29,30,29,30,30],
            2002=>[31,31,32,32,31,30,30,29,30,29,30,30],2003=>[31,32,31,32,31,30,30,30,29,29,30,31],
            2004=>[30,32,31,32,31,31,29,30,29,30,29,31],2005=>[31,31,32,31,31,31,30,29,30,29,30,30],
            2006=>[31,31,32,32,31,30,30,29,30,29,30,30],2007=>[31,32,31,32,31,30,30,30,29,29,30,31],
            2008=>[31,31,31,32,31,31,29,30,30,29,29,31],2009=>[31,31,32,31,31,31,30,29,30,29,30,30],
            2010=>[31,31,32,32,31,30,30,29,30,29,30,30],2011=>[31,32,31,32,31,30,30,30,29,29,30,31],
            2012=>[31,31,31,32,31,31,29,30,30,29,30,30],2013=>[31,31,32,31,31,31,30,29,30,29,30,30],
            2014=>[31,32,31,32,31,30,30,29,30,29,30,30],2015=>[31,32,31,32,31,30,30,30,29,29,30,31],
            2016=>[31,31,31,32,31,31,30,29,30,29,30,30],2017=>[31,31,32,31,31,31,30,29,30,29,30,30],
            2018=>[31,32,31,32,31,30,30,30,29,29,30,30],2019=>[31,32,31,32,31,30,30,30,29,30,29,31],
            2020=>[31,31,31,32,31,31,30,29,30,29,30,30],2021=>[31,31,32,31,31,31,30,29,30,29,30,30],
            2022=>[31,32,31,32,31,30,30,30,29,29,30,30],2023=>[31,32,31,32,31,30,30,30,29,30,29,31],
            2024=>[31,31,31,32,31,31,30,29,30,29,30,30],2025=>[31,31,32,31,31,31,30,29,30,29,30,30],
            2026=>[31,32,31,32,31,30,30,30,29,29,30,31],2027=>[30,32,31,32,31,30,30,30,29,30,29,31],
            2028=>[31,31,32,31,31,31,30,29,30,29,30,30],2029=>[31,31,32,32,31,30,30,29,30,29,30,30],
            2030=>[31,32,31,32,31,30,30,30,29,29,30,31],2031=>[30,32,31,32,31,31,29,30,29,30,29,31],
            2032=>[31,31,32,31,31,31,30,29,30,29,30,30],2033=>[31,31,32,32,31,30,30,29,30,29,30,30],
            2034=>[31,32,31,32,31,30,30,30,29,29,30,31],2035=>[31,31,31,32,31,31,29,30,30,29,29,31],
            2036=>[31,31,32,31,31,31,30,29,30,29,30,30],2037=>[31,31,32,32,31,30,30,29,30,29,30,30],
            2038=>[31,32,31,32,31,30,30,30,29,29,30,31],2039=>[31,31,31,32,31,31,29,30,30,29,30,30],
            2040=>[31,31,32,31,31,31,30,29,30,29,30,30],2041=>[31,32,31,32,31,30,30,29,30,29,30,30],
            2042=>[31,32,31,32,31,30,30,30,29,29,30,31],2043=>[31,31,31,32,31,31,30,29,30,29,30,30],
            2044=>[31,31,32,31,31,31,30,29,30,29,30,30],2045=>[31,32,31,32,31,30,30,30,29,29,30,30],
            2046=>[31,32,31,32,31,30,30,30,29,30,29,31],2047=>[31,31,31,32,31,31,30,29,30,29,30,30],
            2048=>[31,31,32,31,31,31,30,29,30,29,30,30],2049=>[31,32,31,32,31,30,30,30,29,29,30,30],
            2050=>[31,32,31,32,31,30,30,30,29,30,29,31],2051=>[31,31,32,31,31,31,30,29,30,29,30,30],
            2052=>[31,31,32,31,31,31,30,29,30,29,30,30],2053=>[31,32,31,32,31,30,30,30,29,29,30,31],
            2054=>[30,32,31,32,31,30,30,30,29,30,29,31],2055=>[31,31,32,31,31,31,30,29,30,29,30,30],
            2056=>[31,31,32,32,31,30,30,29,30,29,30,30],2057=>[31,32,31,32,31,30,30,30,29,29,30,31],
            2058=>[30,32,31,32,31,31,29,30,29,30,29,31],2059=>[31,31,32,31,31,31,30,29,30,29,30,30],
            2060=>[31,31,32,32,31,30,30,29,30,29,30,30],2061=>[31,32,31,32,31,30,30,30,29,29,30,31],
            2062=>[31,31,31,32,31,31,29,30,30,29,29,31],2063=>[31,31,32,31,31,31,30,29,30,29,30,30],
            2064=>[31,31,32,32,31,30,30,29,30,29,30,30],2065=>[31,32,31,32,31,30,30,30,29,29,30,31],
            2066=>[31,31,31,32,31,31,29,30,30,29,30,30],2067=>[31,31,32,31,31,31,30,29,30,29,30,30],
            2068=>[31,32,31,32,31,30,30,29,30,29,30,30],2069=>[31,32,31,32,31,30,30,30,29,29,30,31],
            2070=>[31,31,31,32,31,31,30,29,30,29,30,30],2071=>[31,31,32,31,31,31,30,29,30,29,30,30],
            2072=>[31,32,31,32,31,30,30,30,29,29,30,30],2073=>[31,32,31,32,31,30,30,30,29,30,29,31],
            2074=>[31,31,31,32,31,31,30,29,30,29,30,30],2075=>[31,31,32,31,31,31,30,29,30,29,30,30],
            2076=>[31,32,31,32,31,30,30,30,29,29,30,30],2077=>[31,32,31,32,31,30,30,30,29,30,29,31],
            2078=>[31,31,32,31,31,31,30,29,30,29,30,30],2079=>[31,31,32,31,31,31,30,29,30,29,30,30],
            2080=>[31,32,31,32,31,30,30,30,29,29,30,31],2081=>[30,32,31,32,31,30,30,30,29,30,29,31],
            2082=>[31,31,32,31,31,31,30,29,30,29,30,30],2083=>[31,31,32,32,31,30,30,29,30,29,30,30],
            2084=>[31,32,31,32,31,30,30,30,29,29,30,31],2085=>[30,32,31,32,31,31,29,30,29,30,29,31],
            2086=>[31,31,32,31,31,31,30,29,30,29,30,30],2087=>[31,31,32,32,31,30,30,29,30,29,30,30],
            2088=>[31,32,31,32,31,30,30,30,29,29,30,31],2089=>[31,31,31,32,31,31,29,30,30,29,29,31],
            2090=>[31,31,32,31,31,31,30,29,30,29,30,30],2091=>[31,31,32,32,31,30,30,29,30,29,30,30],
            2092=>[31,32,31,32,31,30,30,30,29,29,30,31],2093=>[31,31,31,32,31,31,29,30,30,29,30,30],
            2094=>[31,31,32,31,31,31,30,29,30,29,30,30],2095=>[31,32,31,32,31,30,30,29,30,29,30,30],
            2096=>[31,32,31,32,31,30,30,30,29,29,30,31],2097=>[31,31,31,32,31,31,30,29,30,29,30,30],
            2098=>[31,31,32,31,31,31,30,29,30,29,30,30],2099=>[31,32,31,32,31,30,30,30,29,29,30,30],
            2100=>[31,32,31,32,31,30,30,30,29,30,29,31],
        ];
    }

    public static function getForDate(string $date, float $lat = 27.7172, float $lon = 85.3240): array {
        $tz = new \DateTimeZone('UTC');
        $adDt = new \DateTime($date, $tz);
        $y = (int)$adDt->format('Y');
        $m = (int)$adDt->format('m');
        $d = (int)$adDt->format('d');

        $bs = self::adToBs($y, $m, $d);

        $jd = ($adDt->getTimestamp() / 86400) + 2440587.5;
        $ah = $jd - self::KALI_JD;

        $tzOffsetHr = round($lon / 15 * 2) / 2;

        $sr = self::calcSunriseSS($ah, $bs['y'], $lat, $lon, $tzOffsetHr);
        $riseHr = $sr['riseHr'];

        $dtSr = clone $adDt;
        $dtSr->setTime(0, 0, 0);
        $jdSr = ($dtSr->getTimestamp() / 86400) + 2440587.5 + $riseHr / 24;
        $ahSr = $jdSr - self::KALI_JD;

        $nextDt = clone $adDt;
        $nextDt->modify('+1 day');
        $srNext = self::calcSunriseSS($ah + 1, $bs['y'], $lat, $lon, $tzOffsetHr);
        $nextRiseHr = $srNext['riseHr'];

        $A = self::computeAngas($ahSr, $ahSr, $lon);
        $tEndClock = self::ghToClockTimeX($A['tEndGh'], $riseHr, $nextRiseHr);
        $nakEndClock = self::ghToClockTimeX($A['nakEndGh'], $riseHr, $nextRiseHr);
        $yogEndClock = self::ghToClockTimeX($A['yogEndGh'], $riseHr, $nextRiseHr);
        $karEndClock = self::ghToClockTimeX($A['karEndGh'], $riseHr, $nextRiseHr);

        $tn = self::tithiNm($A['tIdx']);
        $tithiFull = ($A['tDiff'] < 180 ? 'शुक्ल' : 'कृष्ण') . ' ' . $tn['t'];
        $nakName = self::$nk[$A['nakIdx'] % 27];
        $nakLord = self::$nl[$A['nakIdx'] % 27];
        $yogaName = self::$yg[$A['yogIdx'] % 27];
        $karanaName = self::getKaranaName($A['karIdx']);

        $suRashi = self::$rashi[(int)floor(self::n360($A['su']['pos']) / 30)];
        $moRashi = self::$rashi[(int)floor(self::n360($A['mo']['pos']) / 30)];

        $dow = (int)$adDt->format('w');
        $dowNp = ['आइतवार', 'सोमवार', 'मंगलवार', 'बुधवार', 'बिहीवार', 'शुक्रवार', 'शनिवार'];

        return [
            'date' => $date,
            'bs_date' => $bs,
            'tithi' => $tithiFull,
            'tithi_end' => $tEndClock,
            'nakshatra' => $nakName,
            'nakshatra_lord' => $nakLord,
            'nakshatra_end' => $nakEndClock,
            'yoga' => $yogaName,
            'yoga_end' => $yogEndClock,
            'karana' => $karanaName,
            'karana_end' => $karEndClock,
            'moon_rashi' => $moRashi,
            'sun_rashi' => $suRashi,
            'sunrise' => $sr['r'],
            'sunset' => $sr['s'],
            'day_of_week' => $dowNp[$dow],
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
