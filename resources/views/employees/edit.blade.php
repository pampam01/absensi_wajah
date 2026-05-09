<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Karyawan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-2xl">
                <div class="p-6">
                    <form method="POST" action="{{ route('employees.update', $employee) }}" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <!-- Nama -->
                        <div>
                            <x-input-label for="name" :value="__('Nama Lengkap')" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $employee->name)" required autofocus />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <!-- Kode Karyawan -->
                        <div>
                            <x-input-label for="employee_code" :value="__('Kode Karyawan / NIK (Opsional)')" />
                            <x-text-input id="employee_code" class="block mt-1 w-full" type="text" name="employee_code" :value="old('employee_code', $employee->employee_code)" />
                            <x-input-error :messages="$errors->get('employee_code')" class="mt-2" />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Departemen -->
                            <div>
                                <x-input-label for="department" :value="__('Departemen')" />
                                <x-text-input id="department" class="block mt-1 w-full" type="text" name="department" :value="old('department', $employee->department)" />
                                <x-input-error :messages="$errors->get('department')" class="mt-2" />
                            </div>

                            <!-- Posisi -->
                            <div>
                                <x-input-label for="position" :value="__('Jabatan')" />
                                <x-text-input id="position" class="block mt-1 w-full" type="text" name="position" :value="old('position', $employee->position)" />
                                <x-input-error :messages="$errors->get('position')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-8">
                            <a href="{{ route('employees.index') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 mr-4">
                                Batal
                            </a>
                            <x-primary-button>
                                {{ __('Perbarui Data') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
