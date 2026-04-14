// Two-loop price ticker: real data fetch (3s) + cosmetic jitter (2s).
//
// Loop A fetches /api/prices every 3s. When a server value actually changes,
// the cell flashes green/red and shows a direction arrow.
//
// Loop B applies tiny random jitter every 2s so numbers visibly tick between
// real upstream updates (~1 min cadence). Jitter never touches data-price
// (the real value) — it only changes textContent.

const FETCH_INTERVAL = 3000;
const JITTER_INTERVAL = 2000;
const MAX_CONSECUTIVE_ERRORS = 5;
const BACKOFF_INTERVAL = 5000;

// Maps keyed by data-pkey
const lastValues = new Map();   // real server values
const directions = new Map();   // last real-change direction

let fetchTimer = null;
let jitterTimer = null;
let errorCount = 0;

// ── Jitter amounts per price category ────────────────────────────────

function jitterAmount(pkey) {
    if (pkey.startsWith('intl-xa'))          return 1;       // USD intl metals ±$1
    if (pkey.startsWith('platinum-intl'))    return 1;
    if (pkey.startsWith('palladium-intl'))   return 1;
    if (pkey.startsWith('platinum-local'))   return 5;       // PKR local metals ±5
    if (pkey.startsWith('palladium-local'))  return 5;
    if (pkey.startsWith('gold-'))            return 5;       // PKR gold ±5 Rs
    if (pkey.startsWith('silver-'))          return 5;       // PKR silver ±5 Rs
    if (pkey.startsWith('crude-oil'))        return 0.25;    // USD crude ±$0.25
    if (pkey.startsWith('psx-'))             return 10;      // PSX index ±10
    if (pkey.startsWith('currency-'))        return 0.10;    // PKR currencies ±0.10
    return 1;
}

// ── Formatting helpers ───────────────────────────────────────────────

function formatPrice(pkey, value) {
    if (pkey.startsWith('intl-xa') || pkey.startsWith('platinum-intl') || pkey.startsWith('palladium-intl')) {
        return '$' + commas(value, 2);
    }
    if (pkey.startsWith('crude-oil')) {
        return '$' + commas(value, 2);
    }
    if (pkey.startsWith('psx-')) {
        return commas(value, 2);
    }
    if (pkey.startsWith('currency-')) {
        return 'Rs ' + commas(value, 2);
    }
    // PKR metals (gold, silver, platinum-local, palladium-local)
    return 'Rs ' + commas(value, 0);
}

function commas(n, decimals) {
    return Number(n).toLocaleString('en-US', {
        minimumFractionDigits: decimals,
        maximumFractionDigits: decimals,
    });
}

// ── Resolve a data-pkey to its value inside the API JSON ─────────────

function resolvePrice(data, pkey) {
    // intl-xau-bid, intl-xau-ask, intl-xag-bid, intl-xag-ask
    let m = pkey.match(/^intl-(xau|xag)-(bid|ask)$/);
    if (m) {
        const metal = m[1] === 'xau' ? 'gold' : 'silver';
        return data.quotes?.[metal]?.[m[2]] ?? null;
    }

    // gold-24k-tola-buy, gold-rawa-10_tola-sell, etc.
    m = pkey.match(/^gold-(\w+)-(\w+)-(buy|sell)$/);
    if (m) return data.gold?.[m[1]]?.[m[2]]?.[m[3]] ?? null;

    // silver-kg-buy, silver-tola-sell, etc.
    m = pkey.match(/^silver-(\w+)-(buy|sell)$/);
    if (m) return data.silver?.[m[1]]?.[m[2]] ?? null;

    // currency-usd_pkr-buy, currency-gbp_pkr-sell, etc.
    m = pkey.match(/^currency-(\w+)-(buy|sell)$/);
    if (m) {
        const rate = data.currencies?.[m[1]];
        if (rate == null) return null;
        if (typeof rate === 'object') return rate[m[2]] ?? null;
        return Number(rate);  // scalar (same for buy/sell)
    }

    // platinum-intl, platinum-local
    m = pkey.match(/^platinum-(intl|local)$/);
    if (m) return data.platinum?.[m[1] === 'intl' ? 'international' : 'local'] ?? null;

    // palladium-intl, palladium-local
    m = pkey.match(/^palladium-(intl|local)$/);
    if (m) return data.palladium?.[m[1] === 'intl' ? 'international' : 'local'] ?? null;

    if (pkey === 'crude-oil') return data.crude_oil ?? null;
    if (pkey === 'psx-index') return data.psx?.index ?? null;

    return null;
}

// ── Seed initial values from DOM ─────────────────────────────────────

function seedInitialValues() {
    document.querySelectorAll('[data-pkey][data-price]').forEach((el) => {
        const key = el.dataset.pkey;
        const val = parseFloat(el.dataset.price);
        if (Number.isFinite(val) && !lastValues.has(key)) {
            lastValues.set(key, val);
        }
    });
}

