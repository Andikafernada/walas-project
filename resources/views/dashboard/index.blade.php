@extends('layouts.app')

@section('title', 'Dashboard')

@php
    $navItems = [
        ['label' => 'Dashboard', 'url' => '/dashboard', 'icon' => 'heroicons-o-home', 'active' => request()->is('dashboard')],
        ['label' => 'Absensi', 'url' => '#', 'icon' => 'heroicons-o-clipboard-check', 'active' => request()->is('*attendance*')],
        ['label' => 'Siswa', 'url' => '#', 'icon' => 'heroicons-o-users', 'active' => request()->is('*students*')],
        ['label' => 'Jadwal', 'url' => '#', 'icon' => 'heroicons-o-calendar', 'active' => request()->is('*schedules*')],
        ['label' => 'Pelanggaran', 'url' => '#', 'icon' => 'heroicons-o-exclamation-circle', 'active' => request()->is('*violations*')],
        ['label' => 'Kas Kelas', 'url' => '#', 'icon' => 'heroicons-o-cash', 'active' => request()->is('*cash*')],
        ['label' => 'Jurnal BK', 'url' => '#', 'icon' => 'heroicons-o-book-open', 'active' => request()->is('*journals*')],
        ['label' => 'Pesan WA', 'url' => '#', 'icon' => 'heroicons-o-chat', 'badge' => $stats['pending_messages'] ?? 0],
    ];
@endphp

@section('content')
<div class="p-4 lg:p-6 max-w-7xl mx-auto space-y-6">
    {{-- Welcome Header --}}
    <div class="bg-gradient-to-r from-blue-600 to-blue-700 rounded-2xl p-6 text-white">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold mb-1">Selamat Datang, {{ auth()->user()->name }}! 👋</h1>
                <p class="text-blue-100">{{ auth()->user()->school_name ?? 'SMK Indonesia' }}</p>
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

        <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-{{ $stats['today_attendance_done'] ? 'emerald' : 'gray' }}-100 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-{{ $stats['today_attendance_done'] ? 'emerald' : 'gray' }}-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2"/>
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['today_attendance_done'] ? 'Sudah' : 'Belum' }}</p>
                    <p class="text-xs text-gray-500">Absensi Hari Ini</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="grid lg:grid-cols-2 gap-6">
        {{-- Classes List --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
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
                                <p class="text-sm text-gray-500">{{ $class->jurusan }}</p>
                            </div>
                            <div class="flex items-center gap-4">
                                <div class="text-right">
                                    <p class="text-sm font-medium text-gray-800">{{ $class->student_count }} siswa</p>
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
            <div class="p-4 grid grid-cols-2 gap-3">
                <a href="#" class="p-4 bg-blue-50 hover:bg-blue-100 rounded-xl text-center transition">
                    <svg class="w-8 h-8 text-blue-600 mx-auto mb-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                    <p class="font-medium text-blue-800 text-sm">Buat Magic Link</p>
                </a>
                <a href="#" class="p-4 bg-emerald-50 hover:bg-emerald-100 rounded-xl text-center transition">
                    <svg class="w-8 h-8 text-emerald-600 mx-auto mb-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-5.682 2.022M3 12c0-4.418 4.03-8 9-8 4.97 0 8.682 3.604 9 8 2.022"/>
                    </svg>
                    <p class="font-medium text-emerald-800 text-sm">Kirim Pesan WA</p>
                </a>
                <a href="#" class="p-4 bg-amber-50 hover:bg-amber-100 rounded-xl text-center transition">
                    <svg class="w-8 h-8 text-amber-600 mx-auto mb-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16.646c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <p class="font-medium text-amber-800 text-sm">Catat Pelanggaran</p>
                </a>
                <a href="#" class="p-4 bg-purple-50 hover:bg-purple-100 rounded-xl text-center transition">
                    <svg class="w-8 h-8 text-purple-600 mx-auto mb-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <p class="font-medium text-purple-800 text-sm">Lihat Rapot</p>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
