<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Registrasi Wajah Karyawan') }}
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

                        <div class="mt-6 flex justify-center">
                            <button id="capture-btn" disabled class="disabled:opacity-50 inline-flex items-center px-6 py-3 bg-gradient-to-r from-indigo-500 to-purple-600 border border-transparent rounded-full font-bold text-white uppercase tracking-widest hover:from-indigo-600 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150 shadow-lg transform hover:-translate-y-1">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                Ambil Foto & Daftarkan Wajah
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Form Column -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-2xl p-6 h-fit">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Pilih Karyawan</h3>
                    
                    <div id="alert-container" class="hidden mb-4 p-4 rounded-xl text-sm font-medium"></div>

                    <form id="registration-form" class="space-y-4">
                        <div>
                            <x-input-label for="employee_id" :value="__('Karyawan (Belum Terdaftar)')" />
                            <select id="employee_id" name="employee_id" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>
                                <option value="" disabled selected>-- Pilih Karyawan --</option>
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}">{{ $employee->name }} ({{ $employee->employee_code ?? '-' }})</option>
                                @endforeach
                            </select>
                            <p class="text-xs text-gray-500 mt-2">Hanya menampilkan karyawan yang belum memiliki data wajah.</p>
                        </div>

                        <!-- Preview Hasil -->
                        <div class="pt-4 border-t border-gray-100 dark:border-gray-700 mt-6">
                            <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Hasil Tangkapan Wajah</h4>
                            <div class="aspect-square bg-gray-100 dark:bg-gray-700 rounded-xl overflow-hidden border-2 border-dashed border-gray-300 dark:border-gray-600 flex items-center justify-center relative">
                                <img id="preview-image" class="hidden w-full h-full object-cover transform scale-x-[-1]" src="" alt="Preview">
                                <span id="preview-placeholder" class="text-gray-400 text-sm">Belum ada foto</span>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>

    @push('scripts')
        <script>
            window.routes = {
                faceRegistrationStore: "{{ route('face-registration.store') }}",
                csrfToken: "{{ csrf_token() }}"
            };
        </script>
        @vite(['resources/js/face-registration-camera.js'])
    @endpush
</x-app-layout>
