const CACHE='astroshreehari-v7';
const SHELL=['/','/about','/services','/appointment','/contact','/kundali','/pooja','/panchang','/payment','/manifest.json','/assets/shreehari-logo.webp','/assets/shreehari-icon-192.png'];
self.addEventListener('install',e=>e.waitUntil(caches.open(CACHE).then(c=>c.addAll(SHELL)).then(()=>self.skipWaiting())));
self.addEventListener('activate',e=>e.waitUntil(caches.keys().then(keys=>Promise.all(keys.filter(k=>k!==CACHE).map(k=>caches.delete(k)))).then(()=>self.clients.claim())));
self.addEventListener('fetch',e=>{const u=new URL(e.request.url);if(e.request.method!=='GET'||u.pathname.startsWith('/backend/api/')||u.origin!==location.origin)return;e.respondWith(caches.match(e.request).then(hit=>hit||fetch(e.request).then(r=>{const copy=r.clone();caches.open(CACHE).then(c=>c.put(e.request,copy));return r}).catch(()=>caches.match('/'))))});
self.addEventListener('push',e=>{let d={};try{d=e.data?.json()||{}}catch{d={body:e.data?.text()}}e.waitUntil(self.registration.showNotification(d.title||'Astro Shree Hari',{body:d.body||'',icon:'/assets/shreehari-icon-192.png',badge:'/assets/shreehari-icon-192.png',data:{url:d.url||'/panchang'}}))});
self.addEventListener('notificationclick',e=>{e.notification.close();e.waitUntil(clients.openWindow(e.notification.data.url))});
