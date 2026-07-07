<!-- Breadcrumb Component -->
<nav class="flex" aria-label="Breadcrumb">
    <ol class="inline-flex items-center space-x-1 md:space-x-2">
        <li class="inline-flex items-center">
            <a href="{{ route('dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-indigo-600">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                Dashboard
            </a>
        </li>

        @foreach($crumbs ?? [] as $crumb => $url)
            <li>
                <div class="flex items-center">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                    @if($url)
                        <a href="{{ $url }}" class="ml-1 text-sm font-medium text-gray-500 hover:text-indigo-600 md:ml-2">
                            {{ $crumb }}
                        </a>
                    @else
                        <span class="ml-1 text-sm font-medium text-gray-700 md:ml-2">
                            {{ $crumb }}
                        </span>
                    @endif
                </div>
            </li>
        @endforeach
    </ol>
</nav>
