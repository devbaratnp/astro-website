const FALLBACK = 'उपलब्ध छैन';

const fields = [
  ['tithi', 'तिथि'],
  ['nakshatra', 'नक्षत्र'],
  ['yoga', 'योग'],
  ['karana', 'करण'],
  ['sunrise', 'सूर्योदय'],
  ['sunset', 'सूर्यास्त'],
];

function displayValue(value, isTime) {
  if (value === null || value === undefined || value === '') return FALLBACK;
  return isTime ? String(value).slice(0, 5) : String(value);
}

export function buildHomePanchangItems(panchang) {
  return fields.map(([key, label]) => ({
    key,
    label,
    value: displayValue(panchang?.[key], key === 'sunrise' || key === 'sunset'),
  }));
}
