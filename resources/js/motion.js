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
    // Reveal is purely additive: the CSS animation runs once when we add
    // [data-reveal-armed]. We never hide anything ourselves. If this whole
    // function fails to run, the page is still visible and usable.
    if (reduced) return;

    const armEl = (el) => {
        const stagger = el.dataset.revealStagger;
        if (stagger) {
            Array.from(el.children).forEach((child, i) => {
                child.style.setProperty('--stagger-delay', `${i * parseInt(stagger, 10)}ms`);
                child.classList.add('reveal-stagger-armed');
            });
        } else {
            el.setAttribute('data-reveal-armed', '');
        }
    };

    const inView = (el) => {
        const r = el.getBoundingClientRect();
        return r.top < window.innerHeight && r.bottom > 0;
    };

    const io = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) return;
                armEl(entry.target);
                io.unobserve(entry.target);
            });
        },
        { threshold: 0.12, rootMargin: '0px 0px -8% 0px' },
    );

    requestAnimationFrame(() => {
        document.querySelectorAll('[data-reveal]').forEach((el) => {
            if (inView(el)) armEl(el);
            else io.observe(el);
        });
    });
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

    const inView = (el) => {
        const r = el.getBoundingClientRect();
        return r.top < window.innerHeight && r.bottom > 0;
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

    const wireCounter = (el) => {
        if (reduced) {
            el.textContent = parseFloat(el.dataset.counter).toLocaleString() + (el.dataset.counterSuffix ?? '');
            return;
        }
        el.textContent = '0';
        // Defer the in-view check until layout has settled so font-loading
        // doesn't push counters off-screen at the moment we read their rect.
        requestAnimationFrame(() => {
            if (inView(el)) animateTo(el, parseFloat(el.dataset.counter));
            else io.observe(el);
        });
    };

    document.querySelectorAll('[data-counter]').forEach(wireCounter);
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

/* ─────────────────────────────────────────────────────────────────────────
 * 9. Word-by-word text reveal: [data-text-reveal] elements split their
 *    textContent into <span class="word"> wrappers that stagger in. Each
 *    word also floats up + blurs in, giving a cinematic typography feel.
 * ─────────────────────────────────────────────────────────────────────── */
function initTextReveal() {
    const targets = document.querySelectorAll('[data-text-reveal]');
    if (!targets.length) return;

    targets.forEach((el) => {
        if (el.dataset.textRevealReady === '1') return;
        const raw = el.textContent.trim();
        el.dataset.textRevealReady = '1';
        el.innerHTML = '';
        raw.split(/\s+/).forEach((w, i) => {
            const span = document.createElement('span');
            span.className = 'word';
            span.style.transitionDelay = `${i * 70}ms`;
            span.textContent = w + ' ';
            el.appendChild(span);
        });
    });

    const revealWords = (el) => el.querySelectorAll('.word').forEach((w) => w.classList.add('in'));

    if (reduced) {
        targets.forEach(revealWords);
        return;
    }

    const inView = (el) => {
        const r = el.getBoundingClientRect();
        return r.top < window.innerHeight && r.bottom > 0;
    };

    const io = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) return;
                revealWords(entry.target);
                io.unobserve(entry.target);
            });
        },
        { threshold: 0.2 },
    );
    requestAnimationFrame(() => {
        targets.forEach((el) => {
            if (inView(el)) revealWords(el);
            else io.observe(el);
        });
    });
}

/* ─────────────────────────────────────────────────────────────────────────
 * 10. Digit roller: when any [data-price] element's text changes, animate
 *     the affected digits as a vertical roll (odometer / split-flap feel).
 *     Works on any text-based number — re-uses lastValues from price-flasher.js
 *     indirectly by reacting to text mutations.
 * ─────────────────────────────────────────────────────────────────────── */
function initDigitRoller() {
    if (reduced) return;

    const prepare = (el) => {
        if (el.dataset.rollerReady === '1') return;
        el.dataset.rollerReady = '1';
        el.dataset.lastText = el.textContent;
    };

    const animate = (el, oldText, newText) => {
        // Pull out just the digit-changing positions. If lengths differ,
        // fall back to a simple flash; no useful character-level diff.
        if (oldText.length !== newText.length) {
            el.classList.remove('roller-flash');
            void el.offsetWidth;
            el.classList.add('roller-flash');
            return;
        }
        let html = '';
        for (let i = 0; i < newText.length; i++) {
            const o = oldText[i];
            const n = newText[i];
            if (o === n || !/\d/.test(n)) {
                html += n === ' ' ? '&nbsp;' : (n === ',' ? ',' : n);
            } else {
                html += `<span class="roller-digit"><span class="roller-old">${o}</span><span class="roller-new">${n}</span></span>`;
            }
        }
        el.innerHTML = html;
        // Trigger reflow then roll
        requestAnimationFrame(() => {
            el.querySelectorAll('.roller-digit').forEach((d) => d.classList.add('rolling'));
        });
        // After the roll, collapse back to plain text so subsequent flashes work
        setTimeout(() => {
            el.textContent = newText;
            el.dataset.lastText = newText;
        }, 500);
    };

    const watch = (el) => {
        prepare(el);
        const obs = new MutationObserver(() => {
            const newText = el.textContent;
            const oldText = el.dataset.lastText || newText;
            if (newText === oldText) return;
            // Don't recurse on our own DOM rewrites
            if (el.querySelector('.roller-digit')) return;
            animate(el, oldText, newText);
        });
        obs.observe(el, { childList: true, characterData: true, subtree: true });
    };

    document.querySelectorAll('[data-price]').forEach(watch);
}

