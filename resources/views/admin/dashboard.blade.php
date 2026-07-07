@extends('admin.layouts.app')

@section('title', 'Dashboard - Admin Walas')
@section('header', 'Dashboard')

@section('content')
    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-2xl p-6 border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Total Organisasi</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ $stats['total_organizations'] }}</p>
                </div>
                <div class="w-12 h-12 bg-primary-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm">
                <span class="text-green-500 flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
                    </svg>
                    {{ $stats['active_organizations'] }}
                </span>
                <span class="text-gray-400 ml-2">aktif</span>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-6 border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Total Wali Kelas</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ $stats['total_walas'] }}</p>
                </div>
                <div class="w-12 h-12 bg-accent-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-accent-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm text-gray-500">
                Dari {{ $stats['total_organizations'] }} organisasi
            </div>
        </div>

        <div class="bg-white rounded-2xl p-6 border border-gray-200">
            <div class="flex items-center gap-4">
                <div>
                    <p class="text-sm text-gray-500">Total Siswa</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ number_format($stats['total_students']) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-6 border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Pendaftaran Bulan Ini</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ $stats['new_this_month'] }}</p>
                </div>
                <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Organizations -->
    <div class="bg-white rounded-2xl border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900">Organisasi Terbaru</h2>
                <a href="{{ route('admin.organizations.index') }}" class="text-sm text-primary-600 hover:text-primary-700">Lihat semua</a>
            </div>
        </div>
        <div class="divide-y divide-gray-100">
            @forelse($recentOrganizations as $org)
                <div class="px-6 py-4 flex items-center justify-between hover:bg-gray-50">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 bg-primary-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">{{ $org->name }}</p>
                            <p class="text-sm text-gray-500">{{ $org->city }} &bull; {{ $org->type_label }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="text-right">
                            <p class="text-sm font-medium text-gray-900">{{ $org->users_count }} pengguna</p>
                            <p class="text-xs text-gray-500">{{ $org->created_at->diffForHumans() }}</p>
                        </div>
                        <span class="px-3 py-1 text-xs font-medium rounded-full {{ $org->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                            {{ ucfirst($org->status) }}
                        </span>
                    </div>
                </div>
            @empty
                <div class="px-6 py-8 text-center text-gray-500">
                    <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    <p>Belum ada organisasi terdaftar</p>
                </div>
            @endforelse
        </div>
    </div>
@endsection
