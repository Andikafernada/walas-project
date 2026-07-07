<x-app-layout>
    <x-slot:title>Profil Siswa - {{ $student->name }}</x-slot>

    <x-page-header title="Profil Siswa" :description="$student->name">

        <x-slot:action>
            <a href="{{ route('classes.students.index', $class) }}"
               class="px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200">
                Kembali
            </a>
        </x-slot:action>
    </x-page-header>

    <!-- Student Info -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-6">
        <div class="p-6">
            <div class="flex items-start space-x-6">
                <div class="w-20 h-20 rounded-full bg-indigo-100 flex items-center justify-center">
                    <span class="text-2xl font-bold text-indigo-600">{{ substr($student->name, 0, 1) }}</span>
                </div>
                <div class="flex-1">
                    <h2 class="text-xl font-bold text-gray-900">{{ $student->name }}</h2>
                    <p class="text-gray-500">{{ $class->name }} | NISN: {{ $student->nisn ?? '-' }}</p>
                    <div class="mt-2 flex items-center space-x-4">
                        <span class="px-2 py-1 text-xs font-medium rounded-full {{ $student->gender === 'laki-laki' ? 'bg-blue-100 text-blue-800' : 'bg-pink-100 text-pink-800' }}">
                            {{ $student->gender === 'laki-laki' ? 'Laki-laki' : 'Perempuan' }}
                        </span>
                        <span class="px-2 py-1 text-xs font-medium rounded-full
                            {{ $student->poin >= 80 ? 'bg-green-100 text-green-800' : ($student->poin >= 60 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                            Poin: {{ $student->poin }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="px-6 py-4 bg-gray-50 border-t grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
            <div>
                <p class="text-gray-500">Nama Ayah</p>
                <p class="font-medium text-gray-900">{{ $student->father_name ?? '-' }}</p>
            </div>
            <div>
                <p class="text-gray-500">Nama Ibu</p>
                <p class="font-medium text-gray-900">{{ $student->mother_name ?? '-' }}</p>
            </div>
            <div>
                <p class="text-gray-500">No. WhatsApp</p>
                <p class="font-medium text-gray-900">{{ $student->parent_whatsapp ?? '-' }}</p>
            </div>
            <div>
                <p class="text-gray-500">Tanggal Lahir</p>
                <p class="font-medium text-gray-900">{{ $student->birth_date?->format('d/m/Y') ?? '-' }}</p>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 md:grid-cols-6 gap-4 mb-6">
        <div class="bg-green-50 rounded-xl p-4 border border-green-200">
            <p class="text-2xl font-bold text-green-700">{{ $stats['present'] }}</p>
            <p class="text-xs text-green-600">Hadir</p>
        </div>
        <div class="bg-yellow-50 rounded-xl p-4 border border-yellow-200">
            <p class="text-2xl font-bold text-yellow-700">{{ $stats['late'] }}</p>
            <p class="text-xs text-yellow-600">Terlambat</p>
        </div>
        <div class="bg-blue-50 rounded-xl p-4 border border-blue-200">
            <p class="text-2xl font-bold text-blue-700">{{ $stats['sick'] }}</p>
            <p class="text-xs text-blue-600">Sakit</p>
        </div>
        <div class="bg-purple-50 rounded-xl p-4 border border-purple-200">
            <p class="text-2xl font-bold text-purple-700">{{ $stats['permit'] }}</p>
            <p class="text-xs text-purple-600">Izin</p>
        </div>
        <div class="bg-red-50 rounded-xl p-4 border border-red-200">
            <p class="text-2xl font-bold text-red-700">{{ $stats['absent'] }}</p>
            <p class="text-xs text-red-600">Alfa</p>
        </div>
        <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
            <p class="text-2xl font-bold text-gray-700">{{ $stats['total_violations'] }}</p>
            <p class="text-xs text-gray-600">Pelanggaran</p>
        </div>
    </div>

    <!-- Recent Attendance -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-900">Riwayat Absensi Terbaru</h3>
        </div>
        @if($recentAttendances->isEmpty())
            <div class="p-6 text-center text-gray-500">Belum ada data absensi</div>
        @else
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($recentAttendances as $attendance)
                        <tr>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $attendance->date->format('d/m/Y') }}</td>
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
        @endif
    </div>
</x-app-layout>
