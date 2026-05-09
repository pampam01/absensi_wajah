<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Riwayat Absensi') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-2xl">
                
                <!-- Filter Section -->
                <div class="p-6 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                    <form method="GET" action="{{ route('attendance-report.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
                        
                        <div>
                            <x-input-label for="start_date" :value="__('Tanggal Mulai')" />
                            <x-text-input id="start_date" class="block mt-1 w-full text-sm" type="date" name="start_date" value="{{ request('start_date', \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d')) }}" />
                        </div>
                        
                        <div>
                            <x-input-label for="end_date" :value="__('Tanggal Akhir')" />
                            <x-text-input id="end_date" class="block mt-1 w-full text-sm" type="date" name="end_date" value="{{ request('end_date') }}" />
                        </div>
                        
                        <div>
                            <x-input-label for="employee_id" :value="__('Karyawan')" />
                            <select id="employee_id" name="employee_id" class="mt-1 block w-full text-sm border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                <option value="">Semua Karyawan</option>
                                @foreach($employees as $emp)
                                    <option value="{{ $emp->id }}" {{ request('employee_id') == $emp->id ? 'selected' : '' }}>{{ $emp->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div>
                            <x-input-label for="department" :value="__('Departemen')" />
                            <select id="department" name="department" class="mt-1 block w-full text-sm border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                <option value="">Semua Dept</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept }}" {{ request('department') == $dept ? 'selected' : '' }}>{{ $dept }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="flex space-x-2">
                            <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                Filter
                            </button>
                            <button type="submit" name="export" value="csv" class="w-full inline-flex justify-center items-center px-4 py-2 bg-emerald-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-emerald-700 focus:bg-emerald-700 active:bg-emerald-900 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                Export CSV
                            </button>
                        </div>
                    </form>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 text-sm">
                                <th class="py-4 px-6 font-semibold border-b border-gray-100 dark:border-gray-700">Tanggal</th>
                                <th class="py-4 px-6 font-semibold border-b border-gray-100 dark:border-gray-700">Karyawan</th>
                                <th class="py-4 px-6 font-semibold border-b border-gray-100 dark:border-gray-700">Dept</th>
                                <th class="py-4 px-6 font-semibold border-b border-gray-100 dark:border-gray-700">Jam Masuk</th>
                                <th class="py-4 px-6 font-semibold border-b border-gray-100 dark:border-gray-700">Jam Keluar</th>
                                <th class="py-4 px-6 font-semibold border-b border-gray-100 dark:border-gray-700">Status</th>
                                <th class="py-4 px-6 font-semibold border-b border-gray-100 dark:border-gray-700">Match</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @forelse($attendances as $attendance)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition duration-150">
                                    <td class="py-4 px-6 text-sm text-gray-900 dark:text-gray-200">
                                        {{ $attendance->date->format('d M Y') }}
                                    </td>
                                    <td class="py-4 px-6">
                                        <div class="font-medium text-gray-900 dark:text-white">{{ $attendance->employee->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $attendance->employee->employee_code ?? '-' }}</div>
                                    </td>
                                    <td class="py-4 px-6 text-sm text-gray-500 dark:text-gray-400">
                                        {{ $attendance->employee->department ?? '-' }}
                                    </td>
                                    <td class="py-4 px-6 text-sm font-mono text-gray-900 dark:text-gray-200">
                                        {{ $attendance->time_in ?? '-' }}
                                    </td>
                                    <td class="py-4 px-6 text-sm font-mono text-gray-900 dark:text-gray-200">
                                        {{ $attendance->time_out ?? '-' }}
                                    </td>
                                    <td class="py-4 px-6">
                                        @if($attendance->status == 'present')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400">
                                                Hadir
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                                {{ ucfirst($attendance->status) }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-6 text-sm font-mono text-gray-500">
                                        {{ $attendance->match_distance ?? '-' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-8 text-center text-gray-500 dark:text-gray-400">Tidak ada data absensi untuk filter tersebut.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
