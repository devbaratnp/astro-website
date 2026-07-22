export function buildPanchangRows(panchang = {}) {
  return [
    ['तिथि', panchang.tithi], ['सूर्योदय', panchang.sunrise], ['सूर्यास्त', panchang.sunset], ['नक्षत्र', panchang.nakshatra], ['करण', panchang.karana], ['योग', panchang.yoga],
  ].filter(([, value]) => value);
}
export function buildForecastEntries(items = [], period) {
  return items
    .map((item) => ({
      title: item.zodiac_ne,
      body: period === 'night' ? item.remedy_tips : item.moon_interpretation,
      note: period === 'night' ? item.infeasible_transit_moon : '',
    }))
    .filter((entry) => entry.title && entry.body);
}
