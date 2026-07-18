const YUGA = 1577917828.0;
const KALI_JD = 588465.5;
const RAD = Math.PI / 180;
const DEG = 180 / Math.PI;

function N360(d) { d %= 360; return d < 0 ? d + 360 : d; }

function kR(d) {
  d = N360(d);
  if (d >= 90 && d < 180) return 180 - d;
  if (d >= 180 && d < 270) return d - 180;
  if (d >= 270) return 360 - d;
  return d;
}

export const nakshatras = [
  'अश्विनी', 'भरणी', 'कृत्तिका', 'रोहिणी', 'मृगशिरा', 'आर्द्रा',
  'पुनर्वसु', 'पुष्य', 'अश्लेषा', 'मघा', 'पूर्वाफाल्गुनी', 'उत्तराफाल्गुनी',
  'हस्त', 'चित्रा', 'स्वाती', 'विशाखा', 'अनुराधा', 'ज्येष्ठा',
  'मूल', 'पूर्वाषाढा', 'उत्तराषाढा', 'श्रवण', 'धनिष्ठा', 'शतभिषा',
  'पूर्वभाद्रपद', 'उत्तरभाद्रपद', 'रेवती',
];

export const tithis = [
  'प्रतिपदा', 'द्वितीया', 'तृतीया', 'चतुर्थी', 'पञ्चमी', 'षष्ठी',
  'सप्तमी', 'अष्टमी', 'नवमी', 'दशमी', 'एकादशी', 'द्वादशी',
  'त्रयोदशी', 'चतुर्दशी', 'पूर्णिमा', 'प्रतिपदा', 'द्वितीया', 'तृतीया',
  'चतुर्थी', 'पञ्चमी', 'षष्ठी', 'सप्तमी', 'अष्टमी', 'नवमी',
  'दशमी', 'एकादशी', 'द्वादशी', 'त्रयोदशी', 'चतुर्दशी', 'अमावास्या',
];

export const rashis = [
  'मेष', 'वृष', 'मिथुन', 'कर्कट', 'सिंह', 'कन्या',
  'तुला', 'वृश्चिक', 'धनु', 'मकर', 'कुम्भ', 'मीन',
];

function findSun(ah) {
  const ms = ((4320000 * ah) % YUGA / YUGA) * 360;
  const apogee = ((ah / YUGA) * 0.387 * 360) + 77.13;
  const d3 = N360(apogee - ms);
  const d4 = kR(d3);
  const sinD5 = Math.sin(d4 * RAD);
  const epi = (14 - sinD5 / 3) / 360;
  const abs = Math.abs(Math.asin(epi * Math.sin(d3 * RAD)) * DEG);
  const madyamgati = 360 / YUGA * 4320000;
  const madyamKgati = 360 / YUGA * 4320000 / 360;
  let gati;
  if (d3 > 90 && d3 < 270) {
    gati = madyamgati + (((14 - sinD5 / 3) * Math.cos(d4 * RAD) / 360) / Math.cos(abs * RAD)) * madyamKgati;
  } else {
    gati = madyamgati - (((14 - sinD5 / 3) * Math.cos(d4 * RAD) / 360) / Math.cos(abs * RAD)) * madyamKgati;
  }
  const pos = d3 > 180 ? ms - abs : ms + abs;
  return { pos: N360(pos), gati, mean: ms };
}

function findMoon(ah) {
  const ms = ((57753336 * ah) % YUGA / YUGA) * 360;
  const apRaw = ((ah * 488199) % YUGA / YUGA) * 360 + 90;
  const apogee = N360(apRaw);
  const d4 = N360(apogee - ms);
  const d5 = kR(d4);
  const sinD6 = Math.sin(d5 * RAD);
  const epi = (32 - sinD6 / 3) / 360;
  const abs = Math.abs(Math.asin(epi * Math.sin(d4 * RAD)) * DEG);
  const pos = d4 > 180 ? ms - abs : ms + abs;
  const abs2 = Math.abs(((32 - sinD6 / 3) * Math.cos(d5 * RAD) / 360) / Math.cos(abs * RAD) * 13.17635214652);
  const gati = d4 < 90 || d4 > 270 ? 13.17635214652 - abs2 : abs2 + 13.17635214652;
  return { pos: N360(pos), gati, mean: ms };
}

export function getPanchangForDate(date) {
  const jd = (date.getTime() / 86400000) + 2440587.5 - (date.getTimezoneOffset() / 1440);
  const ah = jd - KALI_JD;
  const su = findSun(ah);
  const mo = findMoon(ah);
  const nkS = 360 / 27;
  const tDiff = N360(mo.pos - su.pos);
  const tithiIndex = Math.floor(tDiff / 12);
  const nakIndex = Math.floor(mo.pos / nkS) % 27;
  const yogaIndex = Math.floor(N360(mo.pos + su.pos) / nkS) % 27;
  const karanaIndex = Math.floor(tDiff / 6) % 60;
  const moonRashiIndex = Math.floor(mo.pos / 30) % 12;
  const paksha = tDiff < 180 ? 'शुक्ल' : 'कृष्ण';

  return {
    tithi: tithis[tithiIndex % 30],
    tithiIndex: tithiIndex % 30,
    nakshatra: nakshatras[nakIndex],
    nakshatraIndex: nakIndex,
    yoga: yogaIndex,
    karana: karanaIndex,
    moonRashi: rashis[moonRashiIndex],
    moonRashiIndex,
    paksha,
    sunPos: su.pos,
    moonPos: mo.pos,
    tithiEnd: (12 - (tDiff % 12)) / (mo.gati - su.gati) * 24,
  };
}
