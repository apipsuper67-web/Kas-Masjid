@extends('layouts.app')

@section('title', 'Pengaturan masjid')

@section('content')
<div class="mx-auto max-w-7xl px-5 py-10 lg:px-8">
    <div>
        <p class="text-sm font-medium text-mosque">Khusus admin</p>
        <h1 class="mt-1 text-3xl font-semibold tracking-tight text-slate-900">Pengaturan masjid</h1>
        <p class="mt-2 text-sm text-slate-500">Kelola profil, kategori transaksi, dan akun pengurus.</p>
    </div>

    <section class="mt-7 rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
        <h2 class="font-semibold text-slate-900">Profil masjid</h2>
        <form class="mt-5 grid gap-4 sm:grid-cols-2" method="POST" action="{{ route('admin.settings.mosque') }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div>
                <label for="name" class="mb-2 block text-sm font-medium text-slate-700">Nama masjid</label>
                <input id="name" name="name" value="{{ old('name', $settings->name) }}" required class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:border-mosque focus:outline-none">
            </div>
            <div>
                <label for="bank_name" class="mb-2 block text-sm font-medium text-slate-700">Nama bank</label>
                <input id="bank_name" name="bank_name" value="{{ old('bank_name', $settings->bank_name) }}" class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:border-mosque focus:outline-none">
            </div>
            <div class="sm:col-span-2">
                <label for="address" class="mb-2 block text-sm font-medium text-slate-700">Alamat</label>
                <textarea id="address" name="address" rows="2" class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:border-mosque focus:outline-none">{{ old('address', $settings->address) }}</textarea>
            </div>
            <div>
                <label for="bank_account" class="mb-2 block text-sm font-medium text-slate-700">Nomor rekening</label>
                <input id="bank_account" name="bank_account" value="{{ old('bank_account', $settings->bank_account) }}" class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:border-mosque focus:outline-none">
            </div>
            <div>
                <label for="account_holder" class="mb-2 block text-sm font-medium text-slate-700">Nama pemilik rekening</label>
                <input id="account_holder" name="account_holder" value="{{ old('account_holder', $settings->account_holder) }}" class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:border-mosque focus:outline-none">
            </div>
            <div class="sm:col-span-2">
                <label for="logo" class="mb-2 block text-sm font-medium text-slate-700">Logo masjid <span class="font-normal text-slate-400">(JPG/PNG, maks. 2 MB)</span></label>
                <input id="logo" name="logo" type="file" accept=".jpg,.jpeg,.png" class="block w-full rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-600 file:mr-3 file:rounded-lg file:border-0 file:bg-mosque-pale file:px-3 file:py-2 file:font-semibold file:text-mosque">
                @if ($settings->logo_path)
                    <img src="{{ route('mosque.logo') }}" alt="Logo {{ $settings->name }}" class="mt-3 size-16 rounded-xl border border-slate-100 object-cover">
                @endif
            </div>
            <div class="sm:col-span-2">
                <button class="rounded-xl bg-mosque px-5 py-3 text-sm font-semibold text-white hover:bg-mosque-dark">Simpan profil</button>
            </div>
        </form>
    </section>

    <div class="mt-6 grid gap-6 lg:grid-cols-2">
        <section class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
            <h2 class="font-semibold text-slate-900">Kategori transaksi</h2>
            <form class="mt-4 flex flex-wrap gap-2" method="POST" action="{{ route('admin.categories.store') }}">
                @csrf
                <input name="name" required placeholder="Nama kategori" class="min-w-40 flex-1 rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:border-mosque focus:outline-none">
                <select name="type" class="rounded-xl border border-slate-200 px-3 py-2.5 text-sm">
                    <option value="income">Pemasukan</option>
                    <option value="expense">Pengeluaran</option>
                </select>
                <button class="rounded-xl bg-mosque px-4 py-2.5 text-sm font-semibold text-white">Tambah</button>
            </form>
            <div class="mt-5 divide-y divide-slate-100">
                @foreach ($categories as $category)
                    <div class="flex items-center justify-between gap-3 py-3">
                        <div>
                            <p class="text-sm font-medium text-slate-800">{{ $category->name }}</p>
                            <p class="mt-1 text-xs text-slate-500">{{ $category->type === 'income' ? 'Pemasukan' : 'Pengeluaran' }}</p>
                        </div>
                        <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" onsubmit="return confirm('Hapus kategori ini?')">
                            @csrf
                            @method('DELETE')
                            <button class="rounded-lg border border-rose-100 px-3 py-2 text-xs font-medium text-rose-700 hover:bg-rose-50">Hapus</button>
                        </form>
                    </div>
                @endforeach
            </div>
        </section>

        <section class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
            <h2 class="font-semibold text-slate-900">Tambah akun pengurus</h2>
            <form class="mt-4 grid gap-3 sm:grid-cols-2" method="POST" action="{{ route('admin.users.store') }}">
                @csrf
                <input name="name" required placeholder="Nama lengkap" class="rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:border-mosque focus:outline-none">
                <input name="email" type="email" required placeholder="Alamat email" class="rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:border-mosque focus:outline-none">
                <input name="phone" placeholder="Nomor telepon (opsional)" class="rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:border-mosque focus:outline-none">
                <select name="role" class="rounded-xl border border-slate-200 px-3 py-2.5 text-sm">
                    <option value="bendahara">Bendahara</option>
                    <option value="admin">Admin</option>
                </select>
                <input name="password" type="password" required placeholder="Kata sandi (min. 8 karakter)" class="rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:border-mosque focus:outline-none">
                <input name="password_confirmation" type="password" required placeholder="Ulangi kata sandi" class="rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:border-mosque focus:outline-none">
                <button class="rounded-xl bg-mosque px-4 py-2.5 text-sm font-semibold text-white sm:col-span-2">Buat akun</button>
            </form>
        </section>
    </div>

    <section class="mt-6 overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm">
        <div class="border-b border-slate-100 px-5 py-4"><h2 class="font-semibold text-slate-900">Pengguna</h2></div>
        <div class="divide-y divide-slate-100">
            @foreach ($users as $user)
                <div class="flex flex-wrap items-center justify-between gap-4 px-5 py-4">
                    <div>
                        <p class="font-medium text-slate-800">{{ $user->name }}</p>
                        <p class="mt-1 text-xs text-slate-500">{{ $user->email }} · {{ $user->phone ?: 'Tanpa nomor telepon' }}</p>
                    </div>
                    <form class="flex items-center gap-2" method="POST" action="{{ route('admin.users.role', $user) }}">
                        @csrf
                        @method('PATCH')
                        <select name="role" class="rounded-lg border border-slate-200 px-3 py-2 text-xs">
                            @foreach (['admin' => 'Admin', 'bendahara' => 'Bendahara', 'jamaah' => 'Jamaah'] as $role => $label)
                                <option value="{{ $role }}" @selected($user->role === $role)>{{ $label }}</option>
                            @endforeach
                        </select>
                        <button class="rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-600 hover:border-mosque">Simpan</button>
                    </form>
                </div>
            @endforeach
        </div>
        <div class="border-t border-slate-100 px-5 py-4">{{ $users->links() }}</div>
    </section>

    <section class="mt-6 rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
        <h2 class="font-semibold text-slate-900">Log aktivitas terbaru</h2>
        <div class="mt-4 divide-y divide-slate-100">
            @forelse ($activityLogs as $activity)
                <div class="flex flex-wrap justify-between gap-3 py-3">
                    <div>
                        <p class="text-sm font-medium text-slate-700">{{ $activity->description }}</p>
                        <p class="mt-1 text-xs text-slate-500">{{ $activity->user?->name ?? 'Sistem' }} · {{ $activity->created_at->translatedFormat('d F Y H:i') }}</p>
                    </div>
                    <span class="text-xs text-slate-400">{{ $activity->action }}</span>
                </div>
            @empty
                <p class="py-6 text-sm text-slate-500">Belum ada aktivitas tercatat.</p>
            @endforelse
        </div>
    </section>
</div>
@endsection
