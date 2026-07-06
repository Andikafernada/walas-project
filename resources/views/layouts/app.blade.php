<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'WaliKelas Pro') - {{ config('app.name') }}</title>
    @vite
    @stack('styles')
</head>
<body class="bg-gray-100 min-h-screen">

    {{-- Mobile Header --}}
    <header class="lg:hidden bg-white border-b border-gray-200 px-4 py-3 flex items-center justify-between sticky top-0 z-50">
        <div class="flex items-center gap-3">
            <button x-data @click="$dispatch('toggle-sidebar')" class="p-2 hover:bg-gray-100 rounded-lg">
                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-gradient-to-br from-blue-600 to-blue-700 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944c11.736 0 21.568 9.832 21.568 21.568a11.9 11.9 0 01-4.018 8.592"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-sm font-bold text-gray-800">WaliKelas Pro</h1>
                    <p class="text-xs text-gray-500">{{ auth()->user()->classes()->first()?->name ?? 'Dashboard' }}</p>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <button class="p-2 hover:bg-gray-100 rounded-lg text-gray-500">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 15.595V12a6 6 0 10-12 0v3.595c0 .538-.094 1.063-.28 1.568L15 17"/>
                </svg>
            </button>
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" class="flex items-center gap-2 p-1 hover:bg-gray-100 rounded-lg">
                    <img src="{{ auth()->user()->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) }}" class="w-8 h-8 rounded-full">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-gray-200 py-1 z-50">
                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Profil</a>
                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Pengaturan</a>
                    <hr class="my-1">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <div class="flex">
        {{-- Sidebar --}}
        <aside
            x-data="{ sidebar: true }"
            @toggle-sidebar.window="sidebar = !sidebar"
            :class="sidebar ? 'translate-x-0' : '-translate-x-full'"
            class="fixed lg:sticky top-0 lg:top-[65px] inset-y-0 lg:left-0 z-40 w-64 bg-white border-r border-gray-200 transform lg:translate-x-0 transition-transform duration-200 lg:block hidden"
        >
            <div class="h-full flex flex-col">
                {{-- Logo --}}
                <div class="px-4 py-4 border-b border-gray-100">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-blue-600 to-blue-700 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944c11.736 0 21.568 9.832 21.568 21.568a11.9 11.9 0 01-4.018 8.592"/>
                            </svg>
                        </div>
                        <div>
                            <h1 class="font-bold text-gray-800">WaliKelas Pro</h1>
                            <p class="text-xs text-gray-500">{{ auth()->user()->name }}</p>
                        </div>
                    </div>
                </div>

                {{-- Navigation --}}
                <nav class="flex-1 overflow-y-auto p-3 space-y-1">
                    @foreach($navItems as $item)
                        <a href="{{ $item['url'] }}"
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition {{ request()->is($item['active'] ?? '') ? 'bg-blue-600 text-white' : 'text-gray-600 hover:bg-gray-100' }}">
                            @if(isset($item['icon']))
                                @svg($item['icon'], ['class' => 'w-5 h-5'])
                            @endif
                            <span>{{ $item['label'] }}</span>
                        </a>
                    @endforeach
                </nav>

                {{-- Footer --}}
                <div class="p-4 border-t border-gray-100">
                    <div class="text-xs text-gray-400 text-center">
                        <p>&copy; {{ date('Y') }} WaliKelas Pro</p>
                        <p>All rights reserved.</p>
                    </div>
                </div>
            </div>
        </aside>

        {{-- Main Content --}}
        <main class="flex-1 min-h-screen">
            @yield('content')
        </main>
    </div>

    {{-- Toast Notifications --}}
    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
             class="fixed bottom-4 right-4 bg-emerald-500 text-white px-6 py-3 rounded-xl shadow-lg flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
             class="fixed bottom-4 right-4 bg-red-500 text-white px-6 py-3 rounded-xl shadow-lg flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    @stack('scripts')
</body>
</html>