/* ─────────────────────────────────────────────────────────────────────────
 *                       PHASE 3 — POLISH LAYER
 * ─────────────────────────────────────────────────────────────────────── */

/* 11. Custom cursor: a small dot follows the pointer with slight lag, and
 *     a larger outline grows when hovering over interactive elements.
 *     Disabled on touch / coarse-pointer devices automatically.
 */
function initCustomCursor() {
    if (reduced) return;
    if (window.matchMedia('(pointer: coarse)').matches) return;

    const dot = document.createElement('div');
    dot.className = 'cursor-dot';
    const ring = document.createElement('div');
    ring.className = 'cursor-ring';
    document.body.appendChild(dot);
    document.body.appendChild(ring);

    let mx = -100, my = -100;
    let rx = -100, ry = -100;

    window.addEventListener('mousemove', (e) => {
        mx = e.clientX; my = e.clientY;
        dot.style.transform = `translate3d(${mx}px, ${my}px, 0)`;
    });
    window.addEventListener('mouseleave', () => {
        dot.style.opacity = '0';
        ring.style.opacity = '0';
    });
    window.addEventListener('mouseenter', () => {
        dot.style.opacity = '';
        ring.style.opacity = '';
    });

    const tick = () => {
        rx += (mx - rx) * 0.18;
        ry += (my - ry) * 0.18;
        ring.style.transform = `translate3d(${rx}px, ${ry}px, 0)`;
        requestAnimationFrame(tick);
    };
    requestAnimationFrame(tick);

    const setHover = (state) => {
        ring.classList.toggle('cursor-ring--hover', state);
    };
    // Inflate the ring over anything obviously interactive.
    const interactive = 'a, button, [role="button"], input, textarea, select, [data-magnetic], [data-tilt]';
    document.querySelectorAll(interactive).forEach((el) => {
        el.addEventListener('mouseenter', () => setHover(true));
        el.addEventListener('mouseleave', () => setHover(false));
    });
}

/* 12. Page transitions: cross-fade the body on Livewire navigations using
 *     the modern View Transitions API when available, with a CSS fallback.
 */
function initPageTransitions() {
    if (reduced) return;

    document.addEventListener('livewire:navigate', () => {
        document.documentElement.classList.add('page-leaving');
    });
    document.addEventListener('livewire:navigated', () => {
        document.documentElement.classList.remove('page-leaving');
        document.documentElement.classList.add('page-entering');
        setTimeout(() => document.documentElement.classList.remove('page-entering'), 600);
    });
}

/* ─────────────────────────────────────────────────────────────────────────
 *                       PHASE 4 — REAL 3D (Three.js)
 * ─────────────────────────────────────────────────────────────────────── */

/* 13. Three.js gold bar — replaces the CSS-3D bar with a real WebGL gold
 *     ingot when [data-three-bar] is present. Procedural geometry, no
 *     external asset download. Lazy-loaded only when the hero is in view
 *     and only on devices with WebGL + a fine pointer (skip mobile by
 *     default; CSS bar fallback handles those cases).
 */
