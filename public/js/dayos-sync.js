/**
 * DayOS Local Sync — IndexedDB cache + background refresh
 * Uses localforage (must be loaded before this script)
 */
const DayOSSync = (() => {
    const STORE_KEY   = 'dayos_today';
    const FETCHED_KEY = 'dayos_fetched_at';
    const STALE_MS    = 5 * 60 * 1000; // 5 minutes = stale

    let _onUpdate = null; // callback when fresh data arrives

    // --- Public API ---
    async function init(onUpdate) {
        _onUpdate = onUpdate;

        // 1. Load from cache immediately — render fast
        const cached = await load();
        if (cached) _onUpdate(cached, { source: 'cache' });

        // 2. Fetch fresh data in background
        await fetchAndStore();

        // 3. Refresh when tab becomes visible again
        document.addEventListener('visibilitychange', () => {
            if (document.visibilityState === 'visible') {
                isStale().then(stale => { if (stale) fetchAndStore(); });
            }
        });

        // 4. Refresh when window regains focus (covers PWA open from home screen)
        window.addEventListener('focus', () => {
            isStale().then(stale => { if (stale) fetchAndStore(); });
        });

        // 5. Keep session alive every 10 min
        setInterval(pingSession, 10 * 60 * 1000);
    }

    async function fetchAndStore() {
        try {
            const res = await fetch('/admin/api/dashboard/today-data', {
                headers: { 'Accept': 'application/json' },
                credentials: 'same-origin',
            });

            // Session expired — redirect to login
            if (res.status === 401 || res.status === 302) {
                window.location.href = '/admin/login';
                return;
            }

            if (!res.ok) return; // silent fail — use cache

            const data = await res.json();
            await store(data);
            if (_onUpdate) _onUpdate(data, { source: 'network' });
        } catch (e) {
            // Offline — serve from cache silently
            console.warn('[DayOSSync] Fetch failed, using cache.', e.message);
        }
    }

    async function store(data) {
        await localforage.setItem(STORE_KEY, data);
        await localforage.setItem(FETCHED_KEY, Date.now());
    }

    async function load() {
        return await localforage.getItem(STORE_KEY);
    }

    async function clear() {
        await localforage.removeItem(STORE_KEY);
        await localforage.removeItem(FETCHED_KEY);
    }

    async function isStale() {
        const ts = await localforage.getItem(FETCHED_KEY);
        if (!ts) return true;
        return (Date.now() - ts) > STALE_MS;
    }

    async function pingSession() {
        try {
            await fetch('/admin/api/ping', { credentials: 'same-origin' });
        } catch (e) { /* silent */ }
    }

    // Force a manual refresh (e.g., after task completion)
    async function refresh() {
        await fetchAndStore();
    }

    return { init, refresh, clear, load };
})();
