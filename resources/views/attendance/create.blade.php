<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Absensi Wajah') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Camera Column -->
                <div class="md:col-span-2 space-y-6">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-2xl p-6 relative">
                        <!-- Loading State -->
                        <div id="loading-overlay" class="absolute inset-0 bg-white/80 dark:bg-gray-800/80 z-10 flex flex-col items-center justify-center rounded-2xl backdrop-blur-sm">
                            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600 dark:border-indigo-400"></div>
                            <p class="mt-4 text-gray-600 dark:text-gray-300 font-medium" id="loading-text">Memuat model Face API...</p>
                        </div>

                        <div class="aspect-video bg-black rounded-xl overflow-hidden relative">
                            <video id="webcam" autoplay muted playsinline class="w-full h-full object-cover transform scale-x-[-1]"></video>
                            <canvas id="overlay" class="absolute top-0 left-0 w-full h-full transform scale-x-[-1]"></canvas>
                        </div>

                        <div class="mt-6 flex justify-center space-x-4">
                            <button id="scan-btn" disabled class="disabled:opacity-50 inline-flex items-center px-6 py-3 bg-gradient-to-r from-emerald-500 to-teal-600 border border-transparent rounded-full font-bold text-white uppercase tracking-widest hover:from-emerald-600 hover:to-teal-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150 shadow-lg transform hover:-translate-y-1">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path></svg>
                                Scan Wajah Sekarang
                            </button>
                            
                            <button id="auto-scan-btn" disabled class="disabled:opacity-50 inline-flex items-center px-6 py-3 bg-gray-200 dark:bg-gray-700 border border-transparent rounded-full font-bold text-gray-700 dark:text-gray-200 uppercase tracking-widest hover:bg-gray-300 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150 shadow-sm">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                <span id="auto-scan-text">Mulai Auto Scan</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Status Column -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-2xl p-6 h-fit space-y-6">
                    <div class="text-center pb-4 border-b border-gray-100 dark:border-gray-700">
                        <div class="text-4xl font-mono font-bold text-gray-900 dark:text-white" id="clock">00:00:00</div>
                        <div class="text-gray-500 dark:text-gray-400 mt-1" id="date">Tanggal</div>
                    </div>

                    <!-- Status Box -->
                    <div id="status-box" class="rounded-xl p-4 text-center bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600">
                        <svg id="status-icon" class="w-12 h-12 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <h3 id="status-title" class="mt-2 text-lg font-bold text-gray-900 dark:text-white">Menunggu Scan</h3>
                        <p id="status-desc" class="text-sm text-gray-500 dark:text-gray-400 mt-1">Arahkan wajah ke kamera.</p>
                        
                        <div id="match-details" class="hidden mt-4 pt-4 border-t border-gray-200 dark:border-gray-600">
                            <p class="text-xs text-gray-500 uppercase tracking-widest font-semibold">Match Distance</p>
                            <p id="match-distance" class="text-lg font-mono font-bold text-indigo-600 dark:text-indigo-400 mt-1">0.000</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    @push('scripts')
        <script>
            window.routes = {
                attendanceEmployees: "{{ route('attendance.employees') }}",
                attendanceStore: "{{ route('attendance.store') }}",
                csrfToken: "{{ csrf_token() }}"
            };
            
            // Simple clock
            setInterval(() => {
                const now = new Date();
                document.getElementById('clock').innerText = now.toLocaleTimeString('id-ID');
                document.getElementById('date').innerText = now.toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
            }, 1000);
        </script>
        @vite(['resources/js/attendance-camera.js'])
    @endpush
</x-app-layout>
