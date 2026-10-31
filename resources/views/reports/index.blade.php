@extends('layouts.app')

@section('title', 'Laporan kas')

@section('content')
<div class="mx-auto max-w-7xl px-5 py-10 lg:px-8">
    <div>
        <p class="text-sm font-medium text-mosque">Terbuka untuk jamaah</p>
        <h1 class="mt-1 text-3xl font-semibold tracking-tight text-slate-900">Laporan keuangan</h1>
        <p class="mt-2 text-sm text-slate-500">Rincian pemasukan dan pengeluaran kas masjid secara transparan.</p>
    </div>
    <form method="GET" class="mt-6 flex flex-wrap items-end gap-3 rounded-2xl border border-slate-100 bg-white p-4 shadow-sm">
        <div>
            <label for="period" class="mb-1.5 block text-xs font-medium text-slate-500">Periode</label>
            <select id="period" name="period" class="rounded-xl border border-slate-200 px-3 py-2.5 text-sm">
                <option value="bulanan" @selected($filters['period'] === 'bulanan')>Bulanan</option>
                <option value="tahunan" @selected($filters['period'] === 'tahunan')>Tahunan</option>
            </select>
        </div>
        <div>
            <label for="month" class="mb-1.5 block text-xs font-medium text-slate-500">Bulan</label>
            <select id="month" name="month" class="rounded-xl border border-slate-200 px-3 py-2.5 text-sm">
                @foreach (range(1, 12) as $monthNumber)
                    <option value="{{ $monthNumber }}" @selected((int) $filters['month'] === $monthNumber)>{{ \Illuminate\Support\Carbon::create()->month($monthNumber)->translatedFormat('F') }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="year" class="mb-1.5 block text-xs font-medium text-slate-500">Tahun</label>
            <input id="year" name="year" type="number" min="2020" max="{{ now()->year + 1 }}" value="{{ $filters['year'] }}" class="w-28 rounded-xl border border-slate-200 px-3 py-2.5 text-sm">
        </div>
        <button class="rounded-xl bg-mosque px-5 py-2.5 text-sm font-semibold text-white hover:bg-mosque-dark">Tampilkan</button>
        <a href="{{ route('reports.excel', request()->query()) }}" class="no-print rounded-xl border border-slate-200 px-5 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50">Ekspor Excel</a>
        <a href="{{ route('reports.pdf', request()->query()) }}" class="no-print rounded-xl border border-slate-200 px-5 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50">Unduh PDF</a>
        <button type="button" onclick="window.print()" class="no-print rounded-xl border border-slate-200 px-5 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50">Cetak</button>
    </form>
    <div class="mt-5 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        @foreach ([
            ['Saldo awal', $openingBalance, 'text-slate-900'],
            ['Pemasukan', $income, 'text-emerald-700'],
            ['Pengeluaran', $expense, 'text-rose-700'],
            ['Saldo akhir', $closingBalance, 'text-mosque'],
        ] as [$label, $amount, $color])
            <article class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm">
                <p class="text-sm text-slate-500">{{ $label }}</p>
                <p class="mt-2 text-xl font-semibold {{ $color }}">Rp{{ number_format($amount, 0, ',', '.') }}</p>
            </article>
        @endforeach
    </div>
    <section class="mt-6 overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm">
        <div class="border-b border-slate-100 px-5 py-4">
            <h2 class="font-semibold text-slate-900">Rincian transaksi · {{ $filters['period'] === 'tahunan' ? $filters['year'] : \Illuminate\Support\Carbon::create($filters['year'], $filters['month'])->translatedFormat('F Y') }}</h2>
        </div>
        <div class="divide-y divide-slate-100">
            @forelse ($transactions as $transaction)
                <div class="flex flex-wrap items-center justify-between gap-3 px-5 py-4">
                    <div>
                        <p class="font-medium text-slate-800">{{ $transaction->category->name }}</p>
                        <p class="mt-1 text-xs text-slate-500">{{ $transaction->date->format('d/m/Y') }} · {{ $transaction->description ?: 'Tanpa keterangan' }}</p>
                    </div>
                    <p class="font-semibold {{ $transaction->type === 'income' ? 'text-emerald-700' : 'text-slate-700' }}">{{ $transaction->type === 'income' ? '+' : '−' }} Rp{{ number_format($transaction->amount, 0, ',', '.') }}</p>
                </div>
            @empty
                <p class="px-5 py-10 text-center text-sm text-slate-500">Tidak ada transaksi pada periode ini.</p>
            @endforelse
        </div>
        <div class="border-t border-slate-100 px-5 py-4">{{ $transactions->links() }}</div>
    </section>
</div>
@endsection
