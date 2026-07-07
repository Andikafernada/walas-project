<x-app-layout>
    <x-slot:title>Buat Kelas Baru</x-slot>

    <x-page-header title="Buat Kelas Baru" description="Tambah kelas baru yang akan Anda ampu">

        <x-slot:action>
            <a href="{{ route('classes.index') }}"
               class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali
            </a>
        </x-slot:action>
    </x-page-header>

    <div class="max-w-2xl">
        <form action="{{ route('classes.store') }}" method="POST" class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            @csrf

            <div class="p-6 space-y-6">
                <!-- Nama Kelas -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        Nama Kelas <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                           placeholder="Contoh: X IPA 1" required>
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Jurusan -->
                <div>
                    <label for="jurusan" class="block text-sm font-medium text-gray-700 mb-2">
                        Jurusan <span class="text-red-500">*</span>
                    </label>
                    <select name="jurusan" id="jurusan"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required>
                        <option value="">Pilih Jurusan</option>
                        <option value="IPA" {{ old('jurusan') == 'IPA' ? 'selected' : '' }}>IPA</option>
                        <option value="IPS" {{ old('jurusan') == 'IPS' ? 'selected' : '' }}>IPS</option>
                        <option value="Bahasa" {{ old('jurusan') == 'Bahasa' ? 'selected' : '' }}>Bahasa</option>
                        <option value="Agama" {{ old('jurusan') == 'Agama' ? 'selected' : '' }}>Agama</option>
                        <option value="Teknik" {{ old('jurusan') == 'Teknik' ? 'selected' : '' }}>Teknik</option>
                        <option value="Bisnis" {{ old('jurusan') == 'Bisnis' ? 'selected' : '' }}>Bisnis & Manajemen</option>
                        <option value="Kesehatan" {{ old('jurusan') == 'Kesehatan' ? 'selected' : '' }}>Kesehatan</option>
                        <option value="Umum" {{ old('jurusan') == 'Umum' ? 'selected' : '' }}>Umum</option>
                    </select>
                    @error('jurusan')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tingkat -->
                <div>
                    <label for="tingkat" class="block text-sm font-medium text-gray-700 mb-2">
                        Tingkat <span class="text-red-500">*</span>
                    </label>
                    <select name="tingkat" id="tingkat"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required>
                        <option value="">Pilih Tingkat</option>
                        <option value="X" {{ old('tingkat') == 'X' ? 'selected' : '' }}>Kelas X (Sepuluh)</option>
                        <option value="XI" {{ old('tingkat') == 'XI' ? 'selected' : '' }}>Kelas XI (Sebelas)</option>
                        <option value="XII" {{ old('tingkat') == 'XII' ? 'selected' : '' }}>Kelas XII (Dua Belas)</option>
                        <option value="XIII" {{ old('tingkat') == 'XIII' ? 'selected' : '' }}>Kelas XIII ( Tiga Belas)</option>
                    </select>
                    @error('tingkat')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tahun Ajaran -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="school_year_start" class="block text-sm font-medium text-gray-700 mb-2">
                            Tahun Ajaran Mulai
                        </label>
                        <input type="number" name="school_year_start" id="school_year_start"
                               value="{{ old('school_year_start', date('Y')) }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                               min="2020" max="2030">
                        @error('school_year_start')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="school_year_end" class="block text-sm font-medium text-gray-700 mb-2">
                            Tahun Ajaran Selesai
                        </label>
                        <input type="number" name="school_year_end" id="school_year_end"
                               value="{{ old('school_year_end', date('Y') + 1) }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                               min="2020" max="2030">
                        @error('school_year_end')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Alias -->
                <div>
                    <label for="alias" class="block text-sm font-medium text-gray-700 mb-2">
                        Alias / Singkatan
                    </label>
                    <input type="text" name="alias" id="alias" value="{{ old('alias') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                           placeholder="Contoh: XIPA1">
                    <p class="mt-1 text-xs text-gray-500">Opsional. Digunakan untuk shortcut.</p>
                </div>
            </div>

            <!-- Footer -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex items-center justify-end space-x-3">
                <a href="{{ route('classes.index') }}" class="px-4 py-2 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-100">
                    Batal
                </a>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700">
                    Simpan Kelas
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
