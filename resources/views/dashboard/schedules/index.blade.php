<x-app-layout>
    <x-slot:title>Jadwal Pelajaran - {{ $class->name }}</x-slot>

    <!-- Breadcrumb -->
    <nav class="mb-4 flex items-center text-sm text-gray-500">
        <a href="{{ route('dashboard') }}" class="hover:text-indigo-600">Dashboard</a>
        <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <a href="{{ route('classes.show', $class) }}" class="hover:text-indigo-600">{{ $class->name }}</a>
        <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <span>Jadwal</span>
    </nav>

    <x-page-header title="Jadwal Pelajaran" description="{{ $class->name }}">

        <x-slot:action>
            <button @click="$dispatch('open-modal', { name: 'add-schedule' })"
                    class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700">
                + Tambah Jadwal
            </button>
        </x-slot:action>
    </x-page-header>

    <!-- Schedule by Day -->
    @php
        $days = ['senin' => 'Senin', 'selasa' => 'Selasa', 'rabu' => 'Rabu', 'kamis' => 'Kamis', 'jumat' => 'Jumat', 'sabtu' => 'Sabtu'];
        $groupedSchedules = $schedules->groupBy('day');
    @endphp

    <div class="space-y-6">
        @foreach($days as $dayKey => $dayName)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-900">{{ $dayName }}</h3>
                </div>
                @if($groupedSchedules->has($dayKey))
                    <div class="divide-y divide-gray-100">
                        @foreach($groupedSchedules[$dayKey]->sortBy('start_time') as $schedule)
                            <div class="px-6 py-4 flex items-center justify-between">
                                <div>
                                    <p class="font-medium text-gray-900">{{ $schedule->subject }}</p>
                                    <p class="text-sm text-gray-500">{{ $schedule->teacher_name ?? 'Guru belum ditentukan' }}</p>
                                </div>
                                <div class="flex items-center space-x-4">
                                    <span class="text-sm text-gray-600">{{ $schedule->start_time }} - {{ $schedule->end_time }}</span>
                                    <span class="text-xs text-gray-400">{{ $schedule->duration }} menit</span>
                                    <form action="{{ route('classes.schedules.destroy', [$class, $schedule]) }}" method="POST" class="inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 text-sm">Hapus</button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="px-6 py-4 text-gray-500 text-sm">Belum ada jadwal</div>
                @endif
            </div>
        @endforeach
    </div>

    <!-- Add Schedule Modal -->
    <x-modal name="add-schedule" title="Tambah Jadwal" maxWidth="md">
        <form action="{{ route('classes.schedules.store', $class) }}" method="POST">
            @csrf
            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Hari</label>
                        <select name="day" required class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                            @foreach($days as $key => $name)
                                <option value="{{ $key }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Mata Pelajaran</label>
                        <input type="text" name="subject" required class="w-full px-4 py-2 border border-gray-300 rounded-lg" placeholder="Matematika">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Jam Mulai</label>
                        <input type="time" name="start_time" required class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Jam Selesai</label>
                        <input type="time" name="end_time" required class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Guru</label>
                    <input type="text" name="teacher_name" class="w-full px-4 py-2 border border-gray-300 rounded-lg" placeholder="Nama guru">
                </div>
            </div>
            <div class="flex justify-end space-x-3 mt-6">
                <button type="button" @click="show = false" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">Batal</button>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">Simpan</button>
            </div>
        </form>
    </x-modal>
</x-app-layout>
