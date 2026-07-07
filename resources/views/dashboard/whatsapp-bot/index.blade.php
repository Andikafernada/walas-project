@extends('layouts.app')

@section('title', 'WhatsApp Bot')

@section('content')
<div class='p-4 lg:p-6 max-w-4xl mx-auto space-y-6'>

    <div class='bg-gradient-to-r from-green-600 to-green-700 rounded-2xl p-6 text-white'>
        <h1 class='text-2xl font-bold mb-2'>WhatsApp Bot</h1>
        <p>Koneksikan WhatsApp personal Anda</p>
    </div>

    @if(! || !->isConnected())
        <div class='bg-white rounded-xl p-6 text-center'>
            <p>Scan QR Code untuk koneksikan WhatsApp</p>
            <button onclick='generateQr()' class='mt-4 px-6 py-3 bg-green-600 text-white rounded-lg'>Generate QR</button>
        </div>
    @else
        <div class='bg-white rounded-xl p-6'>
            <div class='flex items-center gap-4 mb-4'>
                <span class='w-3 h-3 bg-green-500 rounded-full animate-pulse'></span>
                <span class='font-medium'>Connected</span>
            </div>
            <p class='text-gray-600 mb-4'>WhatsApp Anda terhubung: {{ ->phone }}</p>
            <form action='{{ route('whatsapp-bot.disconnect') }}' method='POST'>
                @csrf
                <button type='submit' class='px-4 py-2 bg-red-600 text-white rounded-lg'>Disconnect</button>
            </form>
        </div>
    @endif

</div>
@endsection
