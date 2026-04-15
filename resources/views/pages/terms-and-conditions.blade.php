<x-layouts.app title="Terms & Conditions - Islamabad Bullion Exchange">

    {{-- Hero --}}
    <section class="relative" style="background: linear-gradient(135deg, #0A2E23, #143D2B);">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-20">
            <div class="text-center max-w-2xl mx-auto">
                <div class="w-14 h-14 rounded-xl flex items-center justify-center mx-auto mb-5" style="background: rgba(201,168,76,0.15);">
                    <svg class="w-7 h-7" style="color: #E8C96A;" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                    </svg>
                </div>
                <h1 class="text-3xl md:text-4xl font-bold text-white mb-4">Terms & Conditions</h1>
                <p class="text-white/50 text-sm">Last updated: April 2026</p>
            </div>
        </div>
    </section>

    {{-- Content --}}
    <section class="py-12 md:py-20" style="background: #0F2419;">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            <div class="rounded-2xl p-6 md:p-8 border" style="background: rgba(255,255,255,0.02); border-color: rgba(255,255,255,0.08);">
                <p class="text-white/55 text-sm leading-relaxed">Welcome to Islamabad Bullion Exchange. By accessing and using this website, you accept and agree to be bound by the terms and provisions of this agreement. If you do not agree to these terms, please do not use our website.</p>
            </div>

            @php
                $sections = [
                    [
                        'icon' => 'M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25m18 0A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25m18 0V12a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 12V5.25',
                        'title' => 'Use of Website',
                        'content' => 'You may use this website for lawful purposes only. You must not use this website in any way that causes, or may cause, damage to the website or impairment of the availability or accessibility of the website. You must not use this website for any unlawful, illegal, fraudulent, or harmful purpose or activity.',
                    ],
                    [
                        'icon' => 'M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z',
                        'title' => 'Intellectual Property',
                        'content' => 'Unless otherwise stated, Islamabad Bullion Exchange owns the intellectual property rights for all material on this website. All intellectual property rights are reserved. You may access content from Islamabad Bullion Exchange for your own personal use subject to restrictions set in these terms and conditions. You must not republish, sell, rent, sub-license, reproduce, duplicate, or redistribute content from this website.',
                    ],
                    [
                        'icon' => 'M10.5 21l5.25-11.25L21 21m-9-3h7.5M3 5.621a48.474 48.474 0 016-.371m0 0c1.12 0 2.233.038 3.334.114M9 5.25V3m3.334 2.364C11.176 10.658 7.69 15.08 3 17.502m9.334-12.138c.896.061 1.785.147 2.666.257m-4.589 8.495a18.023 18.023 0 01-3.827-5.802',
                        'title' => 'Accuracy of Information',
                        'content' => 'While we strive to provide accurate and up-to-date gold and silver pricing information, we do not warrant that the information on this website is accurate, complete, or current. The material on this website is provided for general information only and should not be relied upon or used as the sole basis for making decisions. Prices displayed are indicative and may differ from actual transaction prices.',
                    ],
                    [
                        'icon' => 'M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z',
                        'title' => 'Orders & Transactions',
                        'content' => 'All orders placed through our website are subject to acceptance. We reserve the right to refuse or cancel any order at our discretion. Prices at the time of order placement are subject to confirmation. Payment must be made through our approved payment methods. Refund and return policies are governed by our separate refund policy.',
                    ],
                    [
                        'icon' => 'M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z',
                        'title' => 'Limitation of Liability',
                        'content' => 'In no event shall Islamabad Bullion Exchange, nor any of its officers, directors, and employees, be held liable for anything arising out of or in any way connected with your use of this website. Islamabad Bullion Exchange shall not be held liable for any indirect, consequential, or special liability arising out of or in any way related to your use of this website.',
                    ],
                    [
                        'icon' => 'M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99',
                        'title' => 'Changes to Terms',
                        'content' => 'Islamabad Bullion Exchange reserves the right to revise these terms and conditions at any time without prior notice. By using this website, you are expected to review these terms on a regular basis. Your continued use of the website following the posting of changes constitutes your acceptance of those changes.',
                    ],
                    [
                        'icon' => 'M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 017.843 4.582M12 3a8.997 8.997 0 00-7.843 4.582m15.686 0A11.953 11.953 0 0112 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0121 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0112 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 013 12c0-1.605.42-3.113 1.157-4.418',
                        'title' => 'Governing Law',
                        'content' => 'These terms and conditions are governed by and construed in accordance with the laws of Pakistan. You irrevocably submit to the exclusive jurisdiction of the courts in Islamabad, Pakistan for the resolution of any disputes.',
                    ],
                ];
            @endphp

            @foreach($sections as $section)
            <div class="rounded-2xl p-6 md:p-8 border" style="background: rgba(255,255,255,0.02); border-color: rgba(255,255,255,0.08);">
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center shrink-0" style="background: rgba(201,168,76,0.12);">
                        <svg class="w-5 h-5" style="color: #E8C96A;" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $section['icon'] }}" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-semibold text-white mb-3">{{ $section['title'] }}</h2>
                        <p class="text-white/55 text-sm leading-relaxed">{{ $section['content'] }}</p>
                    </div>
                </div>
            </div>
            @endforeach

            {{-- Contact --}}
            <div class="text-center pt-4">
                <p class="text-white/40 text-sm">Questions about these terms? <a href="/contact" class="transition-colors hover:opacity-80" style="color: #E8C96A;">Contact us</a></p>
            </div>
        </div>
    </section>

</x-layouts.app>
