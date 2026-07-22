import test from 'node:test';
import assert from 'node:assert/strict';
import { buildForecastEntries, buildPanchangRows } from './panchangDisplay.js';

test('buildPanchangRows keeps only available Panchang values in display order', () => {
  assert.deepEqual(buildPanchangRows({ tithi: 'x', sunrise: '05:21:31', sunset: '18:58:53', nakshatra: 'y', yoga: 'z', karana: 'w' }), [['\u0924\u093f\u0925\u093f', 'x'], ['\u0938\u0942\u0930\u094d\u092f\u094b\u0926\u092f', '05:21:31'], ['\u0938\u0942\u0930\u094d\u092f\u093e\u0938\u094d\u0924', '18:58:53'], ['\u0928\u0915\u094d\u0937\u0924\u094d\u0930', 'y'], ['\u0915\u0930\u0923', 'w'], ['\u092f\u094b\u0917', 'z']]);
});

test('buildForecastEntries selects the existing day and night horoscope fields', () => {
  const item = { zodiac_ne: 'Aries', moon_interpretation: 'day', remedy_tips: 'night', infeasible_transit_moon: 'caution' };
  assert.deepEqual(buildForecastEntries([item], 'day'), [{ title: 'Aries', body: 'day', note: '' }]);
  assert.deepEqual(buildForecastEntries([item], 'night'), [{ title: 'Aries', body: 'night', note: 'caution' }]);
});