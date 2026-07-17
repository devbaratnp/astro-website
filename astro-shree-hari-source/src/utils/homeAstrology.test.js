import test from 'node:test';
import assert from 'node:assert/strict';
import { buildHomePanchangItems } from './homeAstrology.js';

test('buildHomePanchangItems preserves complete Panchang values', () => {
  const items = buildHomePanchangItems({
    tithi: 'पञ्चमी',
    nakshatra: 'स्वाती',
    yoga: 'सिद्ध',
    karana: 'बव',
    sunrise: '05:19:00',
    sunset: '19:00:00',
  });

  assert.deepEqual(items.map(({ value }) => value), [
    'पञ्चमी', 'स्वाती', 'सिद्ध', 'बव', '05:19', '19:00',
  ]);
});

test('buildHomePanchangItems supplies honest fallback values for partial data', () => {
  const items = buildHomePanchangItems({
    tithi: 'पञ्चमी',
    nakshatra: 'स्वाती',
    yoga: null,
    karana: '',
    sunrise: '05:19:00',
    sunset: null,
  });

  assert.equal(items.length, 6);
  assert.deepEqual(items.map(({ value }) => value), [
    'पञ्चमी', 'स्वाती', 'उपलब्ध छैन', 'उपलब्ध छैन', '05:19', 'उपलब्ध छैन',
  ]);
});

test('buildHomePanchangItems handles an unavailable Panchang response', () => {
  const items = buildHomePanchangItems(null);

  assert.equal(items.length, 6);
  assert.ok(items.every(({ value }) => value === 'उपलब्ध छैन'));
});
