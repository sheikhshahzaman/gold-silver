/**
 * Site motion layer.
 *
 * Loads on every page. Handles:
 *   - Smooth scrolling (Lenis, loaded via CDN inside this file)
 *   - Scroll-reveal animations driven by `data-reveal` attributes
 *   - Cursor-follow ("magnetic") buttons via `data-magnetic`
 *   - Mouse-tilt cards via `data-tilt`
 *   - Animated counters via `data-counter`
 *   - Hero 3D gold-bar parallax/rotation
 *
 * All effects respect prefers-reduced-motion.
 */

const reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

/* ─────────────────────────────────────────────────────────────────────────
 * 1. Smooth scrolling via Lenis (loaded from CDN; only ~4KB gzipped)
 *    Falls back to native scroll if the CDN is unreachable.
 * ─────────────────────────────────────────────────────────────────────── */
function loadLenis() {
    if (reduced) return;
    if (window.__lenisLoaded) return;
    window.__lenisLoaded = true;

    const s = document.createElement('script');
    s.src = 'https://cdn.jsdelivr.net/npm/lenis@1.1.13/dist/lenis.min.js';
    s.async = true;
    s.onload = () => {
        if (!window.Lenis) return;
        const lenis = new window.Lenis({
            duration: 1.2,
            easing: (t) => Math.min(1, 1.001 - Math.pow(2, -10 * t)), // gentle ease-out
            smoothWheel: true,
        });
        function raf(time) { lenis.raf(time); requestAnimationFrame(raf); }
        requestAnimationFrame(raf);
        window.__lenis = lenis;
    };
    document.head.appendChild(s);
}

/* ─────────────────────────────────────────────────────────────────────────
 * 2. Scroll-reveal: any element with [data-reveal] animates in once it
 *    enters the viewport. Variants via [data-reveal="up|fade|scale|left|right"].
 *    Stagger children via [data-reveal-stagger] on the parent.
 * ─────────────────────────────────────────────────────────────────────── */
function initRevealAnimations() {
    if (reduced) {
        document.querySelectorAll('[data-reveal]').forEach((el) => el.classList.add('reveal-in'));
        return;
    }

    const io = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) return;
                const el = entry.target;
                const stagger = el.dataset.revealStagger;
                if (stagger) {
                    Array.from(el.children).forEach((child, i) => {
                        child.style.transitionDelay = `${i * parseInt(stagger, 10)}ms`;
                        child.classList.add('reveal-in');
                    });
                } else {
                    el.classList.add('reveal-in');
                }
                io.unobserve(el);
            });
        },
        { threshold: 0.12, rootMargin: '0px 0px -8% 0px' },
    );

    document.querySelectorAll('[data-reveal]').forEach((el) => io.observe(el));
}

/* ─────────────────────────────────────────────────────────────────────────
 * 3. Magnetic buttons: button gently follows the cursor when nearby.
 * ─────────────────────────────────────────────────────────────────────── */
function initMagneticButtons() {
    if (reduced) return;

    document.querySelectorAll('[data-magnetic]').forEach((btn) => {
        const strength = parseFloat(btn.dataset.magnetic) || 0.25;
        btn.addEventListener('mousemove', (e) => {
            const rect = btn.getBoundingClientRect();
            const x = e.clientX - rect.left - rect.width / 2;
            const y = e.clientY - rect.top - rect.height / 2;
            btn.style.transform = `translate(${x * strength}px, ${y * strength}px)`;
        });
        btn.addEventListener('mouseleave', () => {
            btn.style.transform = 'translate(0, 0)';
        });
    });
}

/* ─────────────────────────────────────────────────────────────────────────
 * 4. Tilt cards: 3D rotation that follows the cursor over the card.
 * ─────────────────────────────────────────────────────────────────────── */
function initTiltCards() {
    if (reduced) return;

    document.querySelectorAll('[data-tilt]').forEach((card) => {
        const max = parseFloat(card.dataset.tilt) || 8; // degrees
        card.style.transformStyle = 'preserve-3d';

        card.addEventListener('mousemove', (e) => {
            const rect = card.getBoundingClientRect();
            const x = (e.clientX - rect.left) / rect.width - 0.5;
            const y = (e.clientY - rect.top) / rect.height - 0.5;
            card.style.transform = `perspective(800px) rotateY(${x * max}deg) rotateX(${-y * max}deg)`;
        });
        card.addEventListener('mouseleave', () => {
            card.style.transform = 'perspective(800px) rotateY(0) rotateX(0)';
        });
    });
}

/* ─────────────────────────────────────────────────────────────────────────
 * 5. Animated counters: [data-counter="12345"] ticks from 0 -> target
 *    when the element enters the viewport. Honours [data-counter-format].
 * ─────────────────────────────────────────────────────────────────────── */
function initCounters() {
    const fmt = (n, format) => {
        if (format === 'compact') {
            if (n >= 1000) return Math.round(n / 100) / 10 + 'K+';
            return Math.round(n).toString();
        }
        return Math.round(n).toLocaleString();
    };

    const animateTo = (el, target, duration = 1600) => {
        const format = el.dataset.counterFormat;
        const suffix = el.dataset.counterSuffix ?? '';
        const start = performance.now();
        const tick = (now) => {
            const t = Math.min((now - start) / duration, 1);
            const eased = 1 - Math.pow(1 - t, 3);
            el.textContent = fmt(target * eased, format) + suffix;
            if (t < 1) requestAnimationFrame(tick);
        };
        requestAnimationFrame(tick);
    };

    const io = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) return;
                const el = entry.target;
                animateTo(el, parseFloat(el.dataset.counter));
                io.unobserve(el);
            });
        },
        { threshold: 0.5 },
    );

    document.querySelectorAll('[data-counter]').forEach((el) => {
        if (reduced) {
            el.textContent = parseFloat(el.dataset.counter).toLocaleString() + (el.dataset.counterSuffix ?? '');
            return;
        }
        el.textContent = '0';
        io.observe(el);
    });
}

