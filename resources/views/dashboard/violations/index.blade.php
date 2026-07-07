<x-app-layout>
    <x-slot:title>Daftar Pelanggaran - {{ $class->name }}</x-slot>

    <!-- Breadcrumb -->
    <nav class="mb-4 flex items-center text-sm text-gray-500">
        <a href="{{ route('dashboard') }}" class="hover:text-indigo-600">Dashboard</a>
        <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <a href="{{ route('classes.show', $class) }}" class="hover:text-indigo-600">{{ $class->name }}</a>
        <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <span>Pelanggaran</span>
    </nav>

    <x-page-header title="Daftar Pelanggaran" description="{{ $class->name }}">

        <x-slot:action>
            <button @click="$dispatch('open-modal', { name: 'add-violation' })"
                    class="px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700">
                + Catat Pelanggaran
            </button>
        </x-slot:action>
    </x-page-header>

    <!-- Filter -->
    <form action="{{ route('classes.violations.index', $class) }}" method="GET" class="mb-6 flex flex-wrap gap-4">
        <select name="student_id" class="px-4 py-2 border border-gray-300 rounded-lg">
            <option value="">Semua Siswa</option>
            @foreach($students as $student)
                <option value="{{ $student->id }}" {{ request('student_id') == $student->id ? 'selected' : '' }}>{{ $student->name }}</option>
            @endforeach
        </select>
        <select name="severity" class="px-4 py-2 border border-gray-300 rounded-lg">
            <option value="">Semua Tingkat</option>
            <option value="ringan" {{ request('severity') == 'ringan' ? 'selected' : '' }}>Ringan</option>
            <option value="sedang" {{ request('severity') == 'sedang' ? 'selected' : '' }}>Sedang</option>
            <option value="berat" {{ request('severity') == 'berat' ? 'selected' : '' }}>Berat</option>
        </select>
        <button type="submit" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">Filter</button>
    </form>

    <!-- Violations Table -->
    @if($violations->isEmpty())
        <x-empty-state title="Belum ada pelanggaran" description="Tidak ada data pelanggaran" icon="check"/>
    @else
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Siswa</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kategori</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Keterangan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Poin</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($violations as $violation)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $violation->date->format('d/m/Y') }}</td>
                            <td class="px-6 py-4">
                                <p class="text-sm font-medium text-gray-900">{{ $violation->student->name }}</p>
                                <p class="text-xs text-gray-500">Poin: {{ $violation->poin_after }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs font-medium rounded-full
                                    {{ $violation->severity === 'berat' ? 'bg-red-100 text-red-800' : ($violation->severity === 'sedang' ? 'bg-yellow-100 text-yellow-800' : 'bg-orange-100 text-orange-800') }}">
                                    {{ $violation->severity }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ Str::limit($violation->description, 40) }}</td>
                            <td class="px-6 py-4 text-sm font-medium text-red-600">-{{ $violation->poin_reduced }}</td>
                            <td class="px-6 py-4 text-right">
                                <form action="{{ route('classes.violations.destroy', [$class, $violation]) }}" method="POST" class="inline"
                                      onsubmit="return confirm('Hapus pelanggaran ini? Poin akan dikembalikan.')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            @if($violations->hasPages())
                <div class="px-6 py-4 border-t">{{ $violations->links() }}</div>
            @endif
        </div>
    @endif

    <!-- Add Violation Modal -->
    <x-modal name="add-violation" title="Catat Pelanggaran" maxWidth="lg">
        <form action="{{ route('classes.violations.store', $class) }}" method="POST">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Siswa</label>
                    <select name="student_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        <option value="">Pilih Siswa</option>
                        @foreach($students as $student)
                            <option value="{{ $student->id }}">{{ $student->name }} (Poin: {{ $student->poin }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tingkat</label>
                        <select name="severity" required class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                            <option value="ringan">Ringan (-5 poin)</option>
                            <option value="sedang">Sedang (-10 poin)</option>
                            <option value="berat">Berat (-15 poin)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal</label>
                        <input type="date" name="date" value="{{ date('Y-m-d') }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Kategori</label>
                    <select name="category" required class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        @foreach(\App\Models\Violation::CATEGORIES as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                    <textarea name="description" rows="3" required class="w-full px-4 py-2 border border-gray-300 rounded-lg" placeholder="Jelaskan pelanggaran..."></textarea>
                </div>
            </div>
            <div class="flex justify-end space-x-3 mt-6">
                <button type="button" @click="show = false" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">Batal</button>
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">Simpan</button>
            </div>
        </form>
    </x-modal>
</x-app-layout>
