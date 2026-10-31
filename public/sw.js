'use strict';

/*
 * AfyaLink service worker.
 *
 * Goal: the site still opens on a slow or lost connection, without ever hiding that
 * a page may be out of date. Health information that is wrong is worse than none.
 *
 * Rules:
 *  - Pages are fetched from the network first. A saved copy is used only when the
 *    network fails, is very slow, or the server is down.
 *  - A page shown from a saved copy always carries a visible notice with the save date.
 *  - Only GET requests to this site are handled. Everything else goes straight to the network.
 *  - Maintainer pages and the report form are never stored.
 *  - A page showing the "report sent" thank-you notice is never stored.
 *
 * Bump VERSION whenever this file's behaviour changes, so old caches are removed.
 */

const VERSION = 'v1';
const PAGES = 'afyalink-pages-' + VERSION;
const STATIC = 'afyalink-static-' + VERSION;

const OFFLINE_URL = '/offline.html';
const PRECACHE = ['/manifest.webmanifest', '/icons/icon-192.png', '/images/logo.png'];

const MAX_SAVED_PAGES = 40;
const SLOW_NETWORK_MS = 4000;

// Paths that must never be stored.
const NEVER_STORE_PATHS = ['/admin', '/report'];

// Markup of the thank-you notice shown after a report is sent. Pages containing it are not stored.
const NO_STORE_MARKER = 'class="notice success"';

self.addEventListener('install', (event) => {
    event.waitUntil((async () => {
        const staticCache = await caches.open(STATIC);
        await staticCache.add(OFFLINE_URL);
        // Best effort: a missing optional file must not stop the worker from installing.
        await Promise.all(PRECACHE.map((url) => staticCache.add(url).catch(() => undefined)));
        await self.skipWaiting();
    })());
});

self.addEventListener('activate', (event) => {
    event.waitUntil((async () => {
        const keep = [PAGES, STATIC];
        const names = await caches.keys();
        await Promise.all(names.filter((name) => !keep.includes(name)).map((name) => caches.delete(name)));
        await self.clients.claim();
    })());
});

self.addEventListener('fetch', (event) => {
    const request = event.request;

    if (request.method !== 'GET') {
        return;
    }

    const url = new URL(request.url);

    if (url.origin !== self.location.origin) {
        return;
    }

    if (isNeverStored(url.pathname)) {
        return;
    }

    if (request.mode === 'navigate') {
        event.respondWith(handleNavigation(event));
        return;
    }

    if (isStaticAsset(url.pathname)) {
        event.respondWith(cacheFirst(request));
    }
});

function isNeverStored(pathname) {
    return pathname === '/sw.js' || NEVER_STORE_PATHS.some((path) => pathname.startsWith(path) || pathname.includes(path + '/') || pathname.endsWith(path));
}

function isStaticAsset(pathname) {
    return pathname.startsWith('/images/') || pathname.startsWith('/icons/') || pathname === '/manifest.webmanifest';
}

async function cacheFirst(request) {
    const cache = await caches.open(STATIC);
    const hit = await cache.match(request);

    if (hit) {
        return hit;
    }

    try {
        const response = await fetch(request);

        if (response.ok) {
            await cache.put(request, response.clone());
        }

        return response;
    } catch (error) {
        return Response.error();
    }
}

async function handleNavigation(event) {
    const request = event.request;
    const network = fetch(request);
    const timeout = new Promise((resolve) => setTimeout(() => resolve('timeout'), SLOW_NETWORK_MS));

    let first;

    try {
        first = await Promise.race([network, timeout]);
    } catch (error) {
        return savedOrOffline(request);
    }

    if (first !== 'timeout') {
        return settle(event, first);
    }

    // The network is slow. Use a saved copy if there is one, and refresh it in the background.
    const saved = await savedPage(request);

    if (saved) {
        event.waitUntil(network.then((response) => storePage(request, response.clone())).catch(() => undefined));

        return saved;
    }

    try {
        return await settle(event, await network);
    } catch (error) {
        return offlineResponse();
    }
}

async function settle(event, response) {
    // The server is failing: a saved copy, clearly marked, is better than an error page.
    if (response.status >= 500) {
        const saved = await savedPage(event.request);

        return saved || response;
    }

    if (response.ok) {
        event.waitUntil(storePage(event.request, response.clone()));
    }

    return response;
}

async function storePage(request, response) {
    const type = response.headers.get('content-type') || '';

    if (response.status !== 200 || response.type !== 'basic' || !type.includes('text/html')) {
        return;
    }

    const html = await response.clone().text();

    if (html.includes(NO_STORE_MARKER)) {
        return;
    }

    const cache = await caches.open(PAGES);
    await cache.put(request, response);
    await trim(cache, MAX_SAVED_PAGES);
}

async function trim(cache, max) {
    const keys = await cache.keys();

    for (let i = 0; i < keys.length - max; i++) {
        await cache.delete(keys[i]);
    }
}

async function savedPage(request) {
    const cache = await caches.open(PAGES);
    const response = await cache.match(request);

    return response ? withSavedNotice(response) : undefined;
}

async function savedOrOffline(request) {
    return (await savedPage(request)) || offlineResponse();
}

async function offlineResponse() {
    const cache = await caches.open(STATIC);
    const response = await cache.match(OFFLINE_URL);

    return response || new Response('You are offline.', { status: 503, headers: { 'Content-Type': 'text/plain; charset=utf-8' } });
}

async function withSavedNotice(response) {
    const html = await response.text();
    const notice = savedNotice(response.headers.get('date'));
    const body = html.includes('<main') ? html.replace('<main', () => notice + '<main') : notice + html;

    const headers = new Headers(response.headers);
    headers.set('Content-Type', 'text/html; charset=utf-8');
    // The saved body is already decoded, so these headers would now be wrong.
    headers.delete('Content-Encoding');
    headers.delete('Content-Length');
    headers.delete('Transfer-Encoding');

    return new Response(body, { status: 200, statusText: 'OK', headers: headers });
}

function savedNotice(dateHeader) {
    let when = 'an earlier visit';

    try {
        if (dateHeader) {
            when = new Date(dateHeader).toLocaleString('en-GB', { dateStyle: 'medium', timeStyle: 'short' });
        }
    } catch (error) {
        // Keep the generic wording.
    }

    return '<div role="status" style="padding:.6rem 1rem;text-align:center;font-size:.9rem;font-weight:600;'
        + 'background:var(--alert-bg);color:var(--alert-text);border-bottom:1px solid var(--alert-border);">'
        + 'You are viewing a copy saved on ' + when + '. It may be out of date. '
        + 'Call the facility to confirm before you travel.</div>';
}
