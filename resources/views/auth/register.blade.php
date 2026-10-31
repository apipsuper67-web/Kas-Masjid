@extends('layouts.app')

@section('title', 'Daftar')

@section('content')
<section class="mx-auto max-w-lg px-5 py-16">
    <div class="rounded-3xl border border-slate-100 bg-white p-7 shadow-sm sm:p-10">
        <p class="text-sm font-semibold text-mosque">Jadi bagian dari kebaikan</p>
        <h1 class="mt-2 text-3xl font-semibold tracking-tight text-slate-900">Daftar jamaah</h1>
        <p class="mt-2 text-sm text-slate-500">Buat akun untuk melihat riwayat infaq pribadi Anda.</p>
        <form class="mt-8 space-y-5" method="POST" action="{{ route('register.store') }}">
            @csrf
            <div>
                <label for="name" class="mb-2 block text-sm font-medium text-slate-700">Nama lengkap</label>
                <input id="name" name="name" type="text" value="{{ old('name') }}" required autocomplete="name" class="w-full rounded-xl border border-slate-200 px-4 py-3 outline-none focus:border-mosque focus:ring-2 focus:ring-mosque/15">
            </div>
            <div>
                <label for="email" class="mb-2 block text-sm font-medium text-slate-700">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required autocomplete="email" class="w-full rounded-xl border border-slate-200 px-4 py-3 outline-none focus:border-mosque focus:ring-2 focus:ring-mosque/15">
            </div>
            <div>
                <label for="phone" class="mb-2 block text-sm font-medium text-slate-700">Nomor telepon <span class="font-normal text-slate-400">(opsional)</span></label>
                <input id="phone" name="phone" type="tel" value="{{ old('phone') }}" autocomplete="tel" class="w-full rounded-xl border border-slate-200 px-4 py-3 outline-none focus:border-mosque focus:ring-2 focus:ring-mosque/15">
            </div>
            <div>
                <label for="password" class="mb-2 block text-sm font-medium text-slate-700">Kata sandi</label>
                <input id="password" name="password" type="password" required autocomplete="new-password" class="w-full rounded-xl border border-slate-200 px-4 py-3 outline-none focus:border-mosque focus:ring-2 focus:ring-mosque/15">
            </div>
            <div>
                <label for="password_confirmation" class="mb-2 block text-sm font-medium text-slate-700">Ulangi kata sandi</label>
                <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password" class="w-full rounded-xl border border-slate-200 px-4 py-3 outline-none focus:border-mosque focus:ring-2 focus:ring-mosque/15">
            </div>
            <button class="w-full rounded-xl bg-mosque px-4 py-3 font-semibold text-white transition hover:bg-mosque-dark">Buat akun</button>
        </form>
        <p class="mt-6 text-center text-sm text-slate-500">Sudah terdaftar? <a class="font-semibold text-mosque hover:underline" href="{{ route('login') }}">Masuk</a></p>
    </div>
</section>
@endsection
