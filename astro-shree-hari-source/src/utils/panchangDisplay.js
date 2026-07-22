export function buildPanchangRows(panchang = {}) {
  return [
    ['\u0924\u093f\u0925\u093f', panchang.tithi],
    ['\u0938\u0942\u0930\u094d\u092f\u094b\u0926\u092f', panchang.sunrise],
    ['\u0938\u0942\u0930\u094d\u092f\u093e\u0938\u094d\u0924', panchang.sunset],
    ['\u0928\u0915\u094d\u0937\u0924\u094d\u0930', panchang.nakshatra],
    ['\u0915\u0930\u0923', panchang.karana],
    ['\u092f\u094b\u0917', panchang.yoga],
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