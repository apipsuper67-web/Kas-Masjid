<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Kas Masjid') · {{ ($settings ?? null)?->name ?? 'Kas Masjid' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans text-slate-800 antialiased">
    <header class="border-b border-slate-100 bg-white">
        <div class="mx-auto flex max-w-7xl flex-wrap items-center justify-between gap-4 px-5 py-4 lg:px-8">
            <a href="{{ route('home') }}" class="flex items-center gap-3">
                @if (($settings ?? null)?->logo_path)
                    <img src="{{ route('mosque.logo') }}" alt="Logo {{ $settings->name }}" class="size-11 rounded-2xl object-cover">
                @else
                    <span class="grid size-11 place-items-center rounded-2xl bg-mosque text-lg font-bold text-white">K</span>
                @endif
                <span>
                    <span class="block text-base font-bold tracking-tight text-slate-900">Kas Masjid</span>
                    <span class="block text-xs text-slate-500">{{ ($settings ?? null)?->name ?? 'Amanah bersama' }}</span>
                </span>
            </a>
            <nav class="flex flex-wrap items-center gap-x-6 gap-y-2 text-sm font-medium text-slate-600">
                <a class="transition hover:text-mosque" href="{{ route('home') }}">Beranda</a>
                <a class="transition hover:text-mosque" href="{{ route('reports.index') }}">Laporan kas</a>
                <a class="transition hover:text-mosque" href="{{ route('donations.create') }}">Infaq</a>
                @auth
                    <a class="transition hover:text-mosque" href="{{ route('dashboard') }}">Dashboard</a>
                    @if (auth()->user()->hasAnyRole('admin', 'bendahara'))
                        <a class="transition hover:text-mosque" href="{{ route('transactions.index') }}">Transaksi</a>
                    @endif
                    @if (auth()->user()->hasAnyRole('admin'))
                        <a class="transition hover:text-mosque" href="{{ route('admin.settings.index') }}">Pengaturan</a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="rounded-full border border-slate-200 px-4 py-2 transition hover:border-mosque hover:text-mosque">Keluar</button>
                    </form>
                @else
                    <a class="transition hover:text-mosque" href="{{ route('login') }}">Masuk</a>
                    <a class="rounded-full bg-mosque px-5 py-2.5 text-white transition hover:bg-mosque-dark" href="{{ route('register') }}">Daftar</a>
                @endauth
            </nav>
        </div>
    </header>

    @if (session('success'))
        <div class="mx-auto mt-5 max-w-7xl px-5 lg:px-8">
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>
        </div>
    @endif

    @if ($errors->any())
        <div class="mx-auto mt-5 max-w-7xl px-5 lg:px-8">
            <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
                <p class="font-semibold">Mohon periksa kembali data yang diisi.</p>
                <ul class="mt-1 list-inside list-disc">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <main>
        @yield('content')
    </main>

    <footer class="mt-20 border-t border-slate-100 bg-white">
        <div class="mx-auto grid max-w-7xl gap-10 px-5 py-12 md:grid-cols-3 lg:px-8">
            <div>
                <div class="flex items-center gap-3">
                    <span class="grid size-10 place-items-center rounded-xl bg-mosque text-lg font-bold text-white">K</span>
                    <span class="font-bold text-slate-900">Kas Masjid</span>
                </div>
                <p class="mt-4 max-w-sm text-sm leading-6 text-slate-500">Mencatat amanah, menjaga kepercayaan. Bersama jamaah, kita kuatkan rumah ibadah.</p>
            </div>
            <div>
                <h2 class="font-semibold text-slate-900">Tautan cepat</h2>
                <div class="mt-4 flex flex-col gap-3 text-sm text-slate-500">
                    <a class="hover:text-mosque" href="{{ route('home') }}">Beranda</a>
                    <a class="hover:text-mosque" href="{{ route('reports.index') }}">Laporan keuangan</a>
                    <a class="hover:text-mosque" href="{{ route('donations.create') }}">Infaq online</a>
                </div>
            </div>
            <div>
                <h2 class="font-semibold text-slate-900">Informasi masjid</h2>
                <p class="mt-4 text-sm leading-6 text-slate-500">{{ ($settings ?? null)?->address ?? 'Alamat masjid dapat diperbarui melalui pengaturan.' }}</p>
                @if (($settings ?? null)?->bank_name || ($settings ?? null)?->bank_account)
                    <p class="mt-2 text-sm text-slate-600">{{ $settings->bank_name }} · {{ $settings->bank_account }}</p>
                @endif
            </div>
        </div>
        <div class="border-t border-slate-100 py-4 text-center text-xs text-slate-400">© {{ now()->year }} {{ $settings?->name ?? 'Kas Masjid' }} · Transparan dan amanah</div>
    </footer>
    @stack('scripts')
</body>
</html>
