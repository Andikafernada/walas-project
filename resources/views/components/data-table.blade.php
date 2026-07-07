<!-- Data Table Component -->
@props([
    'headers' => [],
    'paginate' => null,
])

<div class="overflow-hidden border border-gray-200 rounded-xl">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    @foreach($headers as $header)
                        <th
                            {{ $header['class'] ?? 'px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider' }}
                        >
                            {{ $header['label'] ?? $header }}
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                {{ $rows }}
            </tbody>
        </table>
    </div>

    @if($paginate)
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $paginate }}
        </div>
    @endif
</div>
