// UI niceties: floating draggable cart button, scroll-reveal animations,
// and count-up stat numbers. All of it respects prefers-reduced-motion.

const reducedMotion = () => window.matchMedia('(prefers-reduced-motion: reduce)').matches;

// ── Floating cart button (Alpine component) ──────────────────────────
//
// The wrapper element is fixed-position. Default spot is bottom-right
// (set via inline style); once the user drags it, we switch to explicit
// left/top coordinates, snap to the nearest horizontal edge on release,
// and remember the position in localStorage. A short pointer movement
// (< 8px) counts as a tap and opens the cart.

document.addEventListener('alpine:init', () => {
    window.Alpine.data('floatingCart', () => ({
        x: null,
        y: null,
        dragging: false,
        moved: false,
        startX: 0,
        startY: 0,
        grabX: 0,
        grabY: 0,

        init() {
            try {
                const saved = JSON.parse(localStorage.getItem('floating-cart-pos') || 'null');
                if (saved && Number.isFinite(saved.x) && Number.isFinite(saved.y)) {
                    this.x = saved.x;
                    this.y = saved.y;
                    this.$nextTick(() => this.clamp());
                }
            } catch { /* corrupted storage — fall back to default spot */ }

            window.addEventListener('resize', () => this.clamp());
        },

        get posStyle() {
            // Alpine's string :style binding replaces the whole attribute,
            // so the default spot must be returned here too.
            if (this.x === null) return 'right:14px; bottom:96px;';
            return `left:${this.x}px; top:${this.y}px; right:auto; bottom:auto;`;
        },

        startDrag(e) {
            // Only main button / single touch
            if (e.button !== undefined && e.button !== 0) return;

            const rect = this.$el.getBoundingClientRect();
            this.dragging = true;
            this.moved = false;
            this.startX = e.clientX;
            this.startY = e.clientY;
            this.grabX = e.clientX - rect.left;
            this.grabY = e.clientY - rect.top;

            const move = (ev) => {
                if (!this.dragging) return;
                if (!this.moved && Math.abs(ev.clientX - this.startX) + Math.abs(ev.clientY - this.startY) > 8) {
                    this.moved = true;
                }
                if (this.moved) {
                    ev.preventDefault();
                    this.x = ev.clientX - this.grabX;
                    this.y = ev.clientY - this.grabY;
                }
            };

            const up = () => {
                window.removeEventListener('pointermove', move);
                window.removeEventListener('pointerup', up);
                window.removeEventListener('pointercancel', up);
                const wasDrag = this.moved;
                this.dragging = false;
                if (wasDrag) {
                    this.snapToEdge();
                } else {
                    window.location.href = '/cart';
                }
            };

            window.addEventListener('pointermove', move, { passive: false });
            window.addEventListener('pointerup', up);
            window.addEventListener('pointercancel', up);
        },

        snapToEdge() {
            const el = this.$el;
            const margin = 14;
            const w = el.offsetWidth;
            const h = el.offsetHeight;
            const centerX = this.x + w / 2;

            this.x = centerX < window.innerWidth / 2 ? margin : window.innerWidth - w - margin;
            // Keep clear of the sticky header (top) and bottom nav (bottom).
            this.y = Math.min(Math.max(this.y, 72), window.innerHeight - h - 92);

            try {
                localStorage.setItem('floating-cart-pos', JSON.stringify({ x: this.x, y: this.y }));
            } catch { /* private mode — position just won't persist */ }
        },

        clamp() {
            if (this.x === null) return;
            const el = this.$el;
            this.x = Math.min(Math.max(this.x, 14), window.innerWidth - el.offsetWidth - 14);
            this.y = Math.min(Math.max(this.y, 72), window.innerHeight - el.offsetHeight - 92);
        },
    }));
});

// ── Scroll reveal ────────────────────────────────────────────────────
//
// Elements with [data-reveal] start hidden (CSS) and slide/fade in when
// they enter the viewport. Optional data-reveal-delay="150" staggers
// items. Re-runs after Livewire commits so morphed-in elements animate.

let revealObserver = null;

function initReveal() {
    const els = document.querySelectorAll('[data-reveal]:not(.revealed)');
    if (!els.length) return;

    if (!('IntersectionObserver' in window) || reducedMotion()) {
        els.forEach((el) => el.classList.add('revealed'));
        return;
    }

    if (!revealObserver) {
        revealObserver = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) return;
                const el = entry.target;
                el.style.transitionDelay = `${el.dataset.revealDelay || 0}ms`;
                el.classList.add('revealed');
                revealObserver.unobserve(el);
            });
        }, { threshold: 0.12, rootMargin: '0px 0px -36px 0px' });
    }

    els.forEach((el) => revealObserver.observe(el));
}

// ── Count-up stats ───────────────────────────────────────────────────
//
// Elements with [data-countup] animate their first number from 0 when
// scrolled into view, preserving any prefix/suffix ("50,000+", "4.8★").

function animateCountUp(el) {
    const raw = el.textContent;
    const match = raw.match(/([\d.,]+)/);
    if (!match) return;

    const numStr = match[1];
    const target = parseFloat(numStr.replace(/,/g, ''));
    if (!Number.isFinite(target)) return;

    const decimals = (numStr.split('.')[1] || '').length;
    const useCommas = numStr.includes(',');
    const prefix = raw.slice(0, match.index);
    const suffix = raw.slice(match.index + numStr.length);
    const duration = 1400;
    const start = performance.now();

    const tick = (now) => {
        const p = Math.min((now - start) / duration, 1);
        const eased = 1 - Math.pow(1 - p, 3);
        let value = (target * eased).toFixed(decimals);
        if (useCommas) {
            value = Number(value).toLocaleString('en-US', {
                minimumFractionDigits: decimals,
                maximumFractionDigits: decimals,
            });
        }
        el.textContent = prefix + value + suffix;
        if (p < 1) requestAnimationFrame(tick);
    };

    requestAnimationFrame(tick);
}

function initCountUp() {
    const els = document.querySelectorAll('[data-countup]:not(.counted)');
    if (!els.length) return;

    if (!('IntersectionObserver' in window) || reducedMotion()) {
        els.forEach((el) => el.classList.add('counted'));
        return;
    }

    const io = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (!entry.isIntersecting) return;
            entry.target.classList.add('counted');
            animateCountUp(entry.target);
            io.unobserve(entry.target);
        });
    }, { threshold: 0.4 });

    els.forEach((el) => io.observe(el));
}

// ── Boot ─────────────────────────────────────────────────────────────

function initAll() {
    initReveal();
    initCountUp();
}

document.addEventListener('DOMContentLoaded', initAll);
document.addEventListener('livewire:init', () => {
    initAll();
    window.Livewire.hook('commit', ({ succeed }) => {
        succeed(() => setTimeout(initAll));
    });
});
