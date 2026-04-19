<div class="min-h-screen relative overflow-hidden" style="background: radial-gradient(ellipse at top, #0A2E23 0%, #060F09 70%);">

    {{-- Ambient gold glow --}}
    <div class="absolute inset-0 pointer-events-none">
        <div class="absolute top-0 left-1/4 w-96 h-96 rounded-full blur-3xl opacity-20" style="background: #C9A84C;"></div>
        <div class="absolute bottom-0 right-1/4 w-96 h-96 rounded-full blur-3xl opacity-10" style="background: #E8C96A;"></div>
    </div>

    <div class="relative max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-10 md:py-16">

        {{-- Header --}}
        <div class="text-center mb-10">
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full mb-4" style="background: rgba(201,168,76,0.1); border: 1px solid rgba(201,168,76,0.3);">
                <span class="relative flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full opacity-75" style="background: #E8C96A;"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2" style="background: #E8C96A;"></span>
                </span>
                <span class="text-[11px] tracking-widest font-medium uppercase" style="color: #E8C96A;">Authenticity Scanner</span>
            </div>
            <h1 class="text-3xl md:text-5xl font-bold text-white mb-3 tracking-tight">
                Scan <span style="color: #E8C96A;">QR Code</span>
            </h1>
            <p class="text-sm md:text-base max-w-xl mx-auto" style="color: rgba(255,255,255,0.6);">
                Point your camera at the QR sticker on any Islamabad Bullion Exchange piece to verify its authenticity instantly.
            </p>
        </div>

        {{-- Scanner Card --}}
        <div class="relative rounded-3xl p-1 mb-6"
             style="background: linear-gradient(135deg, rgba(201,168,76,0.6), rgba(232,201,106,0.1) 40%, rgba(201,168,76,0.3) 80%, rgba(232,201,106,0.6));">
            <div class="rounded-[22px] overflow-hidden" style="background: #0A2E23;">

                {{-- Scanner Viewport --}}
                <div x-data="qrScanner()" x-init="init()" class="relative">
                    <div id="qr-reader" class="w-full aspect-square md:aspect-video bg-black relative overflow-hidden"></div>

                    {{-- Animated scanning overlay --}}
                    <div class="absolute inset-0 pointer-events-none flex items-center justify-center"
                         x-show="scanning" x-cloak>
                        <div class="relative w-64 h-64 md:w-72 md:h-72">
                            {{-- Corner brackets --}}
                            <div class="absolute top-0 left-0 w-10 h-10 border-t-4 border-l-4 rounded-tl-2xl" style="border-color: #E8C96A;"></div>
                            <div class="absolute top-0 right-0 w-10 h-10 border-t-4 border-r-4 rounded-tr-2xl" style="border-color: #E8C96A;"></div>
                            <div class="absolute bottom-0 left-0 w-10 h-10 border-b-4 border-l-4 rounded-bl-2xl" style="border-color: #E8C96A;"></div>
                            <div class="absolute bottom-0 right-0 w-10 h-10 border-b-4 border-r-4 rounded-br-2xl" style="border-color: #E8C96A;"></div>

                            {{-- Scanning laser line --}}
                            <div class="absolute inset-x-4 h-0.5 scanner-laser" style="background: linear-gradient(90deg, transparent, #E8C96A, transparent); box-shadow: 0 0 20px #E8C96A, 0 0 40px rgba(232,201,106,0.5);"></div>
                        </div>
                    </div>

                    {{-- Pre-start placeholder --}}
                    <div class="absolute inset-0 flex flex-col items-center justify-center"
                         x-show="!scanning && !error && !success" x-cloak
                         style="background: linear-gradient(135deg, #0A2E23 0%, #060F09 100%);">
                        <div class="w-20 h-20 rounded-full flex items-center justify-center mb-4 qr-pulse" style="background: rgba(201,168,76,0.15); border: 2px solid rgba(201,168,76,0.4);">
                            <svg class="w-10 h-10" style="color: #E8C96A;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 013.75 9.375v-4.5zM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 01-1.125-1.125v-4.5zM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0113.5 9.375v-4.5zM13.5 14.25h1.5v1.5h-1.5v-1.5zM16.5 14.25h1.5v1.5h-1.5v-1.5zM19.5 14.25h.75v1.5h-.75v-1.5zM13.5 17.25h1.5v1.5h-1.5v-1.5zM13.5 20.25h1.5v.75h-1.5v-.75zM16.5 17.25h3v3h-3v-3z"/>
                            </svg>
                        </div>
                        <p class="text-sm text-white mb-4 max-w-xs text-center px-6" style="color: rgba(255,255,255,0.7);">Tap below to open your camera and start scanning</p>
                        <button @click="startScan()" class="px-6 py-2.5 rounded-full text-sm font-semibold transition-all hover:scale-105" style="background: linear-gradient(135deg, #C9A84C, #E8C96A); color: #0F2419;">
                            <span class="inline-flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z"/><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zM18.75 10.5h.008v.008h-.008V10.5z"/></svg>
                                Start Camera
                            </span>
                        </button>
                    </div>

                    {{-- Success overlay --}}
                    <div x-show="success" x-cloak
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 scale-90"
                         x-transition:enter-end="opacity-100 scale-100"
                         class="absolute inset-0 flex flex-col items-center justify-center"
                         style="background: rgba(10,46,35,0.95); backdrop-filter: blur(8px);">
                        <div class="w-24 h-24 rounded-full flex items-center justify-center mb-4 success-pop" style="background: rgba(16,185,129,0.2); border: 2px solid #10b981;">
                            <svg class="w-14 h-14 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-2">Code Detected</h3>
                        <p class="text-sm" style="color: rgba(255,255,255,0.7);">Opening verification page...</p>
                    </div>

                    {{-- Error overlay --}}
                    <div x-show="error" x-cloak class="absolute inset-0 flex flex-col items-center justify-center p-6" style="background: rgba(60,10,10,0.95); backdrop-filter: blur(8px);">
                        <div class="w-20 h-20 rounded-full flex items-center justify-center mb-4" style="background: rgba(239,68,68,0.2); border: 2px solid #ef4444;">
                            <svg class="w-10 h-10 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
                        </div>
                        <h3 class="text-lg font-bold text-white mb-2">Camera Unavailable</h3>
                        <p class="text-xs text-center max-w-sm mb-4" style="color: rgba(255,255,255,0.7);" x-text="errorMessage"></p>
                        <button @click="retry()" class="px-5 py-2 rounded-full text-xs font-semibold" style="background: #C9A84C; color: #0F2419;">Try Again</button>
                    </div>
                </div>

                {{-- Controls --}}
                <div class="p-4 md:p-5 flex items-center justify-between gap-3 flex-wrap" style="background: rgba(0,0,0,0.3); border-top: 1px solid rgba(201,168,76,0.15);">
                    <div class="flex items-center gap-2 text-xs" style="color: rgba(255,255,255,0.5);">
                        <span class="relative flex h-2 w-2" x-show="scanning">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full opacity-75" style="background: #10b981;"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2" style="background: #10b981;"></span>
                        </span>
                        <span x-text="scanning ? 'Scanning...' : 'Camera off'"></span>
                    </div>
                    <div class="flex items-center gap-2">
                        <button x-show="scanning" @click="stopScan()" class="px-4 py-1.5 rounded-full text-xs font-medium transition-colors" style="background: rgba(239,68,68,0.15); color: #fca5a5; border: 1px solid rgba(239,68,68,0.3);">
                            Stop
                        </button>
                        <button x-show="scanning && hasMultipleCameras" @click="flipCamera()" class="px-4 py-1.5 rounded-full text-xs font-medium transition-colors" style="background: rgba(201,168,76,0.15); color: #E8C96A; border: 1px solid rgba(201,168,76,0.3);">
                            Flip
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Info Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-10">
            <div class="rounded-2xl p-5 transition-transform hover:-translate-y-1" style="background: rgba(10,46,35,0.6); border: 1px solid rgba(201,168,76,0.15);">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center mb-3" style="background: rgba(201,168,76,0.15);">
                    <svg class="w-5 h-5" style="color: #E8C96A;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/></svg>
                </div>
                <h3 class="text-sm font-semibold text-white mb-1">100% Genuine</h3>
                <p class="text-xs leading-relaxed" style="color: rgba(255,255,255,0.5);">Each QR is unique and cryptographically signed — impossible to forge.</p>
            </div>

            <div class="rounded-2xl p-5 transition-transform hover:-translate-y-1" style="background: rgba(10,46,35,0.6); border: 1px solid rgba(201,168,76,0.15);">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center mb-3" style="background: rgba(201,168,76,0.15);">
                    <svg class="w-5 h-5" style="color: #E8C96A;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </div>
                <h3 class="text-sm font-semibold text-white mb-1">Instant Results</h3>
                <p class="text-xs leading-relaxed" style="color: rgba(255,255,255,0.5);">See product details, weight, purity, and live pricing in under a second.</p>
            </div>

            <div class="rounded-2xl p-5 transition-transform hover:-translate-y-1" style="background: rgba(10,46,35,0.6); border: 1px solid rgba(201,168,76,0.15);">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center mb-3" style="background: rgba(201,168,76,0.15);">
                    <svg class="w-5 h-5" style="color: #E8C96A;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/></svg>
                </div>
                <h3 class="text-sm font-semibold text-white mb-1">Privacy First</h3>
                <p class="text-xs leading-relaxed" style="color: rgba(255,255,255,0.5);">Camera runs locally in your browser — no images are uploaded anywhere.</p>
            </div>
        </div>

        {{-- Alternative: Serial Entry --}}
        <div class="rounded-2xl p-6 md:p-8 text-center" style="background: linear-gradient(135deg, rgba(15,36,25,0.9), rgba(10,46,35,0.9)); border: 1px solid rgba(201,168,76,0.2);">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full mb-3" style="background: rgba(201,168,76,0.1);">
                <span class="text-[10px] tracking-widest font-medium uppercase" style="color: #E8C96A;">No camera? No problem</span>
            </div>
            <h3 class="text-lg md:text-xl font-bold text-white mb-2">Enter Serial Number Manually</h3>
            <p class="text-xs md:text-sm max-w-md mx-auto mb-5" style="color: rgba(255,255,255,0.55);">
                You can also verify your piece by typing the serial number printed on the box (e.g. <span class="font-mono" style="color: #E8C96A;">IBE-G24K-000001</span>).
            </p>
            <a href="{{ route('verify.serial') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full text-sm font-semibold transition-all hover:gap-3" style="background: transparent; border: 1.5px solid #C9A84C; color: #E8C96A;" wire:navigate>
                Verify by Serial
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17.25 8.25L21 12m0 0l-3.75 3.75M21 12H3"/></svg>
            </a>
        </div>

    </div>

    {{-- Styles & Scripts --}}
    <style>
        #qr-reader video { width: 100% !important; height: 100% !important; object-fit: cover; }
        #qr-reader__scan_region { background: transparent !important; border: none !important; }
        #qr-reader__dashboard, #qr-reader__header_message, #qr-reader__dashboard_section_csr { display: none !important; }
        #qr-reader__camera_selection { display: none !important; }
        [x-cloak] { display: none !important; }

        @keyframes scanner-laser-move {
            0%   { top: 10%; opacity: 0; }
            10%  { opacity: 1; }
            50%  { top: 85%; opacity: 1; }
            90%  { opacity: 1; }
            100% { top: 10%; opacity: 0; }
        }
        .scanner-laser { animation: scanner-laser-move 2.2s ease-in-out infinite; }

        @keyframes qr-pulse-anim {
            0%, 100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(201,168,76,0.4); }
            50%      { transform: scale(1.05); box-shadow: 0 0 0 12px rgba(201,168,76,0); }
        }
        .qr-pulse { animation: qr-pulse-anim 2s ease-in-out infinite; }

        @keyframes success-pop-anim {
            0% { transform: scale(0.5); opacity: 0; }
            60% { transform: scale(1.15); opacity: 1; }
            100% { transform: scale(1); opacity: 1; }
        }
        .success-pop { animation: success-pop-anim 0.5s ease-out; }
    </style>

    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    <script>
        function qrScanner() {
            return {
                scanning: false,
                success: false,
                error: false,
                errorMessage: '',
                html5QrCode: null,
                cameraId: null,
                cameras: [],
                hasMultipleCameras: false,
                currentCameraIndex: 0,

                init() {
                    // Auto-start if browser supports it
                    this.$nextTick(() => {
                        setTimeout(() => this.startScan(), 300);
                    });
                },

                async startScan() {
                    this.error = false;
                    this.success = false;
                    try {
                        if (!window.Html5Qrcode) {
                            throw new Error('Scanner library not loaded. Please refresh the page.');
                        }

                        const cameras = await Html5Qrcode.getCameras();
                        if (!cameras || cameras.length === 0) {
                            throw new Error('No camera found on this device.');
                        }

                        this.cameras = cameras;
                        this.hasMultipleCameras = cameras.length > 1;

                        // Prefer back camera
                        const back = cameras.find(c => /back|rear|environment/i.test(c.label));
                        this.cameraId = back ? back.id : cameras[cameras.length - 1].id;
                        this.currentCameraIndex = cameras.findIndex(c => c.id === this.cameraId);

                        this.html5QrCode = new Html5Qrcode('qr-reader');
                        await this.html5QrCode.start(
                            this.cameraId,
                            { fps: 10, qrbox: { width: 260, height: 260 }, aspectRatio: 1.0 },
                            (decodedText) => this.onDecoded(decodedText),
                            () => { /* ignore per-frame errors */ }
                        );
                        this.scanning = true;
                    } catch (e) {
                        this.error = true;
                        this.errorMessage = e.message || 'Unable to access camera. Please allow camera permissions and try again.';
                    }
                },

                async stopScan() {
                    if (this.html5QrCode && this.scanning) {
                        try { await this.html5QrCode.stop(); } catch (_) {}
                        try { await this.html5QrCode.clear(); } catch (_) {}
                    }
                    this.scanning = false;
                },

                async flipCamera() {
                    if (!this.hasMultipleCameras) return;
                    await this.stopScan();
                    this.currentCameraIndex = (this.currentCameraIndex + 1) % this.cameras.length;
                    this.cameraId = this.cameras[this.currentCameraIndex].id;
                    try {
                        this.html5QrCode = new Html5Qrcode('qr-reader');
                        await this.html5QrCode.start(
                            this.cameraId,
                            { fps: 10, qrbox: { width: 260, height: 260 }, aspectRatio: 1.0 },
                            (decodedText) => this.onDecoded(decodedText),
                            () => {}
                        );
                        this.scanning = true;
                    } catch (e) {
                        this.error = true;
                        this.errorMessage = e.message;
                    }
                },

                async retry() {
                    this.error = false;
                    await this.startScan();
                },

                onDecoded(text) {
                    if (this.success) return;
                    this.success = true;
                    this.stopScan();
                    // Redirect — accept either full URL or plain token
                    setTimeout(() => {
                        try {
                            const url = new URL(text);
                            window.location.href = url.href;
                        } catch {
                            // Treat as plain token
                            const token = text.trim();
                            window.location.href = '/verify/' + encodeURIComponent(token);
                        }
                    }, 900);
                },
            }
        }
    </script>
</div>
