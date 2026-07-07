@extends('layouts.app')

@section('title', 'WhatsApp Bot')

@section('content')
<div class="p-4 lg:p-6 max-w-4xl mx-auto space-y-6">

    <!-- Header -->
    <div class="bg-gradient-to-r from-green-600 to-green-700 rounded-2xl p-6 text-white">
        <h1 class="text-2xl font-bold mb-2">WhatsApp Bot</h1>
        <p>Koneksikan WhatsApp personal Anda untuk auto kirim magic link</p>
    </div>

    <!-- Connection Status -->
    @if($session && $session->isConnected())
        <div class="bg-white rounded-xl shadow-sm border-green-200 p-6">
            <div class="flex items-center gap-4 mb-4">
                <div class="w-4 h-4 bg-green-500 rounded-full animate-pulse"></div>
                <span class="font-semibold text-green-700">WhatsApp Terhubung</span>
            </div>
            <p class="text-gray-600 mb-2">Nomor: {{ $session->phone }}</p>
            <p class="text-sm text-gray-500 mb-4">Terakhir aktif: {{ $session->last_seen_at ? $session->last_seen_at->diffForHumans() : 'Baru saja' }}</p>

            <div class="flex gap-3">
                <form action="{{ route('whatsapp-bot.disconnect') }}" method="POST">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                        Disconnect
                    </button>
                </form>
            </div>
        </div>
    @else
        <div class="bg-white rounded-xl shadow-sm border p-6 text-center" id="qr-container">
            @if($session && $session->qr_code && !$session->isQrExpired())
                <p class="text-gray-600 mb-4">Scan QR Code di bawah dengan WhatsApp:</p>
                <img src="{{ $session->qr_code }}" alt="QR Code" class="mx-auto w-64 h-64 border-2 border-gray-200 rounded-lg">
                <p class="text-sm text-gray-500 mt-4">Expires dalam: <span id="qr-timer"></span></p>
            @else
                <p class="text-gray-600 mb-4">Klik tombol di bawah untuk generate QR Code:</p>
                <button onclick="generateQr()" id="btn-generate" class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    Generate QR Code
                </button>
            @endif
        </div>
    @endif

    <!-- Test Message -->
    <div class="bg-white rounded-xl shadow-sm border p-6">
        <h2 class="font-bold text-lg mb-4">Kirim Pesan Test</h2>
        <form action="{{ route('whatsapp-bot.send-test') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nomor WhatsApp</label>
                <input type="text" name="phone" value="6281234567890" class="w-full px-4 py-2 border rounded-lg" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Pesan</label>
                <textarea name="message" rows="3" class="w-full px-4 py-2 border rounded-lg" required>Test dari Walas Pro Bot!</textarea>
            </div>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                Kirim Test
            </button>
        </form>
    </div>

</div>

@if(session('success'))
<div class="fixed bottom-4 right-4 bg-green-600 text-white px-6 py-3 rounded-lg shadow-lg">
    {{ session('success') }}
</div>
@endif

@if(session('error'))
<div class="fixed bottom-4 right-4 bg-red-600 text-white px-6 py-3 rounded-lg shadow-lg">
    {{ session('error') }}
</div>
@endif

@endsection

@push('scripts')
<script>
function generateQr() {
    const btn = document.getElementById('btn-generate');
    if (btn) {
        btn.disabled = true;
        btn.innerText = 'Loading...';
    }

    fetch('/whatsapp-bot/generate-qr', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || 'Error generating QR');
            if (btn) {
                btn.disabled = false;
                btn.innerText = 'Generate QR Code';
            }
        }
    })
    .catch(err => {
        console.error(err);
        alert('Error');
        if (btn) {
            btn.disabled = false;
            btn.innerText = 'Generate QR Code';
        }
    });
}

// Check status every 5 seconds
setInterval(() => {
    fetch('/whatsapp-bot/status')
        .then(r => r.json())
        .then(data => {
            if (data.connected) {
                location.reload();
            }
        })
        .catch(() => {});
}, 5000);
</script>
@endpush
