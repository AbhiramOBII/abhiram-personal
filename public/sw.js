importScripts('https://storage.googleapis.com/workbox-cdn/releases/7.0.0/workbox-sw.js');

// ─── Cache Names ───
const CACHE_VERSION = 'dayos-v2';

// ─── Precache (install-time) ───
workbox.precaching.precacheAndRoute([
  { url: '/offline', revision: CACHE_VERSION },
  { url: '/icons/icon-192.png', revision: null },
  { url: '/icons/icon-512.png', revision: null },
]);

// ─── Runtime Caching Strategies ───

// Static assets — CSS, JS, fonts, images: CacheFirst
workbox.routing.registerRoute(
  ({request}) => ['style', 'script', 'font', 'image'].includes(request.destination),
  new workbox.strategies.CacheFirst({
    cacheName: 'dayos-static',
    plugins: [
      new workbox.expiration.ExpirationPlugin({
        maxEntries: 60,
        maxAgeSeconds: 30 * 24 * 60 * 60, // 30 days
      }),
    ],
  })
);

// Today dashboard: NetworkFirst with 3s timeout
workbox.routing.registerRoute(
  ({url}) => url.pathname === '/admin/today',
  new workbox.strategies.NetworkFirst({
    cacheName: 'dayos-pages',
    networkTimeoutSeconds: 3,
    plugins: [
      new workbox.expiration.ExpirationPlugin({
        maxEntries: 10,
        maxAgeSeconds: 7 * 24 * 60 * 60, // 7 days
      }),
    ],
  })
);

// API calls: NetworkOnly — never cache
workbox.routing.registerRoute(
  ({url}) => url.pathname.includes('/api/'),
  new workbox.strategies.NetworkOnly()
);

// All other admin pages: NetworkFirst with offline fallback
workbox.routing.registerRoute(
  ({url}) => url.pathname.startsWith('/admin'),
  new workbox.strategies.NetworkFirst({
    cacheName: 'dayos-pages',
    networkTimeoutSeconds: 3,
    plugins: [
      new workbox.expiration.ExpirationPlugin({
        maxEntries: 20,
        maxAgeSeconds: 7 * 24 * 60 * 60,
      }),
      new workbox.cacheableResponse.CacheableResponsePlugin({
        statuses: [0, 200],
      }),
    ],
  })
);

// ─── Offline Fallback ───
workbox.routing.setCatchHandler(async ({event}) => {
  if (event.request.destination === 'document') {
    return caches.match('/offline');
  }
  return Response.error();
});

// ─── Service Worker Lifecycle ───
self.addEventListener('install', () => self.skipWaiting());
self.addEventListener('activate', () => self.clients.claim());

// ─── Background Sync for Task Completion ───
self.addEventListener('sync', event => {
  if (event.tag === 'sync-tasks') {
    event.waitUntil(syncPendingTasks());
  }
});

async function syncPendingTasks() {
  const cache = await caches.open('dayos-offline-queue');
  const requests = await cache.keys();
  for (const request of requests) {
    try {
      const cached = await cache.match(request);
      const body = await cached.json();
      await fetch(request.url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(body),
      });
      await cache.delete(request);
    } catch (e) {
      // retry on next sync
    }
  }
}
