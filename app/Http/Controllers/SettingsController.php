<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Category;
use App\Models\MosqueSetting;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class SettingsController extends Controller
{
    public function logo(): BinaryFileResponse
    {
        $path = MosqueSetting::query()->value('logo_path');

        abort_unless($path && Storage::disk('public')->exists($path), 404);

        return response()->file(Storage::disk('public')->path($path), [
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    public function index(): View
    {
        return view('settings.index', [
            'settings' => MosqueSetting::query()->firstOrCreate([], ['name' => 'Masjid Kita']),
            'categories' => Category::query()->orderBy('type')->orderBy('name')->get(),
            'users' => User::query()->orderBy('role')->orderBy('name')->paginate(10),
            'activityLogs' => ActivityLog::query()->with('user:id,name')->latest()->limit(8)->get(),
        ]);
    }

    public function updateMosque(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:160'],
            'address' => ['nullable', 'string', 'max:2000'],
            'bank_name' => ['nullable', 'string', 'max:120'],
            'bank_account' => ['nullable', 'string', 'max:60'],
            'account_holder' => ['nullable', 'string', 'max:160'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'extensions:jpg,jpeg,png', 'max:2048'],
        ]);
        $settings = MosqueSetting::query()->firstOrCreate([], ['name' => $validated['name']]);
        $oldLogo = $settings->logo_path;

        if ($request->hasFile('logo')) {
            $validated['logo_path'] = $request->file('logo')->store('mosque-logo', 'public');
        }

        unset($validated['logo']);
        $settings->update($validated);

        if ($oldLogo && $request->hasFile('logo')) {
            Storage::disk('public')->delete($oldLogo);
        }

        $this->log($request, 'pengaturan.masjid.diubah', 'Memperbarui profil masjid.');

        return back()->with('success', 'Profil masjid berhasil diperbarui.');
    }

    public function storeCategory(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('categories')->where('type', $request->input('type')),
            ],
            'type' => ['required', 'in:income,expense'],
        ]);

        $category = Category::create($validated);
        $this->log($request, 'kategori.dibuat', 'Menambahkan kategori '.$category->name.'.', $category);

        return back()->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function destroyCategory(Request $request, Category $category): RedirectResponse
    {
        if ($category->type === 'income' && $category->name === 'Donasi Infaq') {
            return back()->withErrors(['category' => 'Kategori khusus infaq tidak dapat dihapus.']);
        }

        if ($category->transactions()->exists()) {
            return back()->withErrors(['category' => 'Kategori yang sudah dipakai transaksi tidak dapat dihapus.']);
        }

        $categoryName = $category->name;
        $category->delete();
        $this->log($request, 'kategori.dihapus', 'Menghapus kategori '.$categoryName.'.');

        return back()->with('success', 'Kategori berhasil dihapus.');
    }

    public function storeUser(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:30'],
            'role' => ['required', 'in:admin,bendahara'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user = User::create($validated);
        $this->log($request, 'pengguna.dibuat', 'Membuat akun pengurus '.$user->email.'.', $user);

        return back()->with('success', 'Akun pengurus berhasil dibuat.');
    }

    public function updateUserRole(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'role' => ['required', 'in:admin,bendahara,jamaah'],
        ]);

        if ($user->is($request->user()) && $validated['role'] !== 'admin') {
            return back()->withErrors(['role' => 'Anda tidak dapat mengubah role admin pada akun sendiri.']);
        }

        if ($user->role === 'admin' && $validated['role'] !== 'admin'
            && User::query()->where('role', 'admin')->count() === 1) {
            return back()->withErrors(['role' => 'Minimal harus ada satu akun admin.']);
        }

        $oldRole = $user->role;
        $user->update(['role' => $validated['role']]);
        $this->log($request, 'pengguna.role.diubah', 'Mengubah role '.$user->email.' dari '.$oldRole.' menjadi '.$user->role.'.', $user);

        return back()->with('success', 'Role pengguna berhasil diperbarui.');
    }

    private function log(Request $request, string $action, string $description, ?Model $subject = null): void
    {
        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => $action,
            'description' => $description,
            'subject_type' => $subject?->getMorphClass(),
            'subject_id' => $subject?->id,
        ]);
    }
}
