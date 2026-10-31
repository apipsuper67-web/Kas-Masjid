@extends('layouts.app')

@section('title', 'Beranda')

@section('content')
<div class="mx-auto max-w-7xl px-5 pt-7 lg:px-8">
    <section class="hero-panel relative isolate overflow-hidden rounded-[2rem] px-7 py-10 sm:px-12 sm:py-14 lg:min-h-[420px] lg:px-16">
        <div class="relative z-10 max-w-xl">
            <p class="inline-flex items-center gap-2 rounded-full bg-white/80 px-3 py-1.5 text-xs font-semibold tracking-wide text-mosque">
                <span class="size-2 rounded-full bg-mosque"></span> LAPORAN KAS TERBUKA UNTUK SEMUA
            </p>
            <h1 class="mt-6 text-4xl font-semibold leading-tight tracking-tight text-slate-900 sm:text-5xl">Menjaga amanah,<br>menguatkan rumah ibadah.</h1>
            <p class="mt-5 max-w-lg text-base leading-7 text-slate-600">Setiap rupiah tercatat dengan jelas. Mari tumbuhkan kebaikan dan dukung kegiatan masjid bersama-sama.</p>
            <div class="mt-7 flex flex-wrap gap-3">
                <a href="{{ route('donations.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-mosque px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-mosque-dark">Mulai berinfaq <span aria-hidden="true">→</span></a>
                <a href="{{ route('reports.index') }}" class="rounded-xl border border-white bg-white/80 px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-white">Lihat laporan</a>
            </div>
            <div class="mt-8 flex items-center gap-3 text-sm text-slate-500">
                <span class="flex -space-x-2">
                    <span class="grid size-8 place-items-center rounded-full border-2 border-white bg-mosque-pale text-xs">🤲</span>
                    <span class="grid size-8 place-items-center rounded-full border-2 border-white bg-amber-100 text-xs">☪</span>
                    <span class="grid size-8 place-items-center rounded-full border-2 border-white bg-sky-100 text-xs">🌿</span>
                </span>
                Amanah jamaah, tercatat transparan
            </div>
        </div>
        <div class="pointer-events-none absolute inset-y-0 right-0 hidden w-[48%] items-center justify-center lg:flex">
            <div class="mosque-art relative h-[330px] w-[390px] overflow-hidden rounded-[48%_48%_24px_24px] shadow-2xl">
                <div class="absolute right-12 top-10 size-16 rounded-full bg-amber-100/90"></div>
                <div class="absolute bottom-0 left-0 right-0 h-20 bg-[#2f4028]"></div>
                <div class="absolute bottom-16 left-1/2 h-44 w-56 -translate-x-1/2 rounded-t-[120px] bg-[#f7f5e8]"></div>
                <div class="absolute bottom-16 left-1/2 h-32 w-24 -translate-x-1/2 rounded-t-[60px] bg-[#647949]"></div>
                <div class="absolute bottom-[188px] left-1/2 h-20 w-20 -translate-x-1/2 rounded-t-full bg-[#f7f5e8]"></div>
                <div class="absolute bottom-[260px] left-1/2 h-10 w-1 -translate-x-1/2 bg-amber-200"></div>
                <div class="absolute bottom-16 left-8 h-40 w-12 rounded-t-full bg-[#dfe5cb]"></div>
                <div class="absolute bottom-16 right-8 h-40 w-12 rounded-t-full bg-[#dfe5cb]"></div>
                <div class="absolute bottom-[210px] left-[68px] h-10 w-1 bg-amber-200"></div>
                <div class="absolute bottom-[210px] right-[68px] h-10 w-1 bg-amber-200"></div>
                <div class="absolute bottom-0 left-0 right-0 h-2 bg-amber-200/70"></div>
            </div>
        </div>
    </section>

    <section class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        <article class="soft-shadow rounded-2xl border border-slate-100 bg-white p-5">
            <p class="text-sm text-slate-500">Saldo kas saat ini</p>
            <p class="mt-2 text-2xl font-semibold tracking-tight text-slate-900">Rp{{ number_format($balance, 0, ',', '.') }}</p>
            <p class="mt-2 text-xs text-slate-400">Akumulasi seluruh transaksi tercatat</p>
        </article>
        <article class="soft-shadow rounded-2xl border border-slate-100 bg-white p-5">
            <p class="text-sm text-slate-500">Pemasukan bulan ini</p>
            <p class="mt-2 text-2xl font-semibold tracking-tight text-mosque">Rp{{ number_format($monthIncome, 0, ',', '.') }}</p>
            <p class="mt-2 text-xs text-slate-400">{{ now()->translatedFormat('F Y') }}</p>
        </article>
        <article class="soft-shadow rounded-2xl border border-slate-100 bg-white p-5 sm:col-span-2 lg:col-span-1">
            <p class="text-sm text-slate-500">Pengeluaran bulan ini</p>
            <p class="mt-2 text-2xl font-semibold tracking-tight text-rose-700">Rp{{ number_format($monthExpense, 0, ',', '.') }}</p>
            <p class="mt-2 text-xs text-slate-400">{{ now()->translatedFormat('F Y') }}</p>
        </article>
    </section>

    <section class="mt-14">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-mosque">Salurkan kebaikan</p>
                <h2 class="mt-2 text-2xl font-semibold tracking-tight text-slate-900">Pilih tujuan infaq</h2>
            </div>
            <a href="{{ route('donations.create') }}" class="text-sm font-semibold text-mosque hover:text-mosque-dark">Semua tujuan <span aria-hidden="true">→</span></a>
        </div>
        <div class="mt-6 grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-5">
            @foreach ([
                ['🤲', 'Infaq umum', 'bg-[#eef1e5]'],
                ['🕌', 'Pembangunan', 'bg-[#f7ebe0]'],
                ['🌙', 'Infaq Jumat', 'bg-[#e9f1ef]'],
                ['💛', 'Zakat', 'bg-[#f5efdb]'],
                ['🌿', 'Sosial', 'bg-[#eeeaf4]'],
            ] as [$icon, $title, $color])
                <a href="{{ route('donations.create') }}" class="group rounded-2xl border border-slate-100 bg-white p-5 text-center transition hover:-translate-y-1 hover:shadow-lg">
                    <span class="mx-auto grid size-16 place-items-center rounded-full {{ $color }} text-2xl">{{ $icon }}</span>
                    <span class="mt-3 block text-sm font-semibold text-slate-700 group-hover:text-mosque">{{ $title }}</span>
                    <span class="mt-1 block text-xs text-slate-400">Salurkan sekarang</span>
                </a>
            @endforeach
        </div>
    </section>

    <section class="mt-14">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-mosque">Catatan terbaru</p>
                <h2 class="mt-2 text-2xl font-semibold tracking-tight text-slate-900">Ringkasan kas masjid</h2>
            </div>
            <a href="{{ route('reports.index') }}" class="text-sm font-semibold text-mosque hover:text-mosque-dark">Lihat laporan lengkap <span aria-hidden="true">→</span></a>
        </div>
        <div class="mt-5 overflow-hidden rounded-2xl border border-slate-100 bg-white">
            @forelse ($transactions as $transaction)
                <div class="flex flex-wrap items-center justify-between gap-4 border-b border-slate-100 px-5 py-4 last:border-0 sm:px-6">
                    <div class="flex items-center gap-3">
                        <span class="grid size-10 place-items-center rounded-xl {{ $transaction->type === 'income' ? 'bg-mosque-pale text-mosque' : 'bg-rose-50 text-rose-600' }}">
                            {{ $transaction->type === 'income' ? '↙' : '↗' }}
                        </span>
                        <span>
                            <span class="block font-semibold text-slate-800">{{ $transaction->category->name }}</span>
                            <span class="mt-1 block text-xs text-slate-400">{{ $transaction->date->translatedFormat('d F Y') }} · {{ $transaction->description ?: '—' }}</span>
                        </span>
                    </div>
                    <span class="font-semibold {{ $transaction->type === 'income' ? 'text-mosque' : 'text-slate-700' }}">
                        {{ $transaction->type === 'income' ? '+' : '−' }} Rp{{ number_format($transaction->amount, 0, ',', '.') }}
                    </span>
                </div>
            @empty
                <div class="px-6 py-10 text-center text-sm text-slate-500">Belum ada transaksi yang dipublikasikan.</div>
            @endforelse
        </div>
    </section>

    <section class="mt-14 overflow-hidden rounded-[2rem] bg-mosque-dark px-7 py-9 text-white sm:px-12 sm:py-11">
        <div class="flex flex-col items-start justify-between gap-6 sm:flex-row sm:items-center">
            <div class="max-w-2xl">
                <p class="text-sm font-semibold text-lime-200">Kebaikan yang terus mengalir</p>
                <h2 class="mt-2 text-3xl font-semibold tracking-tight">Satu infaq, banyak manfaat.</h2>
                <p class="mt-3 text-sm leading-6 text-white/75">Bantu kami merawat masjid dan mendukung kegiatan jamaah. Setiap donasi dicatat dan dapat dipantau.</p>
            </div>
            <a href="{{ route('donations.create') }}" class="shrink-0 rounded-xl bg-white px-5 py-3 text-sm font-semibold text-mosque-dark transition hover:bg-mosque-pale">Infaq sekarang <span aria-hidden="true">→</span></a>
        </div>
    </section>
</div>
@endsection
