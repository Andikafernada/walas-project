<!-- Sidebar -->
<aside
    x-data="{ open: false }"
    @toggle-sidebar.window="open = !open"
    :class="{'translate-x-0': open, '-translate-x-full lg:translate-x-0': !open}"
    class="fixed inset-y-0 left-0 z-50 w-64 bg-gradient-to-b from-indigo-900 to-indigo-800 text-white transform transition-transform duration-300 lg:translate-x-0"
>
    <!-- Logo -->
    <div class="flex items-center h-16 px-6 border-b border-indigo-700">
        <a href="{{ route('dashboard') }}" class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-white/10 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
            </div>
            <div>
                <span class="text-lg font-bold">WaliKelas</span>
                <span class="text-xs text-indigo-300 block">Pro</span>
            </div>
        </a>

        <!-- Mobile Close Button -->
        <button @click="open = false" class="lg:hidden ml-auto text-white/70 hover:text-white">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 overflow-y-auto py-4 px-3">
        <!-- Main Menu -->
        <div class="space-y-1">
            <x-sidebar-link :href="route('dashboard')" icon="dashboard">
                Dashboard
            </x-sidebar-link>

            <x-sidebar-link :href="route('classes.index')" icon="users" :active="request()->routeIs('classes.*')">
                Kelas
            </x-sidebar-link>
        </div>

        <!-- Class-Specific Menu (when in class context) -->
        @if(isset($class) && $class)
            <div class="mt-6">
                <p class="px-3 text-xs font-semibold text-indigo-300 uppercase tracking-wider mb-2">
                    {{ $class->name }}
                </p>
                <div class="space-y-1">
                    <x-sidebar-link :href="route('classes.students.index', $class)" icon="user-group">
                        Siswa
                    </x-sidebar-link>
                    <x-sidebar-link :href="route('classes.attendance.index', $class)" icon="clipboard-check">
                        Absensi
                    </x-sidebar-link>
                    <x-sidebar-link :href="route('classes.schedules.index', $class)" icon="calendar">
                        Jadwal
                    </x-sidebar-link>
                    <x-sidebar-link :href="route('classes.violations.index', $class)" icon="exclamation-circle">
                        Pelanggaran
                    </x-sidebar-link>
                    <x-sidebar-link :href="route('classes.cashbook.index', $class)" icon="currency-dollar">
                        Buku Kas
                    </x-sidebar-link>
                    <x-sidebar-link :href="route('classes.organization.index', $class)" icon="chart-bar">
                        Struktur Kelas
                    </x-sidebar-link>
                    <x-sidebar-link :href="route('classes.seating-charts.index', $class)" icon="table-cells">
                        Denah Duduk
                    </x-sidebar-link>
                    <x-sidebar-link :href="route('classes.journals.index', $class)" icon="book-open">
                        Journal
                    </x-sidebar-link>
                </div>
            </div>
        @endif

        <!-- Reports Section -->
        <div class="mt-6">
            <p class="px-3 text-xs font-semibold text-indigo-300 uppercase tracking-wider mb-2">
                Laporan
            </p>
            <div class="space-y-1">
                <x-sidebar-link :href="route('reports.attendance')" icon="document-chart-bar">
                    Laporan Absensi
                </x-sidebar-link>
                <x-sidebar-link :href="route('reports.violations')" icon="exclamation-triangle">
                    Laporan Pelanggaran
                </x-sidebar-link>
                <x-sidebar-link :href="route('reports.cash-flow')" icon="banknotes">
                    Arus Kas
                </x-sidebar-link>
            </div>
        </div>

        <!-- Management Section -->
        <div class="mt-6">
            <p class="px-3 text-xs font-semibold text-indigo-300 uppercase tracking-wider mb-2">
                Manajemen
            </p>
            <div class="space-y-1">
                <x-sidebar-link :href="route('wa-queue.index')" icon="chat-bubble-left-right">
                    Antrian WA
                </x-sidebar-link>
                @if(auth()->user()->isPro)
                    <x-sidebar-link :href="route('api-tokens.index')" icon="code-bracket">
                        API Tokens
                    </x-sidebar-link>
                @endif
            </div>
        </div>
    </nav>

    <!-- User Section -->
    <div class="p-4 border-t border-indigo-700">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <div class="w-10 h-10 rounded-full bg-indigo-600 flex items-center justify-center">
                    <span class="text-sm font-medium">{{ substr(auth()->user()->name, 0, 1) }}</span>
                </div>
            </div>
            <div class="ml-3 flex-1 min-w-0">
                <p class="text-sm font-medium truncate">{{ auth()->user()->name }}</p>
                <p class="text-xs text-indigo-300 truncate">{{ auth()->user()->tier === 'pro' ? 'Pro' : 'Free' }}</p>
            </div>
        </div>
    </div>
</aside>

<!-- Sidebar Overlay (Mobile) -->
<div
    x-show="open"
    x-transition:enter="transition-opacity ease-linear duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition-opacity ease-linear duration-300"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    @click="open = false"
    class="fixed inset-0 bg-black/50 z-40 lg:hidden"
    :class="{'hidden': !open}"
></div>
