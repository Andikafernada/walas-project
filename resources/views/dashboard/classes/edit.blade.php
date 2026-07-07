<x-app-layout>
    <x-slot:title>Edit Kelas - {{ $class->name }}</x-slot>

    <x-page-header title="Edit Kelas" description="Perbarui informasi kelas">

        <x-slot:action>
            <a href="{{ route('classes.show', $class) }}"
               class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali
            </a>
        </x-slot:action>
    </x-page-header>

    <div class="max-w-2xl">
        <form action="{{ route('classes.update', $class) }}" method="POST" class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            @csrf
            @method('PUT')

            <div class="p-6 space-y-6">
                <!-- Nama Kelas -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        Nama Kelas <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" id="name" value="{{ old('name', $class->name) }}"
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
                        <option value="IPA" {{ old('jurusan', $class->jurusan) == 'IPA' ? 'selected' : '' }}>IPA</option>
                        <option value="IPS" {{ old('jurusan', $class->jurusan) == 'IPS' ? 'selected' : '' }}>IPS</option>
                        <option value="Bahasa" {{ old('jurusan', $class->jurusan) == 'Bahasa' ? 'selected' : '' }}>Bahasa</option>
                        <option value="Agama" {{ old('jurusan', $class->jurusan) == 'Agama' ? 'selected' : '' }}>Agama</option>
                        <option value="Teknik" {{ old('jurusan', $class->jurusan) == 'Teknik' ? 'selected' : '' }}>Teknik</option>
                        <option value="Bisnis" {{ old('jurusan', $class->jurusan) == 'Bisnis' ? 'selected' : '' }}>Bisnis & Manajemen</option>
                        <option value="Kesehatan" {{ old('jurusan', $class->jurusan) == 'Kesehatan' ? 'selected' : '' }}>Kesehatan</option>
                        <option value="Umum" {{ old('jurusan', $class->jurusan) == 'Umum' ? 'selected' : '' }}>Umum</option>
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
                        <option value="X" {{ old('tingkat', $class->tingkat) == 'X' ? 'selected' : '' }}>Kelas X (Sepuluh)</option>
                        <option value="XI" {{ old('tingkat', $class->tingkat) == 'XI' ? 'selected' : '' }}>Kelas XI (Sebelas)</option>
                        <option value="XII" {{ old('tingkat', $class->tingkat) == 'XII' ? 'selected' : '' }}>Kelas XII (Dua Belas)</option>
                        <option value="XIII" {{ old('tingkat', $class->tingkat) == 'XIII' ? 'selected' : '' }}>Kelas XIII</option>
                    </select>
                    @error('tingkat')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Alias -->
                <div>
                    <label for="alias" class="block text-sm font-medium text-gray-700 mb-2">
                        Alias / Singkatan
                    </label>
                    <input type="text" name="alias" id="alias" value="{{ old('alias', $class->alias) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                           placeholder="Contoh: XIPA1">
                </div>
            </div>

            <!-- Footer -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex items-center justify-between">
                <form action="{{ route('classes.destroy', $class) }}" method="POST" class="inline"
                      onsubmit="return confirm('PERHATIAN: Menghapus kelas akan menghapus semua data terkait. Lanjutkan?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 text-red-600 text-sm font-medium rounded-lg hover:bg-red-50">
                        Hapus Kelas
                    </button>
                </form>

                <div class="flex items-center space-x-3">
                    <a href="{{ route('classes.show', $class) }}" class="px-4 py-2 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-100">
                        Batal
                    </a>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700">
                        Simpan Perubahan
                    </button>
                </div>
            </div>
        </form>
    </div>
</x-app-layout>
