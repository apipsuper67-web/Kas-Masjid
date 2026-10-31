@extends('layouts.app')

@section('title', $transaction->exists ? 'Ubah transaksi' : 'Tambah transaksi')

@section('content')
<section class="mx-auto max-w-3xl px-5 py-10 lg:px-8">
    <a href="{{ route('transactions.index') }}" class="text-sm font-medium text-mosque hover:underline">← Kembali ke transaksi</a>
    <div class="mt-5 rounded-3xl border border-slate-100 bg-white p-6 shadow-sm sm:p-9">
        <h1 class="text-2xl font-semibold text-slate-900">{{ $transaction->exists ? 'Ubah transaksi' : 'Catat transaksi baru' }}</h1>
        <p class="mt-2 text-sm text-slate-500">Isi rincian transaksi kas masjid dengan benar.</p>
        <form class="mt-7 space-y-5" method="POST" enctype="multipart/form-data" action="{{ $transaction->exists ? route('transactions.update', $transaction) : route('transactions.store') }}">
            @csrf
            @if ($transaction->exists)
                @method('PUT')
            @endif
            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label for="type" class="mb-2 block text-sm font-medium text-slate-700">Jenis transaksi</label>
                    <select id="type" name="type" required class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:border-mosque focus:outline-none">
                        <option value="income" @selected(old('type', $transaction->type ?? 'income') === 'income')>Pemasukan</option>
                        <option value="expense" @selected(old('type', $transaction->type ?? '') === 'expense')>Pengeluaran</option>
                    </select>
                </div>
                <div>
                    <label for="category_id" class="mb-2 block text-sm font-medium text-slate-700">Kategori</label>
                    <select id="category_id" name="category_id" required class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:border-mosque focus:outline-none">
                        <option value="">Pilih kategori</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" data-type="{{ $category->type }}" @selected((string) old('category_id', $transaction->category_id) === (string) $category->id)>{{ $category->type === 'income' ? 'Pemasukan' : 'Pengeluaran' }} · {{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="amount" class="mb-2 block text-sm font-medium text-slate-700">Jumlah (Rp)</label>
                    <input id="amount" name="amount" type="number" min="1000" step="1000" value="{{ old('amount', $transaction->amount) }}" required class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:border-mosque focus:outline-none">
                </div>
                <div>
                    <label for="date" class="mb-2 block text-sm font-medium text-slate-700">Tanggal</label>
                    <input id="date" name="date" type="date" value="{{ old('date', $transaction->date?->format('Y-m-d') ?? now()->toDateString()) }}" required class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:border-mosque focus:outline-none">
                </div>
            </div>
            <div>
                <label for="description" class="mb-2 block text-sm font-medium text-slate-700">Keterangan <span class="font-normal text-slate-400">(opsional)</span></label>
                <textarea id="description" name="description" rows="3" class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:border-mosque focus:outline-none">{{ old('description', $transaction->description) }}</textarea>
            </div>
            <div>
                <label for="proof_file" class="mb-2 block text-sm font-medium text-slate-700">Bukti transaksi <span class="font-normal text-slate-400">(JPG, PNG, PDF · maks. 5 MB)</span></label>
                <input id="proof_file" name="proof_file" type="file" accept=".jpg,.jpeg,.png,.pdf" class="block w-full rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-600 file:mr-4 file:rounded-lg file:border-0 file:bg-mosque-pale file:px-3 file:py-2 file:font-semibold file:text-mosque">
                @if ($transaction->proof_file)
                    <p class="mt-2 text-xs text-slate-500">Bukti tersedia. Biarkan kosong untuk mempertahankan file saat ini.</p>
                @endif
            </div>
            <button class="w-full rounded-xl bg-mosque px-5 py-3 font-semibold text-white hover:bg-mosque-dark">{{ $transaction->exists ? 'Simpan perubahan' : 'Simpan transaksi' }}</button>
        </form>
    </div>
</section>
@endsection
