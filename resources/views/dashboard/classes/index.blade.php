<x-app-layout>
    <x-slot:title>Daftar Kelas</x-slot>

    <x-page-header title="Daftar Kelas" description="Kelola kelas yang Anda ampu">

        <x-slot:action>
            <a href="{{ route('classes.create') }}"
               class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Kelas
            </a>
        </x-slot:action>
    </x-page-header>

    <!-- Classes Grid -->
    @if($classes->isEmpty())
        <x-empty-state
            title="Belum ada kelas"
            description="Mulai dengan membuat kelas pertama Anda"
            icon="users"
        >
            <x-slot:action>
                <a href="{{ route('classes.create') }}"
                   class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Buat Kelas
                </a>
            </x-slot:action>
        </x-empty-state>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($classes as $class)
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow">
                    <!-- Header -->
                    <div class="p-6 bg-gradient-to-r from-indigo-500 to-indigo-600 text-white">
                        <div class="flex items-start justify-between">
                            <div>
                                <h3 class="text-lg font-bold">{{ $class->name }}</h3>
                                <p class="text-indigo-100 text-sm">{{ $class->jurusan }}</p>
                            </div>
                            <span class="px-2 py-1 bg-white/20 rounded-full text-xs font-medium">
                                {{ $class->tingkat }}
                            </span>
                        </div>
                    </div>

                    <!-- Stats -->
                    <div class="px-6 py-4 border-b border-gray-100">
                        <div class="grid grid-cols-2 gap-4 text-center">
                            <div>
                                <p class="text-2xl font-bold text-gray-900">{{ $class->students()->where('is_active', true)->count() }}</p>
                                <p class="text-xs text-gray-500">Siswa</p>
                            </div>
                            <div>
                                <p class="text-2xl font-bold text-gray-900">{{ $class->schedules()->count() }}</p>
                                <p class="text-xs text-gray-500">Jadwal</p>
                            </div>
                        </div>
                    </div>

                    <!-- Info -->
                    <div class="px-6 py-3 bg-gray-50">
                        <p class="text-xs text-gray-500">
                            TA {{ $class->school_year_start }}/{{ $class->school_year_end }}
                        </p>
                    </div>

                    <!-- Actions -->
                    <div class="px-6 py-4 flex items-center justify-between">
                        <a href="{{ route('classes.show', $class) }}"
                           class="text-indigo-600 hover:text-indigo-700 text-sm font-medium">
                            Lihat Detail
                        </a>
                        <div class="flex items-center space-x-2">
                            <a href="{{ route('classes.edit', $class) }}"
                               class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                </svg>
                            </a>
                            <form action="{{ route('classes.destroy', $class) }}" method="POST" class="inline"
                                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus kelas ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</x-app-layout>
