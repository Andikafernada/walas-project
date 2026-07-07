@extends('admin.layouts.app')

@section('title', 'Kelola Organisasi - Admin Walas')
@section('header', 'Kelola Organisasi')

@section('header_actions')
    <a href="{{ route('admin.organizations.export') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
        </svg>
        Export
    </a>
@endsection

@section('content')
    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl p-4 border border-gray-200">
            <p class="text-sm text-gray-500">Total</p>
            <p class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-white rounded-xl p-4 border border-gray-200">
            <p class="text-sm text-gray-500">Aktif</p>
            <p class="text-2xl font-bold text-green-600">{{ $stats['active'] }}</p>
        </div>
        <div class="bg-white rounded-xl p-4 border border-gray-200">
            <p class="text-sm text-gray-500">Pending</p>
            <p class="text-2xl font-bold text-amber-600">{{ $stats['pending'] }}</p>
        </div>
        <div class="bg-white rounded-xl p-4 border border-gray-200">
            <p class="text-sm text-gray-500">Total Walas</p>
            <p class="text-2xl font-bold text-primary-600">{{ $stats['total_users'] }}</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl border border-gray-200 mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <form method="GET" class="flex flex-wrap gap-4">
                <div class="flex-1 min-w-[200px]">
                    <div class="relative">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari organisasi..."
                            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                </div>
                <select name="type" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                    <option value="all">Semua Jenjang</option>
                    <option value="sd" {{ request('type') === 'sd' ? 'selected' : '' }}>SD</option>
                    <option value="smp" {{ request('type') === 'smp' ? 'selected' : '' }}>SMP</option>
                    <option value="sma" {{ request('type') === 'sma' ? 'selected' : '' }}>SMA</option>
                    <option value="smk" {{ request('type') === 'smk' ? 'selected' : '' }}>SMK</option>
                    <option value="others" {{ request('type') === 'others' ? 'selected' : '' }}>Lainnya</option>
                </select>
                <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                    <option value="all">Semua Status</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                    <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Suspended</option>
                </select>
                <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
                    Filter
                </button>
                <a href="{{ route('admin.organizations.index') }}" class="px-4 py-2 text-gray-600 hover:text-gray-900">
                    Reset
                </a>
            </form>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Organisasi</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenjang</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Wali Kelas</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bergabung</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($organizations as $org)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-primary-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">{{ $org->name }}</p>
                                    <p class="text-sm text-gray-500">{{ $org->city }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-700 rounded">
                                {{ $org->type_label }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <span class="font-medium text-gray-900">{{ $org->users_count }}</span>
                                <span class="text-sm text-gray-500">pengguna</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <form action="{{ route('admin.organizations.update-status', $org) }}" method="POST" class="inline">
                                @csrf
                                @method('PATCH')
                                <select name="status" onchange="this.form.submit()"
                                    class="px-2 py-1 text-xs font-medium rounded-full cursor-pointer {{ $org->status === 'active' ? 'bg-green-100 text-green-700' : ($org->status === 'pending' ? 'bg-amber-100 text-amber-700' : 'bg-gray-100 text-gray-600') }}">
                                    <option value="active" {{ $org->status === 'active' ? 'selected' : '' }}>Aktif</option>
                                    <option value="pending" {{ $org->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="inactive" {{ $org->status === 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                                    <option value="suspended" {{ $org->status === 'suspended' ? 'selected' : '' }}>Suspended</option>
                                </select>
                            </form>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ $org->created_at->format('d M Y') }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('admin.organizations.show', $org) }}"
                                class="inline-flex items-center gap-1 px-3 py-1.5 text-sm text-primary-600 hover:bg-primary-50 rounded-lg transition-colors">
                                Detail
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                            <p class="text-gray-500">Tidak ada organisasi ditemukan</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($organizations->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $organizations->withQueryString()->links() }}
            </div>
        @endif
    </div>
@endsection
