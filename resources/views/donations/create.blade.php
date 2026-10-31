@extends('layouts.app')

@section('title', 'Infaq online')

@section('content')
<section class="mx-auto grid max-w-7xl gap-8 px-5 py-10 lg:grid-cols-[1fr_0.8fr] lg:px-8">
    <div class="rounded-3xl bg-mosque-dark p-7 text-white sm:p-10">
        <p class="text-sm font-semibold text-lime-200">Infaq online</p>
        <h1 class="mt-3 text-3xl font-semibold tracking-tight sm:text-4xl">Titipkan kebaikan Anda.</h1>
        <p class="mt-4 max-w-xl text-sm leading-6 text-white/75">Pilih tujuan dan nominal infaq. Transfer dapat diverifikasi pengurus, lalu dicatat sebagai pemasukan kas masjid.</p>
        <div class="mt-8 rounded-2xl border border-white/15 bg-white/10 p-5">
            <p class="text-sm font-semibold">Transfer manual</p>
            <p class="mt-2 text-sm text-white/75">Kirim ke rekening resmi masjid berikut. Unggah bukti transfer agar pengurus dapat memverifikasi donasi Anda.</p>
            @if ($settings?->bank_account)
                <div class="mt-4 rounded-xl bg-white/10 p-4">
                    <p class="text-xs text-white/60">{{ $settings->bank_name }}</p>
                    <p class="mt-1 text-xl font-semibold tracking-wide">{{ $settings->bank_account }}</p>
                    <p class="mt-1 text-sm text-white/75">a.n. {{ $settings->account_holder ?? $settings->name }}</p>
                </div>
            @else
                <p class="mt-4 rounded-xl bg-white/10 p-4 text-sm text-white/75">Informasi rekening belum diatur. Silakan hubungi pengurus masjid untuk detail transfer.</p>
            @endif
        </div>
        <p class="mt-6 text-xs leading-5 text-white/60">Data transfer diverifikasi oleh admin. Jangan mengirimkan PIN, OTP, atau kata sandi perbankan Anda.</p>
    </div>
    <div class="rounded-3xl border border-slate-100 bg-white p-6 shadow-sm sm:p-9">
        <h2 class="text-xl font-semibold text-slate-900">Formulir infaq</h2>
        <p class="mt-2 text-sm text-slate-500">Data donatur hanya digunakan untuk pencatatan infaq.</p>
        <form class="mt-7 space-y-5" method="POST" action="{{ route('donations.store') }}" enctype="multipart/form-data">
            @csrf
            <div>
                <label for="donor_name" class="mb-2 block text-sm font-medium text-slate-700">Nama donatur</label>
                <input id="donor_name" name="donor_name" value="{{ old('donor_name', auth()->user()?->name) }}" class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:border-mosque focus:outline-none">
                <label class="mt-3 flex items-center gap-2 text-sm text-slate-600">
                    <input type="hidden" name="is_anonymous" value="0">
                    <input type="checkbox" name="is_anonymous" value="1" @checked(old('is_anonymous')) class="rounded border-slate-300 text-mosque focus:ring-mosque">
                    Tampilkan sebagai Hamba Allah
                </label>
            </div>
            <div>
                <label for="amount" class="mb-2 block text-sm font-medium text-slate-700">Nominal infaq (Rp)</label>
                <input id="amount" name="amount" type="number" min="1000" step="1000" value="{{ old('amount') }}" required placeholder="Contoh: 50000" class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:border-mosque focus:outline-none">
            </div>
            <div>
                <label for="purpose" class="mb-2 block text-sm font-medium text-slate-700">Tujuan infaq</label>
                <select id="purpose" name="purpose" required class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:border-mosque focus:outline-none">
                    <option value="">Pilih tujuan</option>
                    @foreach (['infaq_umum' => 'Infaq umum', 'infaq_jumat' => 'Infaq Jumat', 'pembangunan' => 'Pembangunan masjid', 'zakat' => 'Zakat', 'sosial' => 'Kegiatan sosial'] as $value => $label)
                        <option value="{{ $value }}" @selected(old('purpose') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="message" class="mb-2 block text-sm font-medium text-slate-700">Pesan <span class="font-normal text-slate-400">(opsional)</span></label>
                <textarea id="message" name="message" rows="2" class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:border-mosque focus:outline-none">{{ old('message') }}</textarea>
            </div>
            <div>
                <label for="proof_file" class="mb-2 block text-sm font-medium text-slate-700">Bukti transfer <span class="font-normal text-slate-400">(opsional, maks. 5 MB)</span></label>
                <input id="proof_file" name="proof_file" type="file" accept=".jpg,.jpeg,.png,.pdf" class="block w-full rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-600 file:mr-3 file:rounded-lg file:border-0 file:bg-mosque-pale file:px-3 file:py-2 file:font-semibold file:text-mosque">
            </div>
            <button class="w-full rounded-xl bg-mosque px-5 py-3 font-semibold text-white hover:bg-mosque-dark">Kirim pengajuan infaq</button>
            <p class="text-center text-xs text-slate-400">Dengan mengirim formulir, Anda menyetujui data ini digunakan untuk verifikasi donasi.</p>
        </form>
    </div>
</section>
@endsection
