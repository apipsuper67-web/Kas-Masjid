@extends('layouts.app')

@section('title', 'Masuk')

@section('content')
<section class="mx-auto max-w-lg px-5 py-16">
    <div class="rounded-3xl border border-slate-100 bg-white p-7 shadow-sm sm:p-10">
        <p class="text-sm font-semibold text-mosque">Selamat datang kembali</p>
        <h1 class="mt-2 text-3xl font-semibold tracking-tight text-slate-900">Masuk ke akun</h1>
        <p class="mt-2 text-sm text-slate-500">Pantau infaq atau kelola catatan kas masjid Anda.</p>
        <form class="mt-8 space-y-5" method="POST" action="{{ route('login.store') }}">
            @csrf
            <div>
                <label for="email" class="mb-2 block text-sm font-medium text-slate-700">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="email" class="w-full rounded-xl border border-slate-200 px-4 py-3 outline-none transition focus:border-mosque focus:ring-2 focus:ring-mosque/15">
            </div>
            <div>
                <label for="password" class="mb-2 block text-sm font-medium text-slate-700">Kata sandi</label>
                <input id="password" name="password" type="password" required autocomplete="current-password" class="w-full rounded-xl border border-slate-200 px-4 py-3 outline-none transition focus:border-mosque focus:ring-2 focus:ring-mosque/15">
            </div>
            <label class="flex items-center gap-2 text-sm text-slate-600">
                <input type="checkbox" name="remember" value="1" class="rounded border-slate-300 text-mosque focus:ring-mosque">
                Ingat saya
            </label>
            <button class="w-full rounded-xl bg-mosque px-4 py-3 font-semibold text-white transition hover:bg-mosque-dark">Masuk</button>
        </form>
        <p class="mt-6 text-center text-sm text-slate-500">Belum punya akun? <a class="font-semibold text-mosque hover:underline" href="{{ route('register') }}">Daftar sebagai jamaah</a></p>
    </div>
</section>
@endsection
