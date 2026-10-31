@extends('layouts.app')

@section('title', 'Dashboard jamaah')

@section('content')
<div class="mx-auto max-w-7xl px-5 py-10 lg:px-8">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="text-sm font-medium text-mosque">Ruang jamaah</p>
            <h1 class="mt-1 text-3xl font-semibold tracking-tight text-slate-900">Assalamu'alaikum, {{ auth()->user()->name }}</h1>
            <p class="mt-2 text-sm text-slate-500">Terima kasih telah ikut menjaga amanah masjid.</p>
        </div>
        <a href="{{ route('donations.create') }}" class="rounded-xl bg-mosque px-5 py-3 text-sm font-semibold text-white hover:bg-mosque-dark">+ Infaq sekarang</a>
    </div>
    <section class="mt-8 rounded-2xl bg-mosque-dark p-6 text-white sm:p-8">
        <p class="text-sm text-white/70">Total kontribusi berhasil</p>
        <p class="mt-2 text-3xl font-semibold">Rp{{ number_format($totalContribution, 0, ',', '.') }}</p>
        <p class="mt-3 text-sm text-white/70">Kontribusi Anda membantu kegiatan dan perawatan masjid.</p>
    </section>
    <section class="mt-8 rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-slate-900">Riwayat infaq</h2>
            <a href="{{ route('donations.create') }}" class="text-sm font-semibold text-mosque hover:underline">Buat infaq baru →</a>
        </div>
        <div class="mt-4 divide-y divide-slate-100">
            @forelse ($donations as $donation)
                <div class="flex flex-wrap items-center justify-between gap-4 py-4">
                    <div>
                        <p class="font-medium capitalize text-slate-800">{{ str_replace('_', ' ', $donation->purpose) }}</p>
                        <p class="mt-1 text-xs text-slate-500">{{ $donation->created_at->translatedFormat('d F Y') }} · {{ $donation->payment_reference }}</p>
                    </div>
                    <div class="text-right">
                        <p class="font-semibold text-slate-800">Rp{{ number_format($donation->amount, 0, ',', '.') }}</p>
                        <span class="mt-1 inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $donation->status === 'berhasil' ? 'bg-emerald-50 text-emerald-700' : ($donation->status === 'pending' ? 'bg-amber-50 text-amber-700' : 'bg-rose-50 text-rose-700') }}">{{ ucfirst($donation->status) }}</span>
                    </div>
                </div>
            @empty
                <p class="py-10 text-center text-sm text-slate-500">Belum ada riwayat infaq.</p>
            @endforelse
        </div>
        <div class="mt-4">{{ $donations->links() }}</div>
    </section>
</div>
@endsection
