<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\MosqueSetting;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        foreach ([
            ['name' => 'Infaq Jumat', 'type' => 'income'],
            ['name' => 'Zakat', 'type' => 'income'],
            ['name' => 'Sedekah', 'type' => 'income'],
            ['name' => 'Donasi Pembangunan', 'type' => 'income'],
            ['name' => 'Donasi Infaq', 'type' => 'income'],
            ['name' => 'Listrik', 'type' => 'expense'],
            ['name' => 'Air', 'type' => 'expense'],
            ['name' => 'Kebersihan', 'type' => 'expense'],
            ['name' => 'Honor Imam', 'type' => 'expense'],
            ['name' => 'Perbaikan', 'type' => 'expense'],
        ] as $category) {
            Category::query()->firstOrCreate($category);
        }

        MosqueSetting::query()->firstOrCreate([], [
            'name' => 'Masjid Kita',
            'address' => 'Alamat masjid belum diatur.',
        ]);

        $adminEmail = config('kas-masjid.admin.email');
        $adminPassword = config('kas-masjid.admin.password');

        if (filled($adminEmail) && filled($adminPassword)) {
            User::query()->firstOrCreate(
                ['email' => $adminEmail],
                [
                    'name' => config('kas-masjid.admin.name'),
                    'password' => $adminPassword,
                    'role' => 'admin',
                ],
            );
        }
    }
}
