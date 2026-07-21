import { API_BASE } from '../../config';

const API = API_BASE;

export async function api(path, options = {}) {
  const response = await fetch(`${API}/${path}`, {
    credentials: 'same-origin',
    headers: { 'Content-Type': 'application/json' },
    ...options,
  });
  const body = await response.json().catch(() => ({
    success: false,
    message: `Server error (${response.status})`,
  }));
  if (!body.success) throw new Error(body.message || 'Request failed');
  return body.data;
}
