@extends('layouts.app')

@section('title', 'Konfirmasi infaq')

@section('content')
<section class="mx-auto max-w-2xl px-5 py-14">
    <div class="rounded-3xl border border-slate-100 bg-white p-7 text-center shadow-sm sm:p-10">
        <span class="mx-auto grid size-16 place-items-center rounded-full bg-amber-50 text-3xl">🤲</span>
        <p class="mt-5 text-sm font-semibold text-mosque">Terima kasih atas niat baik Anda</p>
        <h1 class="mt-2 text-3xl font-semibold tracking-tight text-slate-900">Pengajuan infaq diterima</h1>
        <p class="mt-3 text-sm leading-6 text-slate-500">Pengurus akan memeriksa transfer dan bukti yang dikirim. Infaq baru masuk ke saldo setelah diverifikasi.</p>
        <div class="mt-7 rounded-2xl bg-slate-50 p-5 text-left">
            <div class="flex justify-between gap-4 text-sm"><span class="text-slate-500">Nomor referensi</span><span class="font-semibold text-slate-800">{{ $donation->payment_reference }}</span></div>
            <div class="mt-3 flex justify-between gap-4 text-sm"><span class="text-slate-500">Nominal</span><span class="font-semibold text-slate-800">Rp{{ number_format($donation->amount, 0, ',', '.') }}</span></div>
            <div class="mt-3 flex justify-between gap-4 text-sm"><span class="text-slate-500">Status</span><span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $donation->status === 'berhasil' ? 'bg-emerald-100 text-emerald-800' : ($donation->status === 'pending' ? 'bg-amber-100 text-amber-800' : 'bg-rose-100 text-rose-800') }}">{{ ucfirst($donation->status) }}</span></div>
        </div>
        @if ($donation->status === 'berhasil')
            <a href="{{ route('donations.receipt', $donation->payment_reference) }}" class="mt-5 inline-flex rounded-xl bg-mosque px-5 py-3 text-sm font-semibold text-white hover:bg-mosque-dark">Unduh bukti infaq (PDF)</a>
        @endif
        @if ($settings?->bank_account)
            <div class="mt-4 rounded-2xl border border-mosque/15 bg-mosque-pale p-5 text-left">
                <p class="text-sm font-semibold text-slate-800">Rekening tujuan transfer</p>
                <p class="mt-2 text-sm text-slate-600">{{ $settings->bank_name }} · <span class="font-semibold">{{ $settings->bank_account }}</span></p>
                <p class="mt-1 text-sm text-slate-600">a.n. {{ $settings->account_holder ?? $settings->name }}</p>
            </div>
        @endif
        <div class="mt-7 flex flex-wrap justify-center gap-3">
            <a href="{{ route('home') }}" class="rounded-xl border border-slate-200 px-5 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50">Kembali ke beranda</a>
            @auth
                <a href="{{ route('dashboard') }}" class="rounded-xl bg-mosque px-5 py-3 text-sm font-semibold text-white hover:bg-mosque-dark">Lihat dashboard</a>
            @else
                <a href="{{ route('register') }}" class="rounded-xl bg-mosque px-5 py-3 text-sm font-semibold text-white hover:bg-mosque-dark">Daftar untuk melihat riwayat</a>
            @endauth
        </div>
    </div>
</section>
@endsection
