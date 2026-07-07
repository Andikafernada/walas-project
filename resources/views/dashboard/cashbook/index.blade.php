<x-app-layout>
    <x-slot:title>Buku Kas - {{ $class->name }}</x-slot>

    <!-- Breadcrumb -->
    <nav class="mb-4 flex items-center text-sm text-gray-500">
        <a href="{{ route('dashboard') }}" class="hover:text-indigo-600">Dashboard</a>
        <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <a href="{{ route('classes.show', $class) }}" class="hover:text-indigo-600">{{ $class->name }}</a>
        <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <span>Buku Kas</span>
    </nav>

    <x-page-header title="Buku Kas" description="{{ $class->name }}">

        <x-slot:action>
            <button @click="$dispatch('open-modal', { name: 'add-transaction' })"
                    class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700">
                + Tambah Transaksi
            </button>
        </x-slot:action>
    </x-page-header>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-green-50 rounded-xl p-4 border border-green-200">
            <p class="text-sm text-green-600">Total Pemasukan</p>
            <p class="text-2xl font-bold text-green-700">Rp {{ number_format($totals['income'], 0, ',', '.') }}</p>
        </div>
        <div class="bg-red-50 rounded-xl p-4 border border-red-200">
            <p class="text-sm text-red-600">Total Pengeluaran</p>
            <p class="text-2xl font-bold text-red-700">Rp {{ number_format($totals['expense'], 0, ',', '.') }}</p>
        </div>
        <div class="bg-indigo-50 rounded-xl p-4 border border-indigo-200">
            <p class="text-sm text-indigo-600">Saldo</p>
            <p class="text-2xl font-bold text-indigo-700">Rp {{ number_format($totals['balance'], 0, ',', '.') }}</p>
        </div>
    </div>

    <!-- Transactions Table -->
    @if($transactions->isEmpty())
        <x-empty-state title="Belum ada transaksi" description="Mulai mencatat transaksi" icon="money"/>
    @else
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipe</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kategori</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Keterangan</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Jumlah</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($transactions as $transaction)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $transaction->date->format('d/m/Y') }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs font-medium rounded-full {{ $transaction->type === 'income' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $transaction->type === 'income' ? 'Masuk' : 'Keluar' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $transaction->category }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ Str::limit($transaction->description, 40) }}</td>
                            <td class="px-6 py-4 text-right font-medium {{ $transaction->type === 'income' ? 'text-green-600' : 'text-red-600' }}">
                                {{ $transaction->formatted_amount }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            @if($transactions->hasPages())
                <div class="px-6 py-4 border-t">{{ $transactions->links() }}</div>
            @endif
        </div>
    @endif

    <!-- Add Transaction Modal -->
    <x-modal name="add-transaction" title="Tambah Transaksi" maxWidth="md">
        <form action="{{ route('classes.cashbook.store', $class) }}" method="POST">
            @csrf
            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tipe</label>
                        <select name="type" required class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                            <option value="income">Pemasukan</option>
                            <option value="expense">Pengeluaran</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal</label>
                        <input type="date" name="date" value="{{ date('Y-m-d') }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Kategori</label>
                    <select name="category" required class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        @foreach(\App\Models\CashBook::CATEGORIES as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Jumlah (Rp)</label>
                    <input type="number" name="amount" min="0" required class="w-full px-4 py-2 border border-gray-300 rounded-lg" placeholder="0">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Keterangan</label>
                    <textarea name="description" rows="2" required class="w-full px-4 py-2 border border-gray-300 rounded-lg"></textarea>
                </div>
            </div>
            <div class="flex justify-end space-x-3 mt-6">
                <button type="button" @click="show = false" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">Batal</button>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">Simpan</button>
            </div>
        </form>
    </x-modal>
</x-app-layout>
