const FALLBACK = 'उपलब्ध छैन';

const fields = [
  ['tithi', 'तिथि'],
  ['nakshatra', 'नक्षत्र'],
  ['moon_rashi', 'चन्द्र राशि'],
  ['sunrise', 'सूर्योदय'],
  ['sunset', 'सूर्यास्त'],
];

export const bsMonths = ['बैशाख','जेठ','असार','श्रावण','भाद्र','आश्विन','कार्तिक','मंसिर','पौष','माघ','फाल्गुन','चैत्र'];

export function getBsDateString(bs) {
  if (!bs) return FALLBACK;
  return `${bs.y} ${bsMonths[bs.m - 1]} ${bs.d}`;
}

function toN(num) {
  if (num === undefined || num === null) return '';
  return String(num).replace(/[0-9]/g, d => '०१२३४५६७८९'[d]);
}

function displayValue(value, isTime) {
  if (value === null || value === undefined || value === '') return FALLBACK;
  return isTime ? String(value).slice(0, 5) : String(value);
}

export function buildHomePanchangItems(panchang) {
  return fields
    .map(([key, label]) => ({
      key,
      label,
      value: displayValue(panchang?.[key], key === 'sunrise' || key === 'sunset'),
    }))
    .filter((item) => item.value !== FALLBACK);
}