// ── Loop A: real data fetch ──────────────────────────────────────────

async function fetchPrices() {
    try {
        const res = await fetch('/api/prices', { cache: 'no-store' });
        if (!res.ok) throw new Error(res.status);
        const data = await res.json();
        errorCount = 0;

        document.querySelectorAll('[data-pkey][data-price]').forEach((el) => {
            const pkey = el.dataset.pkey;
            const newVal = resolvePrice(data, pkey);
            if (newVal == null || !Number.isFinite(Number(newVal))) return;

            const numVal = Number(newVal);
            const prev = lastValues.get(pkey);

            // Real value changed → flash + arrow
            if (prev !== undefined && prev !== numVal) {
                const dir = numVal > prev ? 'up' : 'down';
                el.classList.remove('flash-up', 'flash-down');
                void el.offsetWidth;
                el.classList.add(dir === 'up' ? 'flash-up' : 'flash-down');
                directions.set(pkey, dir);
            }

            // Update stored real value + DOM attribute + displayed text
            lastValues.set(pkey, numVal);
            el.dataset.price = numVal;
            el.textContent = formatPrice(pkey, numVal);

            // Re-apply sticky direction
            applyDirection(el, pkey);
        });
    } catch {
        errorCount++;
    }

    // Schedule next fetch (back off on repeated errors)
    const delay = errorCount >= MAX_CONSECUTIVE_ERRORS ? BACKOFF_INTERVAL : FETCH_INTERVAL;
    fetchTimer = setTimeout(fetchPrices, delay);
}

// ── Loop B: cosmetic jitter ──────────────────────────────────────────

// Pairs that must stay coordinated (low side key suffix → high side key suffix).
// E.g. "intl-xau-bid" pairs with "intl-xau-ask"; "gold-24k-tola-buy" pairs with
// "gold-24k-tola-sell". We jitter the low side first, then ensure the high side
// stays above it.

function pairKey(pkey) {
    // bid/ask pairs: strip the suffix to get a shared group key
    if (pkey.endsWith('-bid')) return { group: pkey.slice(0, -4), side: 'low' };
    if (pkey.endsWith('-ask')) return { group: pkey.slice(0, -4), side: 'high' };
    // buy/sell pairs
    if (pkey.endsWith('-buy')) return { group: pkey.slice(0, -4), side: 'low' };
    if (pkey.endsWith('-sell')) return { group: pkey.slice(0, -4), side: 'high' };
    return { group: pkey, side: 'solo' };
}

// Shared offsets per group per tick so paired elements use the same random seed
const groupOffsets = new Map();

function applyJitter() {
    groupOffsets.clear();

    document.querySelectorAll('[data-pkey][data-price]').forEach((el) => {
        const pkey = el.dataset.pkey;
        const base = lastValues.get(pkey);
        if (base == null) return;

        const range = jitterAmount(pkey);
        const { group, side } = pairKey(pkey);

        let offset;
        if (side === 'solo') {
            offset = (Math.random() * 2 - 1) * range;
        } else {
            // Generate one offset per group, reuse for both sides
            if (!groupOffsets.has(group)) {
                groupOffsets.set(group, (Math.random() * 2 - 1) * range);
            }
            offset = groupOffsets.get(group);
        }

        const jittered = base + offset;
        el.textContent = formatPrice(pkey, jittered);

        // Cosmetic direction class based on jitter sign
        const dir = offset >= 0 ? 'up' : 'down';
        el.classList.remove('dir-up', 'dir-down');
        el.classList.add(dir === 'up' ? 'dir-up' : 'dir-down');
    });
}

// ── Direction helper ─────────────────────────────────────────────────

function applyDirection(el, pkey) {
    const dir = directions.get(pkey);
    el.classList.remove('dir-up', 'dir-down');
    if (dir === 'up') el.classList.add('dir-up');
    else if (dir === 'down') el.classList.add('dir-down');
}

// ── Visibility API: pause when tab hidden ────────────────────────────

function startLoops() {
    if (!fetchTimer) fetchPrices();
    if (!jitterTimer) jitterTimer = setInterval(applyJitter, JITTER_INTERVAL);
}

function stopLoops() {
    if (fetchTimer) { clearTimeout(fetchTimer); fetchTimer = null; }
    if (jitterTimer) { clearInterval(jitterTimer); jitterTimer = null; }
}

document.addEventListener('visibilitychange', () => {
    if (document.hidden) {
        stopLoops();
    } else {
        startLoops();
    }
});

// ── Init ─────────────────────────────────────────────────────────────

document.addEventListener('livewire:init', () => {
    seedInitialValues();
    startLoops();

    // After Livewire morphs (e.g. gold tab switch), re-seed new elements
    Livewire.hook('commit', ({ succeed }) => {
        succeed(() => {
            seedInitialValues();
        });
    });
});
