@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="p-4 lg:p-6 max-w-7xl mx-auto space-y-6">
    {{-- Welcome Header --}}
    <div class="bg-gradient-to-r from-blue-600 to-blue-700 rounded-2xl p-6 text-white">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold mb-1">Selamat Datang, {{ auth()->user()->name }}! 👋</h1>
                <p class="text-blue-100">{{ auth()->user()->organization?->name ?? 'Walas User' }}</p>
            </div>
            <div class="text-right">
                <p class="text-3xl font-bold">{{ now()->format('d') }}</p>
                <p class="text-blue-200 text-sm">{{ now()->translatedFormat('l, F Y') }}</p>
            </div>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H5a2 2 0 00-2 2v16m14 0h2m-2 0h-2M7 21v-4m0 0v4m0-4h4m-4 0h4m-4-4v4"/>
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['total_classes'] }}</p>
                    <p class="text-xs text-gray-500">Total Kelas</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-emerald-100 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-4.172-3.534 1.408.636-1.408 3H5a2 2 0 01-2-2v-4a2 2 0 012-2h5m12 0v14H5a2 2 0 01-2-2v-4a2 2 0 012-2h2"/>
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['total_students'] }}</p>
                    <p class="text-xs text-gray-500">Total Siswa</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-red-100 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16.646c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['weekly_violations'] ?? 0 }}</p>
                    <p class="text-xs text-gray-500">Pelanggaran Minggu Ini</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-amber-100 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-5.682 2.022M3 12c0-4.418 4.03-8 9-8 4.97 0 8.682 3.604 9 8 2.022"/>
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['pending_messages'] }}</p>
                    <p class="text-xs text-gray-500">Pesan Pending</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Content Grid --}}
    <div class="grid lg:grid-cols-3 gap-6">
        {{-- Classes List --}}
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="p-4 border-b border-gray-100 flex items-center justify-between">
                <h2 class="font-bold text-gray-800">Kelas Saya</h2>
                <a href="{{ route('classes.create') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">
                    + Tambah Kelas
                </a>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($classes as $class)
                    <a href="{{ route('classes.show', $class) }}" class="block p-4 hover:bg-gray-50 transition">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="font-semibold text-gray-800">{{ $class->name }}</h3>
                                <p class="text-sm text-gray-500">{{ $class->jurusan ?? 'Tidak ada jurusan' }}</p>
                            </div>
                            <div class="flex items-center gap-4">
                                <div class="text-right">
                                    <p class="text-sm font-medium text-gray-800">{{ $class->students()->where('is_active', true)->count() }} siswa</p>
                                    <p class="text-xs text-gray-500">{{ $class->school_year_start }}/{{ $class->school_year_end }}</p>
                                </div>
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                                </svg>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="p-8 text-center text-gray-500">
                        <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16"/>
                        </svg>
                        <p>Belum ada kelas</p>
                        <a href="{{ route('classes.create') }}" class="text-blue-600 hover:underline">+ Tambah kelas pertama</a>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Quick Actions --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="p-4 border-b border-gray-100">
                <h2 class="font-bold text-gray-800">Aksi Cepat</h2>
            </div>
            <div class="p-4 space-y-3">
                <a href="{{ route('classes.index') }}" class="flex items-center p-3 bg-blue-50 hover:bg-blue-100 rounded-xl transition">
                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-medium text-blue-800">Buat Absensi</p>
                        <p class="text-xs text-blue-600">Generate magic link WA</p>
                    </div>
                </a>
                <a href="{{ route('classes.index') }}" class="flex items-center p-3 bg-emerald-50 hover:bg-emerald-100 rounded-xl transition">
                    <div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-medium text-emerald-800">Tambah Siswa</p>
                        <p class="text-xs text-emerald-600">Registrasi siswa baru</p>
                    </div>
                </a>
                <a href="{{ route('wa-queue.index') }}" class="flex items-center p-3 bg-purple-50 hover:bg-purple-100 rounded-xl transition">
                    <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-5.682 2.022M3 12c0-4.418 4.03-8 9-8 4.97 0 8.682 3.604 9 8 2.022"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-medium text-purple-800">Kirim Pesan WA</p>
                        <p class="text-xs text-purple-600">Bulk message ke orang tua</p>
                    </div>
                </a>
                <a href="{{ route('reports.attendance') }}" class="flex items-center p-3 bg-amber-50 hover:bg-amber-100 rounded-xl transition">
                    <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-medium text-amber-800">Lihat Laporan</p>
                        <p class="text-xs text-amber-600">Summary laporan</p>
                    </div>
                </a>
            </div>
        </div>
    </div>

    {{-- Recent Activity --}}
    <div class="grid lg:grid-cols-2 gap-6">
        {{-- Recent Attendances --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="p-4 border-b border-gray-100 flex items-center justify-between">
                <h2 class="font-bold text-gray-800">Absensi Terbaru</h2>
                <a href="{{ route('reports.attendance') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">Lihat semua</a>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($recentAttendances as $attendance)
                    <div class="p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="font-medium text-gray-800">{{ $attendance->student->name ?? 'Unknown' }}</p>
                                <p class="text-sm text-gray-500">{{ $attendance->student->classModel->name ?? '-' }}</p>
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
                @empty
                    <div class="p-8 text-center text-gray-500">
                        <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        <p>Belum ada data absensi</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Recent Violations --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="p-4 border-b border-gray-100 flex items-center justify-between">
                <h2 class="font-bold text-gray-800">Pelanggaran Terbaru</h2>
                <a href="{{ route('reports.violations') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">Lihat semua</a>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($recentViolations as $violation)
                    <div class="p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="font-medium text-gray-800">{{ $violation->student->name ?? 'Unknown' }}</p>
                                <p class="text-sm text-gray-500">{{ Str::limit($violation->description, 40) }}</p>
                            </div>
                            <div class="text-right">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    -{{ $violation->poin_reduced }} poin
                                </span>
                                <p class="text-xs text-gray-500 mt-1">{{ $violation->date->format('d/m/Y') }}</p>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-gray-500">
                        <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p>Tidak ada pelanggaran</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
