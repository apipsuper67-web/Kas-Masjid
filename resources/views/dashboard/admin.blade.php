@extends('layouts.app')

@section('title', 'Dashboard pengurus')

@section('content')
<div class="mx-auto max-w-7xl px-5 py-10 lg:px-8">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="text-sm font-medium text-mosque">Dashboard pengurus</p>
            <h1 class="mt-1 text-3xl font-semibold tracking-tight text-slate-900">Assalamu'alaikum, {{ auth()->user()->name }}</h1>
            <p class="mt-2 text-sm text-slate-500">Ringkasan keuangan dan aktivitas masjid.</p>
        </div>
        <a href="{{ route('transactions.create') }}" class="rounded-xl bg-mosque px-5 py-3 text-sm font-semibold text-white hover:bg-mosque-dark">+ Catat transaksi</a>
    </div>

    <div class="mt-8 grid gap-4 sm:grid-cols-3">
        @foreach ([
            ['Saldo kas', $balance, 'bg-mosque-pale text-mosque'],
            ['Pemasukan bulan ini', $monthIncome, 'bg-emerald-50 text-emerald-700'],
            ['Pengeluaran bulan ini', $monthExpense, 'bg-rose-50 text-rose-700'],
        ] as [$label, $amount, $classes])
            <article class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm">
                <p class="text-sm text-slate-500">{{ $label }}</p>
                <p class="mt-2 text-2xl font-semibold {{ $classes }}">Rp{{ number_format($amount, 0, ',', '.') }}</p>
            </article>
        @endforeach
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-[1.4fr_1fr]">
        <section class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h2 class="font-semibold text-slate-900">Pemasukan & pengeluaran</h2>
                    <p class="mt-1 text-xs text-slate-500">Enam bulan terakhir</p>
                </div>
                <span class="rounded-full bg-slate-50 px-3 py-1 text-xs text-slate-500">IDR</span>
            </div>
            <div class="mt-5 h-72">
                <canvas id="finance-chart" aria-label="Grafik pemasukan dan pengeluaran per bulan"></canvas>
            </div>
            <div class="mt-4 flex gap-5 text-xs text-slate-500">
                <span class="flex items-center gap-2"><i class="size-2.5 rounded-full bg-mosque"></i> Pemasukan</span>
                <span class="flex items-center gap-2"><i class="size-2.5 rounded-full bg-amber-400"></i> Pengeluaran</span>
            </div>
        </section>
        <section class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="font-semibold text-slate-900">Infaq menunggu verifikasi</h2>
                    <p class="mt-1 text-xs text-slate-500">Periksa bukti transfer jamaah</p>
                </div>
                <span class="rounded-full bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-700">{{ $pendingDonations->count() }} pending</span>
            </div>
            <div class="mt-5 space-y-4">
                @forelse ($pendingDonations as $donation)
                    <div class="rounded-xl border border-slate-100 p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="font-semibold text-slate-800">{{ $donation->is_anonymous ? 'Hamba Allah' : ($donation->donor_name ?? 'Jamaah') }}</p>
                                <p class="mt-1 text-xs text-slate-500">{{ str_replace('_', ' ', ucfirst($donation->purpose)) }} · {{ $donation->created_at->translatedFormat('d M Y') }}</p>
                            </div>
                            <span class="whitespace-nowrap text-sm font-semibold text-mosque">Rp{{ number_format($donation->amount, 0, ',', '.') }}</span>
                        </div>
                        <div class="mt-3 flex flex-wrap gap-2">
                            @if ($donation->proof_file)
                                <a href="{{ route('donations.proof', $donation) }}" class="rounded-lg border border-slate-200 px-3 py-2 text-xs font-medium text-slate-600 hover:border-mosque">Lihat bukti</a>
                            @endif
                            <form method="POST" action="{{ route('admin.donations.verify', $donation) }}">
                                @csrf
                                <button class="rounded-lg bg-mosque px-3 py-2 text-xs font-semibold text-white hover:bg-mosque-dark">Verifikasi</button>
                            </form>
                            <form method="POST" action="{{ route('admin.donations.reject', $donation) }}">
                                @csrf
                                <button class="rounded-lg bg-rose-50 px-3 py-2 text-xs font-semibold text-rose-700 hover:bg-rose-100">Tolak</button>
                            </form>
                        </div>
                    </div>
                @empty
                    <p class="rounded-xl bg-slate-50 px-4 py-8 text-center text-sm text-slate-500">Tidak ada infaq yang menunggu verifikasi.</p>
                @endforelse
            </div>
        </section>
    </div>

    <section class="mt-6 rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h2 class="font-semibold text-slate-900">Transaksi terbaru</h2>
                <p class="mt-1 text-xs text-slate-500">Catatan kas terbaru masjid</p>
            </div>
            <a href="{{ route('transactions.index') }}" class="text-sm font-semibold text-mosque hover:underline">Kelola transaksi →</a>
        </div>
        <div class="mt-4 divide-y divide-slate-100">
            @forelse ($transactions as $transaction)
                <div class="flex flex-wrap items-center justify-between gap-3 py-4">
                    <div>
                        <p class="font-medium text-slate-800">{{ $transaction->category->name }}</p>
                        <p class="mt-1 text-xs text-slate-500">{{ $transaction->date->translatedFormat('d F Y') }} · {{ $transaction->description ?: 'Tanpa keterangan' }}</p>
                    </div>
                    <span class="font-semibold {{ $transaction->type === 'income' ? 'text-mosque' : 'text-slate-700' }}">{{ $transaction->type === 'income' ? '+' : '−' }} Rp{{ number_format($transaction->amount, 0, ',', '.') }}</span>
                </div>
            @empty
                <p class="py-8 text-center text-sm text-slate-500">Belum ada transaksi.</p>
            @endforelse
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script>
    window.financeChartData = {
        labels: @js($chartLabels),
        income: @js($chartIncome),
        expense: @js($chartExpense),
    };
</script>
@endpush
