const CACHE_NAME = 'astroshreehari-v1';
const urlsToCache = [
    '/',
    '/index.html',
    '/about.html',
    '/services.html',
    '/appointment.html',
    '/contact.html',
    '/kundali.html',
    '/panchang.html',
    '/assets/styles.css',
    '/assets/script.js',
    '/assets/logo.svg',
    '/assets/favicon.svg',
];

self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE_NAME).then(cache => cache.addAll(urlsToCache))
    );
});

self.addEventListener('fetch', event => {
    event.respondWith(
        caches.match(event.request).then(response => {
            return response || fetch(event.request).then(fetchResponse => {
                if (event.request.url.includes('/backend/api/')) {
                    const clone = fetchResponse.clone();
                    caches.open(CACHE_NAME + '-api').then(cache => {
                        cache.put(event.request, clone);
                    });
                }
                return fetchResponse;
            });
        })
    );
});

self.addEventListener('push', event => {
    const data = event.data.json();
    const options = {
        body: data.body,
        icon: '/assets/favicon.svg',
        badge: '/assets/favicon.svg',
        vibrate: [200, 100, 200],
        data: { url: data.url || '/' },
    };
    event.waitUntil(
        self.registration.showNotification(data.title, options)
    );
});

self.addEventListener('notificationclick', event => {
    event.notification.close();
    event.waitUntil(clients.openWindow(event.notification.data.url));
});
