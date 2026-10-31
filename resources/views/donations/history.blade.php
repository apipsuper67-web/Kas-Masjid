@extends('layouts.app')

@section('title', 'Riwayat infaq')

@section('content')
<section class="mx-auto max-w-5xl px-5 py-10 lg:px-8">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="text-sm font-medium text-mosque">Akun jamaah</p>
            <h1 class="mt-1 text-3xl font-semibold tracking-tight text-slate-900">Riwayat infaq</h1>
        </div>
        <a href="{{ route('donations.create') }}" class="rounded-xl bg-mosque px-5 py-3 text-sm font-semibold text-white hover:bg-mosque-dark">+ Infaq baru</a>
    </div>
    <div class="mt-6 divide-y divide-slate-100 rounded-2xl border border-slate-100 bg-white px-5 shadow-sm">
        @forelse ($donations as $donation)
            <div class="flex flex-wrap items-center justify-between gap-4 py-5">
                <div>
                    <p class="font-semibold capitalize text-slate-800">{{ str_replace('_', ' ', $donation->purpose) }}</p>
                    <p class="mt-1 text-xs text-slate-500">{{ $donation->created_at->translatedFormat('d F Y') }} · {{ $donation->payment_reference }}</p>
                </div>
                <div class="text-right">
                    <p class="font-semibold text-slate-900">Rp{{ number_format($donation->amount, 0, ',', '.') }}</p>
                    <span class="mt-1 inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $donation->status === 'berhasil' ? 'bg-emerald-50 text-emerald-700' : ($donation->status === 'pending' ? 'bg-amber-50 text-amber-700' : 'bg-rose-50 text-rose-700') }}">{{ ucfirst($donation->status) }}</span>
                    @if ($donation->status === 'berhasil')
                        <a href="{{ route('donations.receipt', $donation->payment_reference) }}" class="mt-1 block text-xs font-semibold text-mosque hover:underline">Unduh bukti PDF</a>
                    @endif
                </div>
            </div>
        @empty
            <p class="py-12 text-center text-sm text-slate-500">Riwayat infaq Anda masih kosong.</p>
        @endforelse
    </div>
    <div class="mt-5">{{ $donations->links() }}</div>
</section>
@endsection
