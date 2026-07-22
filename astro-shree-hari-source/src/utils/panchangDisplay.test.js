import test from 'node:test';
import assert from 'node:assert/strict';
import { buildForecastEntries, buildPanchangRows } from './panchangDisplay.js';

test('buildPanchangRows keeps only available Panchang values in display order', () => {
  assert.deepEqual(buildPanchangRows({ tithi: 'अष्टमी', sunrise: '05:21:31', sunset: '18:58:53', nakshatra: 'स्वाती', yoga: 'साध्य', karana: 'बव' }), [
    ['तिथि', 'अष्टमी'], ['सूर्योदय', '05:21:31'], ['सूर्यास्त', '18:58:53'], ['नक्षत्र', 'स्वाती'], ['करण', 'बव'], ['योग', 'साध्य'],
  ]);
});
test('buildForecastEntries selects the existing day and night horoscope fields', () => {
  const item = { zodiac_ne: 'मेष', moon_interpretation: 'दिनको फल', remedy_tips: 'रात्रीको उपाय', infeasible_transit_moon: 'सावधानी' };
  assert.deepEqual(buildForecastEntries([item], 'day'), [{ title: 'मेष', body: 'दिनको फल', note: '' }]);
  assert.deepEqual(buildForecastEntries([item], 'night'), [{ title: 'मेष', body: 'रात्रीको उपाय', note: 'सावधानी' }]);
});