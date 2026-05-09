<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard Absensi Wajah') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <!-- Total Karyawan -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-2xl p-6 border-t-4 border-indigo-500">
                    <div class="text-gray-500 dark:text-gray-400 text-sm font-medium uppercase tracking-wide">Total Karyawan Aktif</div>
                    <div class="mt-2 flex items-center justify-between">
                        <div class="text-3xl font-bold text-gray-900 dark:text-white">{{ $totalEmployees }}</div>
                        <div class="p-3 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-500 rounded-full">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        </div>
                    </div>
                </div>

                <!-- Absen Hari Ini -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-2xl p-6 border-t-4 border-emerald-500">
                    <div class="text-gray-500 dark:text-gray-400 text-sm font-medium uppercase tracking-wide">Hadir Hari Ini</div>
                    <div class="mt-2 flex items-center justify-between">
                        <div class="text-3xl font-bold text-gray-900 dark:text-white">{{ $totalPresent }}</div>
                        <div class="p-3 bg-emerald-50 dark:bg-emerald-900/30 text-emerald-500 rounded-full">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                    </div>
                </div>

                <!-- Belum Absen -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-2xl p-6 border-t-4 border-amber-500">
                    <div class="text-gray-500 dark:text-gray-400 text-sm font-medium uppercase tracking-wide">Belum Hadir</div>
                    <div class="mt-2 flex items-center justify-between">
                        <div class="text-3xl font-bold text-gray-900 dark:text-white">{{ $totalAbsent }}</div>
                        <div class="p-3 bg-amber-50 dark:bg-amber-900/30 text-amber-500 rounded-full">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                    </div>
                </div>

                <!-- Sudah Pulang -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-2xl p-6 border-t-4 border-blue-500">
                    <div class="text-gray-500 dark:text-gray-400 text-sm font-medium uppercase tracking-wide">Sudah Pulang</div>
                    <div class="mt-2 flex items-center justify-between">
                        <div class="text-3xl font-bold text-gray-900 dark:text-white">{{ $totalCheckout }}</div>
                        <div class="p-3 bg-blue-50 dark:bg-blue-900/30 text-blue-500 rounded-full">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Attendances -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-2xl">
                <div class="p-6 text-gray-900 dark:text-gray-100 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
                    <h3 class="text-lg font-semibold">Absensi Terakhir (Hari Ini)</h3>
                    <a href="{{ route('attendance-report.index') }}" class="text-sm text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">Lihat Semua &rarr;</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-700/50">
                                <th class="py-4 px-6 font-semibold text-sm text-gray-600 dark:text-gray-300">Karyawan</th>
                                <th class="py-4 px-6 font-semibold text-sm text-gray-600 dark:text-gray-300">Waktu Masuk</th>
                                <th class="py-4 px-6 font-semibold text-sm text-gray-600 dark:text-gray-300">Waktu Keluar</th>
                                <th class="py-4 px-6 font-semibold text-sm text-gray-600 dark:text-gray-300">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @forelse($recentAttendances as $attendance)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition duration-150">
                                    <td class="py-4 px-6">
                                        <div class="flex items-center">
                                            <div class="h-10 w-10 flex-shrink-0 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center overflow-hidden">
                                                @if($attendance->employee->photo_path)
                                                    <img class="h-full w-full object-cover" src="{{ asset('storage/' . $attendance->employee->photo_path) }}" alt="">
                                                @else
                                                    <span class="text-gray-500 font-medium">{{ substr($attendance->employee->name, 0, 1) }}</span>
                                                @endif
                                            </div>
                                            <div class="ml-4">
                                                <div class="font-medium text-gray-900 dark:text-white">{{ $attendance->employee->name }}</div>
                                                <div class="text-sm text-gray-500 dark:text-gray-400">{{ $attendance->employee->department ?? 'No Dept' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-4 px-6 text-sm">
                                        {{ $attendance->time_in ?? '-' }}
                                    </td>
                                    <td class="py-4 px-6 text-sm">
                                        {{ $attendance->time_out ?? '-' }}
                                    </td>
                                    <td class="py-4 px-6">
                                        @if($attendance->time_in && !$attendance->time_out)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400">
                                                Sudah Masuk
                                            </span>
                                        @elseif($attendance->time_in && $attendance->time_out)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400">
                                                Sudah Pulang
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-8 text-center text-gray-500 dark:text-gray-400">Belum ada data absensi hari ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
