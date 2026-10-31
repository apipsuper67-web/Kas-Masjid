@extends('layouts.app')

@section('title', 'Transaksi')

@section('content')
<div class="mx-auto max-w-7xl px-5 py-10 lg:px-8">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="text-sm font-medium text-mosque">Buku kas</p>
            <h1 class="mt-1 text-3xl font-semibold tracking-tight text-slate-900">Transaksi</h1>
            <p class="mt-2 text-sm text-slate-500">Kelola pemasukan dan pengeluaran masjid.</p>
        </div>
        <a href="{{ route('transactions.create') }}" class="rounded-xl bg-mosque px-5 py-3 text-sm font-semibold text-white hover:bg-mosque-dark">+ Tambah transaksi</a>
    </div>

    <form method="GET" class="mt-7 grid gap-3 rounded-2xl border border-slate-100 bg-white p-4 shadow-sm sm:grid-cols-2 lg:grid-cols-6">
        <input name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Cari keterangan" class="rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:border-mosque focus:outline-none">
        <select name="type" class="rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:border-mosque focus:outline-none">
            <option value="">Semua jenis</option>
            <option value="income" @selected(($filters['type'] ?? '') === 'income')>Pemasukan</option>
            <option value="expense" @selected(($filters['type'] ?? '') === 'expense')>Pengeluaran</option>
        </select>
        <select name="category_id" class="rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:border-mosque focus:outline-none">
            <option value="">Semua kategori</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" @selected((string) ($filters['category_id'] ?? '') === (string) $category->id)>{{ $category->name }}</option>
            @endforeach
        </select>
        <input type="date" name="from" value="{{ $filters['from'] ?? '' }}" aria-label="Tanggal mulai" class="rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:border-mosque focus:outline-none">
        <input type="date" name="to" value="{{ $filters['to'] ?? '' }}" aria-label="Tanggal akhir" class="rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:border-mosque focus:outline-none">
        <div class="flex gap-2">
            <button class="flex-1 rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white">Filter</button>
            <a href="{{ route('transactions.index') }}" class="rounded-xl border border-slate-200 px-3 py-2.5 text-sm text-slate-600">Reset</a>
        </div>
    </form>

    <div class="mt-5 overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[760px] text-left text-sm">
                <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-5 py-4 font-semibold">Tanggal</th>
                        <th class="px-5 py-4 font-semibold">Kategori / keterangan</th>
                        <th class="px-5 py-4 font-semibold">Jenis</th>
                        <th class="px-5 py-4 text-right font-semibold">Jumlah</th>
                        <th class="px-5 py-4 text-right font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($transactions as $transaction)
                        <tr>
                            <td class="whitespace-nowrap px-5 py-4 text-slate-600">{{ $transaction->date->format('d/m/Y') }}</td>
                            <td class="px-5 py-4">
                                <p class="font-semibold text-slate-800">{{ $transaction->category->name }}</p>
                                <p class="mt-1 max-w-md truncate text-xs text-slate-500">{{ $transaction->description ?: 'Tanpa keterangan' }}</p>
                            </td>
                            <td class="px-5 py-4">
                                <span class="rounded-full px-2.5 py-1 text-xs font-medium {{ $transaction->type === 'income' ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">{{ $transaction->type === 'income' ? 'Pemasukan' : 'Pengeluaran' }}</span>
                            </td>
                            <td class="whitespace-nowrap px-5 py-4 text-right font-semibold text-slate-800">Rp{{ number_format($transaction->amount, 0, ',', '.') }}</td>
                            <td class="whitespace-nowrap px-5 py-4 text-right">
                                <div class="flex justify-end gap-2">
                                    @if ($transaction->proof_file)
                                        <a href="{{ route('transactions.proof', $transaction) }}" class="rounded-lg border border-slate-200 px-3 py-2 text-xs font-medium text-slate-600 hover:border-mosque">Bukti</a>
                                    @endif
                                    @if (! $transaction->donation)
                                        <a href="{{ route('transactions.edit', $transaction) }}" class="rounded-lg border border-slate-200 px-3 py-2 text-xs font-medium text-slate-600 hover:border-mosque">Ubah</a>
                                        <form method="POST" action="{{ route('transactions.destroy', $transaction) }}" onsubmit="return confirm('Hapus transaksi ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="rounded-lg border border-rose-100 px-3 py-2 text-xs font-medium text-rose-700 hover:bg-rose-50">Hapus</button>
                                        </form>
                                    @else
                                        <span class="rounded-lg bg-slate-50 px-3 py-2 text-xs text-slate-500">Infaq terverifikasi</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-5 py-12 text-center text-slate-500">Tidak ada transaksi yang cocok dengan filter.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-slate-100 px-5 py-4">{{ $transactions->links() }}</div>
    </div>
</div>
@endsection
