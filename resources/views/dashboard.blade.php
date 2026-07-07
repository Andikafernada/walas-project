<x-app-layout>
    <x-slot:title>Dashboard</x-slot>

    <!-- Dashboard Content -->
    <div class="space-y-6">
        <!-- Welcome Section -->
        <div class="bg-gradient-to-r from-indigo-600 to-indigo-700 rounded-2xl p-6 text-white">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold">Selamat Datang, {{ auth()->user()->name }}!</h1>
                    <p class="mt-1 text-indigo-100">Hari ini {{ now()->locale('id')->translatedFormat('l, d F Y') }}</p>
                </div>
                <div class="flex-shrink-0">
                    <div class="bg-white/10 backdrop-blur-sm rounded-lg px-4 py-3 text-center">
                        <p class="text-3xl font-bold">{{ $stats['total_students'] }}</p>
                        <p class="text-xs text-indigo-200">Total Siswa</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <x-stats-card
                title="Total Kelas"
                :value="$stats['total_classes']"
                icon="users"
                color="indigo"
            />

            <x-stats-card
                title="Absensi Hari Ini"
                :value="$stats['today_attendance']['completed'] . '/' . $stats['today_attendance']['total']"
                icon="clipboard"
                color="green"
            />

            <x-stats-card
                title="Pelanggaran Minggu Ini"
                :value="$stats['weekly_violations']"
                icon="warning"
                color="yellow"
            />

            <x-stats-card
                title="Pesan Pending"
                :value="$stats['pending_messages']"
                icon="chat"
                color="blue"
            />
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Classes List -->
            <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900">Kelas Saya</h2>
                    <a href="{{ route('classes.index') }}" class="text-sm text-indigo-600 hover:text-indigo-700 font-medium">
                        Lihat semua
                    </a>
                </div>

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
                        </x-slot>
                    </x-empty-state>
                @else
                    <div class="divide-y divide-gray-100">
                        @foreach($classes->take(5) as $class)
                            <a href="{{ route('classes.show', $class) }}" class="block px-6 py-4 hover:bg-gray-50 transition-colors">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 rounded-lg bg-indigo-100 flex items-center justify-center">
                                            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                            </svg>
                                        </div>
                                        <div class="ml-4">
                                            <p class="text-sm font-medium text-gray-900">{{ $class->name }}</p>
                                            <p class="text-sm text-gray-500">{{ $class->jurusan }} - {{ $class->tingkat }}</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-medium text-gray-900">{{ $class->students()->where('is_active', true)->count() }} siswa</p>
                                        <p class="text-xs text-gray-500">TA {{ $class->school_year_start }}/{{ $class->school_year_end }}</p>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                    @if($classes->count() > 5)
                        <div class="px-6 py-3 bg-gray-50 border-t border-gray-100">
                            <a href="{{ route('classes.index') }}" class="text-sm text-indigo-600 hover:text-indigo-700 font-medium">
                                + {{ $classes->count() - 5 }} kelas lainnya
                            </a>
                        </div>
                    @endif
                @endif
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h2 class="text-lg font-semibold text-gray-900">Aksi Cepat</h2>
                </div>
                <div class="p-4 space-y-2">
                    @foreach($quickActions as $action)
                        <a href="{{ $action['route'] }}"
                           class="flex items-center p-3 rounded-lg hover:bg-gray-50 transition-colors group">
                            <div class="w-10 h-10 rounded-lg bg-indigo-100 flex items-center justify-center group-hover:bg-indigo-200 transition-colors">
                                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900">{{ $action['name'] }}</p>
                                <p class="text-xs text-gray-500">{{ $action['description'] }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Recent Activity Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Recent Attendances -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900">Absensi Terbaru</h2>
                    <a href="{{ route('reports.attendance') }}" class="text-sm text-indigo-600 hover:text-indigo-700 font-medium">
                        Lihat semua
                    </a>
                </div>

                @if($recentAttendances->isEmpty())
                    <div class="p-6 text-center text-gray-500">
                        <svg class="mx-auto w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        <p class="mt-2 text-sm">Belum ada data absensi</p>
                    </div>
                @else
                    <div class="divide-y divide-gray-100">
                        @foreach($recentAttendances as $attendance)
                            <div class="px-6 py-4">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $attendance->student->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $attendance->student->classModel->name ?? '-' }}</p>
                                    </div>
                                    <div class="text-right">
                                        @php
                                            $statusColors = [
                                                'hadir' => 'bg-green-100 text-green-800',
                                                'terlambat' => 'bg-yellow-100 text-yellow-800',
                                                'sakit' => 'bg-blue-100 text-blue-800',
                                                'izin' => 'bg-purple-100 text-purple-800',
                                                'alpa' => 'bg-red-100 text-red-800',
                                            ];
                                            $statusLabels = [
                                                'hadir' => 'Hadir',
                                                'terlambat' => 'Terlambat',
                                                'sakit' => 'Sakit',
                                                'izin' => 'Izin',
                                                'alpa' => 'Alpa',
                                            ];
                                        @endphp
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$attendance->status] ?? 'bg-gray-100 text-gray-800' }}">
                                            {{ $statusLabels[$attendance->status] ?? $attendance->status }}
                                        </span>
                                        <p class="text-xs text-gray-500 mt-1">{{ $attendance->date->format('d/m/Y') }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Recent Violations -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900">Pelanggaran Terbaru</h2>
                    <a href="{{ route('reports.violations') }}" class="text-sm text-indigo-600 hover:text-indigo-700 font-medium">
                        Lihat semua
                    </a>
                </div>

                @if($recentViolations->isEmpty())
                    <div class="p-6 text-center text-gray-500">
                        <svg class="mx-auto w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="mt-2 text-sm">Tidak ada pelanggaran minggu ini</p>
                    </div>
                @else
                    <div class="divide-y divide-gray-100">
                        @foreach($recentViolations as $violation)
                            <div class="px-6 py-4">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $violation->student->name }}</p>
                                        <p class="text-xs text-gray-500">{{ Str::limit($violation->description, 50) }}</p>
                                    </div>
                                    <div class="text-right">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            -{{ $violation->poin_reduced }} poin
                                        </span>
                                        <p class="text-xs text-gray-500 mt-1">{{ $violation->date->format('d/m/Y') }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