function initThreeBar() {
    const host = document.querySelector('[data-three-bar]');
    if (!host) return;
    if (reduced) return;

    // Detect WebGL support
    try {
        const canvas = document.createElement('canvas');
        const gl = canvas.getContext('webgl2') || canvas.getContext('webgl');
        if (!gl) return; // bail; CSS bar stays visible
    } catch (e) { return; }

    // Single core-THREE import — avoids fragile examples/jsm submodule loads
    // that fail in some iframes/CSP contexts. We build the rounded ingot
    // geometry by chamfering a plain BoxGeometry's corners manually.
    let booted = false;
    const boot = () => {
        if (booted) return;
        booted = true;

        const s = document.createElement('script');
        s.type = 'module';
        s.textContent = `
            (async () => {
                try {
                    const THREE = await import('https://cdn.jsdelivr.net/npm/three@0.160.0/build/three.module.js');
                    const host = document.querySelector('[data-three-bar]');
                    if (!host) return;

                    const W = () => host.clientWidth || 320;
                    const H = () => host.clientHeight || 220;

                    const renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true });
                    renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
                    renderer.setSize(W(), H());
                    renderer.toneMapping = THREE.ACESFilmicToneMapping;
                    renderer.toneMappingExposure = 1.2;
                    host.appendChild(renderer.domElement);

                    const scene = new THREE.Scene();
                    const camera = new THREE.PerspectiveCamera(35, W() / H(), 0.1, 100);
                    camera.position.set(0, 0, 6);

                    // Tapered gold ingot: 8 vertices, hand-shaped wider-top profile.
                    // Top face is bigger than the bottom face (classic bar look).
                    const verts = new Float32Array([
                        // bottom face (smaller)
                        -1.4, -0.55, -0.45,   1.4, -0.55, -0.45,
                         1.4, -0.55,  0.45,  -1.4, -0.55,  0.45,
                        // top face (larger)
                        -1.6,  0.55, -0.55,   1.6,  0.55, -0.55,
                         1.6,  0.55,  0.55,  -1.6,  0.55,  0.55,
                    ]);
                    const idx = new Uint16Array([
                        0,1,2, 0,2,3,       // bottom
                        4,6,5, 4,7,6,       // top
                        0,5,1, 0,4,5,       // front
                        1,6,2, 1,5,6,       // right
                        2,6,7, 2,7,3,       // back
                        3,7,4, 3,4,0,       // left
                    ]);
                    const geom = new THREE.BufferGeometry();
                    geom.setAttribute('position', new THREE.BufferAttribute(verts, 3));
                    geom.setIndex(new THREE.BufferAttribute(idx, 1));
                    geom.computeVertexNormals();

                    const mat = new THREE.MeshPhysicalMaterial({
                        color: new THREE.Color('#E0B651'),
                        metalness: 1.0,
                        roughness: 0.25,
                        clearcoat: 0.5,
                        clearcoatRoughness: 0.2,
                    });
                    const bar = new THREE.Mesh(geom, mat);
                    bar.rotation.set(-0.18, 0.55, 0);
                    scene.add(bar);

                    // Lighting: warm key, cool fill, gold rim, ambient warmth
                    const key = new THREE.DirectionalLight(0xfff4d0, 2.8);
                    key.position.set(2.5, 3, 4);
                    scene.add(key);
                    const fill = new THREE.DirectionalLight(0x66b3ff, 0.6);
                    fill.position.set(-3, 1, 2);
                    scene.add(fill);
                    const rim = new THREE.DirectionalLight(0xffd97a, 1.8);
                    rim.position.set(-1.5, -2, -3);
                    scene.add(rim);
                    scene.add(new THREE.AmbientLight(0x442200, 0.5));

                    // (env map removed — the EquirectangularReflectionMapping path
                    // triggers a CubeUV GLSL bug on non-power-of-two textures. The
                    // three directional lights + clearcoat give plenty of metal feel.)

                    let mouseX = 0, mouseY = 0;
                    window.addEventListener('mousemove', (e) => {
                        const r = host.getBoundingClientRect();
                        mouseX = (e.clientX - (r.left + r.width / 2)) / window.innerWidth;
                        mouseY = (e.clientY - (r.top + r.height / 2)) / window.innerHeight;
                    });

                    let scrollY = 0;
                    window.addEventListener('scroll', () => { scrollY = window.scrollY; }, { passive: true });

                    const resize = () => {
                        renderer.setSize(W(), H());
                        camera.aspect = W() / H();
                        camera.updateProjectionMatrix();
                    };
                    window.addEventListener('resize', resize);

                    const animate = () => {
                        const t = performance.now() / 2200;
                        const targetY = 0.55 + mouseX * 1.2 + scrollY * 0.0008;
                        const targetX = -0.18 + mouseY * 0.7 + Math.sin(t) * 0.02;
                        bar.rotation.y += (targetY - bar.rotation.y) * 0.08;
                        bar.rotation.x += (targetX - bar.rotation.x) * 0.08;
                        bar.rotation.z = Math.sin(t * 0.6) * 0.04;
                        renderer.render(scene, camera);
                        requestAnimationFrame(animate);
                    };
                    animate();

                    // Hide CSS fallback once the WebGL canvas is mounted.
                    const fallback = document.querySelector('[data-three-bar-fallback]');
                    if (fallback) fallback.style.display = 'none';

                    window.__threeBarReady = true;
                } catch (e) {
                    window.__threeBarError = e && e.message ? e.message : String(e);
                    console.warn('Three.js gold bar failed; CSS fallback stays visible:', e);
                }
            })();
        `;
        document.head.appendChild(s);
    };

    // Hero is above the fold, so boot right away — no IntersectionObserver
    // dance. (We already gated on WebGL support above.)
    boot();
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
    initTextReveal();
    initDigitRoller();
    initCustomCursor();
    initPageTransitions();
    initThreeBar();
};

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', boot);
} else {
    boot();
}

// Re-run on Livewire DOM updates so dynamically inserted reveal targets work.
document.addEventListener('livewire:navigated', boot);
