import test from 'node:test';
import assert from 'node:assert/strict';

import { ad2bs } from './bsDate.js';

test('ad2bs converts an AD date after consuming the mutable day offset', () => {
  assert.deepEqual(ad2bs(new Date(2026, 6, 17)), { y: 2083, m: 4, d: 1 });
});
