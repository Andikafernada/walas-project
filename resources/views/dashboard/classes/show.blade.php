<x-app-layout>
    <x-slot:title>{{ $class->name }}</x-slot>

    <!-- Breadcrumb -->
    <nav class="mb-4 flex items-center text-sm text-gray-500">
        <a href="{{ route('dashboard') }}" class="hover:text-indigo-600">Dashboard</a>
        <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
        <a href="{{ route('classes.index') }}" class="hover:text-indigo-600">Kelas</a>
        <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
        <span>{{ $class->name }}</span>
    </nav>

    <!-- Page Header -->
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $class->name }}</h1>
            <p class="text-gray-500">{{ $class->jurusan }} - {{ $class->tingkat }} | TA {{ $class->school_year_start }}/{{ $class->school_year_end }}</p>
        </div>

        <div class="flex items-center space-x-3">
            <a href="{{ route('classes.edit', $class) }}"
               class="px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50">
                Edit Kelas
            </a>
            <form action="{{ route('classes.attendance.generate', $class) }}" method="POST" class="inline">
                @csrf
                <button type="submit"
                        class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700">
                    Generate Link Absensi
                </button>
            </form>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <p class="text-sm text-gray-500">Total Siswa</p>
            <p class="text-2xl font-bold text-gray-900">{{ $class->students()->where('is_active', true)->count() }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <p class="text-sm text-gray-500">Jumlah Jadwal</p>
            <p class="text-2xl font-bold text-gray-900">{{ $class->schedules()->count() }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <p class="text-sm text-gray-500">Organisasi</p>
            <p class="text-2xl font-bold text-gray-900">{{ $class->organizationStructures()->count() }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <p class="text-sm text-gray-500">Total Saldo</p>
            <p class="text-2xl font-bold text-gray-900">Rp {{ number_format($class->cashBooks()->where('type', 'income')->sum('amount') - $class->cashBooks()->where('type', 'expense')->sum('amount'), 0, ',', '.') }}</p>
        </div>
    </div>

    <!-- Menu Grid -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
        <a href="{{ route('classes.students.index', $class) }}" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow group">
            <div class="w-12 h-12 rounded-lg bg-blue-100 flex items-center justify-center mb-4 group-hover:bg-blue-200">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
            <h3 class="font-semibold text-gray-900">Siswa</h3>
            <p class="text-sm text-gray-500">{{ $class->students()->where('is_active', true)->count() }} siswa aktif</p>
        </a>

        <a href="{{ route('classes.attendance.index', $class) }}" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow group">
            <div class="w-12 h-12 rounded-lg bg-green-100 flex items-center justify-center mb-4 group-hover:bg-green-200">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
            </div>
            <h3 class="font-semibold text-gray-900">Absensi</h3>
            <p class="text-sm text-gray-500">Kelola absensi harian</p>
        </a>

        <a href="{{ route('classes.schedules.index', $class) }}" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow group">
            <div class="w-12 h-12 rounded-lg bg-purple-100 flex items-center justify-center mb-4 group-hover:bg-purple-200">
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <h3 class="font-semibold text-gray-900">Jadwal</h3>
            <p class="text-sm text-gray-500">{{ $class->schedules()->count() }} jadwal</p>
        </a>

        <a href="{{ route('classes.violations.index', $class) }}" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow group">
            <div class="w-12 h-12 rounded-lg bg-red-100 flex items-center justify-center mb-4 group-hover:bg-red-200">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <h3 class="font-semibold text-gray-900">Pelanggaran</h3>
            <p class="text-sm text-gray-500">Poin & kedisiplinan</p>
        </a>

        <a href="{{ route('classes.cashbook.index', $class) }}" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow group">
            <div class="w-12 h-12 rounded-lg bg-yellow-100 flex items-center justify-center mb-4 group-hover:bg-yellow-200">
                <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <h3 class="font-semibold text-gray-900">Buku Kas</h3>
            <p class="text-sm text-gray-500">Keuangan kelas</p>
        </a>

        <a href="{{ route('classes.organization.index', $class) }}" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow group">
            <div class="w-12 h-12 rounded-lg bg-indigo-100 flex items-center justify-center mb-4 group-hover:bg-indigo-200">
                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
            </div>
            <h3 class="font-semibold text-gray-900">Struktur Kelas</h3>
            <p class="text-sm text-gray-500">Organisasi</p>
        </a>

        <a href="{{ route('classes.seating-charts.index', $class) }}" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow group">
            <div class="w-12 h-12 rounded-lg bg-teal-100 flex items-center justify-center mb-4 group-hover:bg-teal-200">
                <svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                </svg>
            </div>
            <h3 class="font-semibold text-gray-900">Denah Duduk</h3>
            <p class="text-sm text-gray-500">Pengaturan tempat duduk</p>
        </a>

        <a href="{{ route('classes.journals.index', $class) }}" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow group">
            <div class="w-12 h-12 rounded-lg bg-pink-100 flex items-center justify-center mb-4 group-hover:bg-pink-200">
                <svg class="w-6 h-6 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
            </div>
            <h3 class="font-semibold text-gray-900">Journal</h3>
            <p class="text-sm text-gray-500">Catatan BK</p>
        </a>
    </div>

    <!-- Today's Schedule Preview -->
    @if($schedules->isNotEmpty())
        <div class="mt-8 bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="text-lg font-semibold text-gray-900">Jadwal Hari Ini</h2>
            </div>
            <div class="divide-y divide-gray-100">
                @foreach($schedules as $day => $daySchedules)
                    @if($day === strtolower(now()->locale('id')->dayName))
                        @foreach($daySchedules as $schedule)
                            <div class="px-6 py-4 flex items-center justify-between">
                                <div>
                                    <p class="font-medium text-gray-900">{{ $schedule->subject }}</p>
                                    <p class="text-sm text-gray-500">{{ $schedule->teacher_name ?? 'Guru belum ditentukan' }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="font-medium text-gray-900">{{ $schedule->start_time }} - {{ $schedule->end_time }}</p>
                                    <p class="text-sm text-gray-500">{{ $schedule->duration }} menit</p>
                                </div>
                            </div>
                        @endforeach
                    @endif
                @endforeach
            </div>
        </div>
    @endif
</x-app-layout>
