@extends('admin.layouts.app')

@section('title', $organization->name . ' - Admin Walas')
@section('header', $organization->name)

@section('header_actions')
    <a href="{{ route('admin.organizations.index') }}" class="inline-flex items-center gap-2 px-4 py-2 text-gray-600 hover:text-gray-900">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
        </svg>
        Kembali
    </a>
@endsection

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Organization Info -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl border border-gray-200 p-6">
                <div class="flex items-start justify-between mb-6">
                    <div class="flex items-center gap-4">
                        <div class="w-16 h-16 bg-primary-100 rounded-2xl flex items-center justify-center">
                            <svg class="w-8 h-8 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-gray-900">{{ $organization->name }}</h2>
                            <p class="text-gray-500">{{ $organization->type_label }} &bull; {{ $organization->city }}</p>
                        </div>
                    </div>
                    <span class="px-3 py-1 text-sm font-medium rounded-full {{ $organization->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                        {{ ucfirst($organization->status) }}
                    </span>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="bg-gray-50 rounded-xl p-4">
                        <p class="text-sm text-gray-500">Wali Kelas</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $organization->users_count }}</p>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-4">
                        <p class="text-sm text-gray-500">Kelas</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $organization->classes_count ?? 0 }}</p>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-4">
                        <p class="text-sm text-gray-500">Total Siswa</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $organization->total_students_count ?? 0 }}</p>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-4">
                        <p class="text-sm text-gray-500">Bergabung</p>
                        <p class="text-lg font-semibold text-gray-900 mt-1">{{ $organization->created_at->format('d M Y') }}</p>
                    </div>
                </div>
            </div>

            <!-- Users List -->
            <div class="bg-white rounded-2xl border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Wali Kelas</h3>
                </div>
                <div class="divide-y divide-gray-100">
                    @forelse($organization->users as $user)
                        <div class="px-6 py-4 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-10 h-10 rounded-full">
                                <div>
                                    <p class="font-medium text-gray-900">{{ $user->name }}</p>
                                    <p class="text-sm text-gray-500">{{ $user->email }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="px-2 py-1 text-xs font-medium rounded-full {{ $user->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                                    {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                                <span class="text-xs text-gray-500">{{ $user->role_label }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="px-6 py-8 text-center text-gray-500">
                            <p>Belum ada wali kelas</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Details -->
            <div class="bg-white rounded-2xl border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Detail</h3>
                <dl class="space-y-4">
                    <div>
                        <dt class="text-sm text-gray-500">Email</dt>
                        <dd class="text-gray-900">{{ $organization->email ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">Telepon</dt>
                        <dd class="text-gray-900">{{ $organization->phone ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">Alamat</dt>
                        <dd class="text-gray-900">{{ $organization->address ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">Website</dt>
                        <dd class="text-gray-900">{{ $organization->website ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">Slug</dt>
                        <dd class="text-gray-900 font-mono text-sm">{{ $organization->slug }}</dd>
                    </div>
                </dl>
            </div>

            <!-- Status Update -->
            <div class="bg-white rounded-2xl border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Update Status</h3>
                <form action="{{ route('admin.organizations.update-status', $organization) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg mb-4">
                        <option value="active" {{ $organization->status === 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="pending" {{ $organization->status === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="inactive" {{ $organization->status === 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                        <option value="suspended" {{ $organization->status === 'suspended' ? 'selected' : '' }}>Suspended</option>
                    </select>
                    <button type="submit" class="w-full py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
                        Update Status
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection
