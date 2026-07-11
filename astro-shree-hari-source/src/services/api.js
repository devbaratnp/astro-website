const API_BASE = '/backend/api';

async function request(endpoint, options = {}) {
  const url = `${API_BASE}/${endpoint}`;
  const res = await fetch(url, {
    headers: { 'Content-Type': 'application/json' },
    ...options,
  });
  const data = await res.json().catch(() => ({ success: false, message: `Server error (${res.status})` }));
  if (!data.success) throw new Error(data.message);
  return data;
}

export function getAvailableSlots(date) {
  return request(`appointments.php?date=${date}`);
}

export function createAppointment(payload) {
  return request('appointments.php', {
    method: 'POST',
    body: JSON.stringify(payload),
  });
}

export function sendContactMessage(payload) {
  return request('contact.php', {
    method: 'POST',
    body: JSON.stringify(payload),
  });
}

export function getKundali(payload) {
  return request('kundali.php', {
    method: 'POST',
    body: JSON.stringify(payload),
  });
}

export function getPoojaServices() {
  return request('pooja.php');
}

export function bookPooja(payload) {
  return request('pooja.php', {
    method: 'POST',
    body: JSON.stringify(payload),
  });
}

export function getPanchang(date) {
  return request(`panchang.php?date=${date || new Date().toISOString().split('T')[0]}`);
}

export function submitPayment(payload) {
  return request('payments.php', {
    method: 'POST',
    body: JSON.stringify(payload),
  });
}

export function getHoroscope(date) { return request(`horoscope.php?date=${date}`); }
