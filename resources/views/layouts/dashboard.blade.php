<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="description" content="WaliKelas Pro - Solusi Administrasi Wali Kelas">

        <title>{{ $title ?? 'WaliKelas Pro' }} | {{ config('app.name') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">

        <!-- Favicon -->
        <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">

        <!-- Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @stack('styles')
    </head>
    <body x-data="{ sidebarOpen: false }" class="font-sans antialiased bg-gray-50 text-gray-900">
        <div class="min-h-screen flex">
            <!-- Sidebar -->
            @include('layouts.partials.sidebar')

            <!-- Main Content -->
            <div class="flex-1 flex flex-col lg:pl-64">
                <!-- Top Navigation -->
                @include('layouts.partials.topbar')

                <!-- Page Content -->
                <main class="flex-1 p-4 sm:p-6 lg:p-8">
                    <!-- Breadcrumb -->
                    @isset($breadcrumbs)
                        <nav class="mb-4">
                            {{ $breadcrumbs }}
                        </nav>
                    @endisset

                    <!-- Page Header -->
                    @isset($header)
                        <div class="mb-6">
                            {{ $header }}
                        </div>
                    @endisset

                    <!-- Flash Messages -->
                    @if(session('success'))
                        <x-alert type="success" :message="session('success')" class="mb-4" />
                    @endif

                    @if(session('error'))
                        <x-alert type="error" :message="session('error')" class="mb-4" />
                    @endif

                    @if(session('info'))
                        <x-alert type="info" :message="session('info')" class="mb-4" />
                    @endif

                    @if(session('warning'))
                        <x-alert type="warning" :message="session('warning')" class="mb-4" />
                    @endif

                    <!-- Main Slot -->
                    {{ $slot }}
                </main>

                <!-- Footer -->
                @include('layouts.partials.footer')
            </div>
        </div>

        <!-- Modal Container -->
        <div x-data="{ show: false, content: '' }">
            <x-modal />
        </div>

        <!-- Scripts -->
        @stack('scripts')

        <!-- Alpine.js Initialization -->
        <script>
            document.addEventListener('alpine:init', () => {
                // Custom Alpine components can be added here
            });
        </script>
    </body>
</html>
