<x-app-layout>
    <x-slot:title>Antrian WhatsApp</x-slot>

    <x-page-header title="Antrian WhatsApp" description="Kelola pesan yang akan dikirim">

        <x-slot:action>
            <a href="{{ route('dashboard') }}" class="px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200">
                Kembali
            </a>
        </x-slot:action>
    </x-page-header>

    <!-- Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-yellow-50 rounded-xl p-4 border border-yellow-200">
            <p class="text-sm text-yellow-600">Pending</p>
            <p class="text-2xl font-bold text-yellow-700">{{ $stats['pending'] }}</p>
        </div>
        <div class="bg-blue-50 rounded-xl p-4 border border-blue-200">
            <p class="text-sm text-blue-600">Processing</p>
            <p class="text-2xl font-bold text-blue-700">{{ $stats['processing'] }}</p>
        </div>
        <div class="bg-green-50 rounded-xl p-4 border border-green-200">
            <p class="text-sm text-green-600">Sent</p>
            <p class="text-2xl font-bold text-green-700">{{ $stats['sent'] }}</p>
        </div>
        <div class="bg-red-50 rounded-xl p-4 border border-red-200">
            <p class="text-sm text-red-600">Failed</p>
            <p class="text-2xl font-bold text-red-700">{{ $stats['failed'] }}</p>
        </div>
    </div>

    <!-- Queue Table -->
    @if($queues->isEmpty())
        <x-empty-state title="Tidak ada pesan" description="Pesan WhatsApp akan muncul di sini" icon="chat"/>
    @else
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Penerima</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pesan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Waktu</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($queues as $queue)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                @php
                                    $statusConfig = [
                                        'pending' => ['bg-yellow-100 text-yellow-800', 'Pending'],
                                        'processing' => ['bg-blue-100 text-blue-800', 'Processing'],
                                        'sent' => ['bg-green-100 text-green-800', 'Sent'],
                                        'failed' => ['bg-red-100 text-red-800', 'Failed'],
                                    ];
                                    $config = $statusConfig[$queue->status] ?? ['bg-gray-100 text-gray-800', $queue->status];
                                @endphp
                                <span class="px-2 py-1 text-xs font-medium rounded-full {{ $config[0] }}">{{ $config[1] }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm font-medium text-gray-900">{{ $queue->recipient_name }}</p>
                                <p class="text-xs text-gray-500">{{ $queue->phone }}</p>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate">{{ $queue->message }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $queue->created_at->format('d/m H:i') }}</td>
                            <td class="px-6 py-4 text-right">
                                @if($queue->status === 'failed')
                                    <form action="{{ route('wa-queue.retry', $queue) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-blue-600 hover:text-blue-800 text-sm">Retry</button>
                                    </form>
                                @endif
                                <form action="{{ route('wa-queue.destroy', $queue) }}" method="POST" class="inline ml-2">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 text-sm">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            @if($queues->hasPages())
                <div class="px-6 py-4 border-t">{{ $queues->links() }}</div>
            @endif
        </div>
    @endif
</x-app-layout>
