<x-app-layout>
    <x-slot:title>Daftar Absensi - {{ $class->name }}</x-slot>

    <!-- Breadcrumb -->
    <nav class="mb-4 flex items-center text-sm text-gray-500">
        <a href="{{ route('dashboard') }}" class="hover:text-indigo-600">Dashboard</a>
        <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
        <a href="{{ route('classes.show', $class) }}" class="hover:text-indigo-600">{{ $class->name }}</a>
        <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
        <span>Absensi</span>
    </nav>

    <x-page-header title="Daftar Absensi" description="{{ $class->name }}">

        <x-slot:action>
            <div class="flex items-center space-x-3">
                <a href="{{ route('classes.attendance.export', $class) }}"
                   class="px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50">
                    Export
                </a>
                <form action="{{ route('classes.attendance.generate', $class) }}" method="POST">
                    @csrf
                    <button type="submit"
                            class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700">
                        + Generate Link Absensi
                    </button>
                </form>
            </div>
        </x-slot:action>
    </x-page-header>

    <!-- Attendance List -->
    @if($sessions->isEmpty())
        <x-empty-state
            title="Belum ada sesi absensi"
            description="Generate link absensi untuk memulai"
            icon="clipboard"
        >
            <x-slot:action>
                <form action="{{ route('classes.attendance.generate', $class) }}" method="POST">
                    @csrf
                    <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700">
                        Generate Link Absensi
                    </button>
                </form>
            </x-slot:action>
        </x-empty-state>
    @else
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Petugas</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu Submit</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Summary</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($sessions as $session)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $session->date->format('d/m/Y') }}</p>
                                    <p class="text-xs text-gray-500">{{ $session->date->locale('id')->dayName }}</p>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $statusConfig = [
                                        'active' => ['bg-yellow-100 text-yellow-800', 'Aktif'],
                                        'used' => ['bg-green-100 text-green-800', 'Selesai'],
                                        'expired' => ['bg-gray-100 text-gray-800', 'Expired'],
                                        'pending' => ['bg-blue-100 text-blue-800', 'Pending'],
                                    ];
                                    $config = $statusConfig[$session->status] ?? ['bg-gray-100 text-gray-800', $session->status];
                                @endphp
                                <span class="px-2 py-1 text-xs font-medium rounded-full {{ $config[0] }}">
                                    {{ $config[1] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                {{ $session->submitted_by_name ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ $session->submitted_at?->format('H:i') ?? '-' }}
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $attendances = $session->attendances;
                                    $hadir = $attendances->where('status', 'hadir')->count();
                                    $terlambat = $attendances->where('status', 'terlambat')->count();
                                    $sakit = $attendances->where('status', 'sakit')->count();
                                    $izin = $attendances->where('status', 'izin')->count();
                                    $alpa = $attendances->where('status', 'alpa')->count();
                                @endphp
                                <div class="flex items-center space-x-2 text-xs">
                                    @if($hadir > 0)
                                        <span class="px-1.5 py-0.5 bg-green-100 text-green-700 rounded">{{ $hadir }} H</span>
                                    @endif
                                    @if($terlambat > 0)
                                        <span class="px-1.5 py-0.5 bg-yellow-100 text-yellow-700 rounded">{{ $terlambat }} T</span>
                                    @endif
                                    @if($sakit > 0)
                                        <span class="px-1.5 py-0.5 bg-blue-100 text-blue-700 rounded">{{ $sakit }} S</span>
                                    @endif
                                    @if($izin > 0)
                                        <span class="px-1.5 py-0.5 bg-purple-100 text-purple-700 rounded">{{ $izin }} I</span>
                                    @endif
                                    @if($alpa > 0)
                                        <span class="px-1.5 py-0.5 bg-red-100 text-red-700 rounded">{{ $alpa }} A</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end space-x-2">
                                    @if($session->status === 'active')
                                        <div class="relative" x-data="{ copied: false }">
                                            <button @click="navigator.clipboard.writeText('{{ $session->magic_link }}'); copied = true; setTimeout(() => copied = false, 2000)"
                                                    class="p-2 text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg" title="Salin Link">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/>
                                                </svg>
                                            </button>
                                            <span x-show="copied" x-transition class="absolute right-0 top-8 bg-gray-800 text-white text-xs px-2 py-1 rounded">
                                                Tersalin!
                                            </span>
                                        </div>
                                        <form action="{{ route('classes.attendance.resend', [$class, $session]) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit"
                                                    class="p-2 text-gray-400 hover:text-green-600 hover:bg-green-50 rounded-lg" title="Kirim Ulang">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                                </svg>
                                            </button>
                                        </form>
                                    @endif
                                    <a href="{{ route('classes.attendance.show', [$class, $session]) }}"
                                       class="p-2 text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg" title="Detail">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            @if($sessions->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $sessions->links() }}
                </div>
            @endif
        </div>
    @endif
</x-app-layout>