/* ─────────────────────────────────────────────────────────────────────────
 * 6. Hero scroll parallax: any [data-parallax="0.4"] translates Y based on
 *    scroll progress. Cheap rAF-based; no library.
 * ─────────────────────────────────────────────────────────────────────── */
function initParallax() {
    if (reduced) return;

    const items = Array.from(document.querySelectorAll('[data-parallax]')).map((el) => ({
        el,
        speed: parseFloat(el.dataset.parallax) || 0.3,
    }));
    if (!items.length) return;

    let ticking = false;
    const onScroll = () => {
        if (ticking) return;
        ticking = true;
        requestAnimationFrame(() => {
            const y = window.scrollY;
            items.forEach(({ el, speed }) => {
                el.style.transform = `translate3d(0, ${y * speed}px, 0)`;
            });
            ticking = false;
        });
    };
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
}

/* ─────────────────────────────────────────────────────────────────────────
 * 7. Hero 3D gold bar: scroll-controlled rotation + idle wobble.
 *    Targets [data-hero-bar].
 * ─────────────────────────────────────────────────────────────────────── */
function initHeroBar() {
    const bar = document.querySelector('[data-hero-bar]');
    if (!bar) return;
    if (reduced) return;

    let rx = -12, ry = 24, rz = 0;
    let mouseX = 0, mouseY = 0;

    // Mouse-follow tilt while hovering near the hero
    const hero = bar.closest('section') || document.body;
    hero.addEventListener('mousemove', (e) => {
        const rect = hero.getBoundingClientRect();
        mouseX = (e.clientX - rect.left) / rect.width - 0.5;
        mouseY = (e.clientY - rect.top) / rect.height - 0.5;
    });

    let scrollY = 0;
    window.addEventListener('scroll', () => { scrollY = window.scrollY; }, { passive: true });

    const tick = () => {
        const targetX = -12 + mouseY * 18 - scrollY * 0.04;
        const targetY = 24 + mouseX * 25 + scrollY * 0.08;
        rx += (targetX - rx) * 0.08;
        ry += (targetY - ry) * 0.08;
        rz = Math.sin(performance.now() / 1800) * 1.5;
        bar.style.transform = `rotateX(${rx}deg) rotateY(${ry}deg) rotateZ(${rz}deg)`;
        requestAnimationFrame(tick);
    };
    requestAnimationFrame(tick);
}

/* ─────────────────────────────────────────────────────────────────────────
 * 8. Particles: tiny gold sparks drifting upward over the hero.
 *    Targets [data-particles] (a <canvas>).
 * ─────────────────────────────────────────────────────────────────────── */
function initParticles() {
    const canvas = document.querySelector('[data-particles]');
    if (!canvas) return;
    if (reduced) return;

    const ctx = canvas.getContext('2d');
    const dpr = Math.min(window.devicePixelRatio || 1, 2);

    const resize = () => {
        canvas.width = canvas.offsetWidth * dpr;
        canvas.height = canvas.offsetHeight * dpr;
        ctx.scale(dpr, dpr);
    };
    resize();
    window.addEventListener('resize', resize);

    const count = 38;
    const parts = Array.from({ length: count }, () => ({
        x: Math.random() * canvas.offsetWidth,
        y: Math.random() * canvas.offsetHeight,
        r: Math.random() * 1.6 + 0.4,
        vy: -(Math.random() * 0.4 + 0.15),
        vx: (Math.random() - 0.5) * 0.15,
        alpha: Math.random() * 0.6 + 0.2,
    }));

    const draw = () => {
        ctx.clearRect(0, 0, canvas.offsetWidth, canvas.offsetHeight);
        parts.forEach((p) => {
            p.x += p.vx;
            p.y += p.vy;
            if (p.y < -10) {
                p.y = canvas.offsetHeight + 10;
                p.x = Math.random() * canvas.offsetWidth;
            }
            if (p.x < -10 || p.x > canvas.offsetWidth + 10) p.x = Math.random() * canvas.offsetWidth;

            const grad = ctx.createRadialGradient(p.x, p.y, 0, p.x, p.y, p.r * 4);
            grad.addColorStop(0, `rgba(232, 201, 106, ${p.alpha})`);
            grad.addColorStop(1, 'rgba(232, 201, 106, 0)');
            ctx.fillStyle = grad;
            ctx.beginPath();
            ctx.arc(p.x, p.y, p.r * 4, 0, Math.PI * 2);
            ctx.fill();
        });
        requestAnimationFrame(draw);
    };
    requestAnimationFrame(draw);
}

/* ───── Boot ──────────────────────────────────────────────────────────── */
const boot = () => {
    loadLenis();
    initRevealAnimations();
    initMagneticButtons();
    initTiltCards();
    initCounters();
    initParallax();
    initHeroBar();
    initParticles();
};

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', boot);
} else {
    boot();
}

// Re-run on Livewire DOM updates so dynamically inserted reveal targets work.
document.addEventListener('livewire:navigated', boot);
