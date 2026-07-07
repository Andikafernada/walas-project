<x-app-layout>
    <x-slot:title>Laporan Absensi</x-slot>

    <x-page-header title="Laporan Absensi" description="Ringkasan absensi siswa">

        <x-slot:action>
            <a href="{{ route('reports.attendance.export') }}"
               class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700">
                Export Excel
            </a>
        </x-slot:action>
    </x-page-header>

    <!-- Filter -->
    <form action="{{ route('reports.attendance') }}" method="GET" class="mb-6 bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Kelas</label>
                <select name="class_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                    <option value="">Semua Kelas</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Mulai</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Akhir</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
            </div>
            <div class="flex items-end">
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">Filter</button>
            </div>
        </div>
    </form>

    <!-- Attendance Table -->
    @if($attendances->isEmpty())
        <x-empty-state title="Tidak ada data" description="Tidak ada data absensi dengan filter tersebut" icon="clipboard"/>
    @else
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Siswa</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kelas</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($attendances as $attendance)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $attendance->date->format('d/m/Y') }}</td>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $attendance->student->name ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $attendance->student->classModel->name ?? '-' }}</td>
                            <td class="px-6 py-4">
                                @php
                                    $statusConfig = [
                                        'hadir' => ['bg-green-100 text-green-800', 'Hadir'],
                                        'terlambat' => ['bg-yellow-100 text-yellow-800', 'Terlambat'],
                                        'sakit' => ['bg-blue-100 text-blue-800', 'Sakit'],
                                        'izin' => ['bg-purple-100 text-purple-800', 'Izin'],
                                        'alpa' => ['bg-red-100 text-red-800', 'Alfa'],
                                    ];
                                    $config = $statusConfig[$attendance->status] ?? ['bg-gray-100 text-gray-800', $attendance->status];
                                @endphp
                                <span class="px-2 py-1 text-xs font-medium rounded-full {{ $config[0] }}">{{ $config[1] }}</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            @if($attendances->hasPages())
                <div class="px-6 py-4 border-t">{{ $attendances->links() }}</div>
            @endif
        </div>
    @endif
</x-app-layout>
