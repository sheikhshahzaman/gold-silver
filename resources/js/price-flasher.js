// Price flash animation driver.
//
// For every element carrying data-pkey + data-price, we remember the last
// numeric value. When a subsequent Livewire update lands and the value has
// changed, we apply `.flash-up` or `.flash-down` so the cell briefly flashes
// green (price went up) or red (price went down).
//
// The matching is done by `data-pkey` rather than DOM identity so Livewire's
// morph strategy (which may swap nodes) doesn't break the diff.

const lastValues = new Map();
// Sticky direction memory: remembers the last direction a price moved so we
// can keep ▲/▼ visible between ticks. Livewire morph strips class names we
// add, so we re-apply them on every hook invocation.
const directions = new Map();

function flashChanged(root = document) {
    const nodes = root.querySelectorAll
        ? root.querySelectorAll('[data-pkey][data-price]')
        : [];

    nodes.forEach((el) => {
        const key = el.dataset.pkey;
        const next = parseFloat(el.dataset.price);
        if (!Number.isFinite(next)) return;

        const prev = lastValues.get(key);
        if (prev !== undefined && prev !== next) {
            const dir = next > prev ? 'up' : 'down';
            el.classList.remove('flash-up', 'flash-down');
            // Force a reflow so the animation restarts if the same class
            // is re-added on back-to-back ticks.
            void el.offsetWidth;
            el.classList.add(dir === 'up' ? 'flash-up' : 'flash-down');
            directions.set(key, dir);
        }
        lastValues.set(key, next);

        // Re-apply the sticky direction arrow class. Livewire morph will
        // strip these on each commit, so we re-set them on every flashChanged
        // call (which runs on morph.updated + commit.succeed).
        const dir = directions.get(key);
        el.classList.remove('dir-up', 'dir-down');
        if (dir === 'up') el.classList.add('dir-up');
        else if (dir === 'down') el.classList.add('dir-down');
    });
}

document.addEventListener('livewire:init', () => {
    // Seed the cache with whatever prices the initial render shows.
    flashChanged();

    // Fine-grained: fired per-element as Livewire morphs DOM attributes.
    Livewire.hook('morph.updated', ({ el }) => flashChanged(el));

    // Belt-and-braces: after every successful commit (full-page morph,
    // initial mount, route revisit), re-scan the whole document.
    Livewire.hook('commit', ({ succeed }) => {
        succeed(() => flashChanged());
    });
});
