<?php

use App\Exports\ReportExport;
use App\Models\Category;
use App\Models\Donation;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

it('shows public finance figures and a transparent report', function () {
    $incomeCategory = Category::factory()->create(['name' => 'Infaq Jumat', 'type' => 'income']);
    $expenseCategory = Category::factory()->create(['name' => 'Listrik', 'type' => 'expense']);
    $cashier = User::factory()->create(['role' => 'bendahara']);

    Transaction::factory()->create([
        'category_id' => $incomeCategory->id,
        'type' => 'income',
        'amount' => 150000,
        'date' => now()->startOfMonth()->toDateString(),
        'created_by' => $cashier->id,
    ]);
    Transaction::factory()->create([
        'category_id' => $expenseCategory->id,
        'type' => 'expense',
        'amount' => 25000,
        'date' => now()->startOfMonth()->toDateString(),
        'created_by' => $cashier->id,
    ]);

    $this->get('/')
        ->assertOk()
        ->assertSee('Saldo kas saat ini')
        ->assertSee('Rp125.000');

    $this->get(route('reports.index', ['month' => now()->month, 'year' => now()->year]))
        ->assertOk()
        ->assertSee('Saldo awal')
        ->assertSee('Rp125.000');
});

it('renders public donation and authentication forms in Indonesian', function () {
    $this->get(route('donations.create'))
        ->assertOk()
        ->assertSee('Formulir infaq')
        ->assertSee('Transfer manual');

    $this->get(route('login'))->assertOk()->assertSee('Masuk ke akun');
    $this->get(route('register'))->assertOk()->assertSee('Daftar jamaah');
});

it('allows a cashier to create an audited transaction with a private proof', function () {
    Storage::fake('local');
    $cashier = User::factory()->create(['role' => 'bendahara']);
    $category = Category::factory()->create(['name' => 'Infaq Jumat', 'type' => 'income']);

    $response = $this->actingAs($cashier)->post(route('transactions.store'), [
        'type' => 'income',
        'category_id' => $category->id,
        'amount' => 75000,
        'date' => now()->toDateString(),
        'description' => 'Infaq pekan ini',
        'proof_file' => UploadedFile::fake()->createWithContent('bukti.pdf', '%PDF-1.4 test'),
    ]);

    $response->assertRedirect(route('transactions.index'));
    $this->assertDatabaseHas('transactions', [
        'type' => 'income',
        'amount' => 75000,
        'description' => 'Infaq pekan ini',
        'created_by' => $cashier->id,
    ]);
    $this->assertDatabaseHas('activity_logs', [
        'user_id' => $cashier->id,
        'action' => 'transaksi.dibuat',
    ]);

    $transaction = Transaction::query()->firstOrFail();
    Storage::disk('local')->assertExists($transaction->proof_file);
});

it('rejects transaction writes from jamaah and category type mismatches', function () {
    $jamaah = User::factory()->create(['role' => 'jamaah']);
    $incomeCategory = Category::factory()->create(['type' => 'income']);
    $cashier = User::factory()->create(['role' => 'bendahara']);

    $this->actingAs($jamaah)->post(route('transactions.store'), [
        'type' => 'income',
        'category_id' => $incomeCategory->id,
        'amount' => 20000,
        'date' => now()->toDateString(),
    ])->assertForbidden();

    $this->actingAs($cashier)->from(route('transactions.create'))->post(route('transactions.store'), [
        'type' => 'expense',
        'category_id' => $incomeCategory->id,
        'amount' => 20000,
        'date' => now()->toDateString(),
    ])->assertSessionHasErrors('category_id');

    $this->assertDatabaseCount('transactions', 0);
});

it('records manual infaq as income only after an administrator verifies it', function () {
    $donor = User::factory()->create(['role' => 'jamaah']);
    $admin = User::factory()->create(['role' => 'admin']);
    Category::factory()->create(['name' => 'Donasi Infaq', 'type' => 'income']);

    $response = $this->actingAs($donor)->post(route('donations.store'), [
        'donor_name' => $donor->name,
        'is_anonymous' => '0',
        'amount' => 125000,
        'purpose' => 'infaq_umum',
        'message' => 'Semoga bermanfaat',
    ]);

    $donation = Donation::query()->firstOrFail();
    $response->assertRedirect(route('donations.confirmation', $donation->payment_reference));
    expect($donation->status)->toBe('pending');
    $this->assertDatabaseCount('transactions', 0);

    $this->actingAs($admin)
        ->post(route('admin.donations.verify', $donation))
        ->assertRedirect();

    $donation->refresh();
    expect($donation->status)->toBe('berhasil');
    expect($donation->transaction_id)->not->toBeNull();
    $this->assertDatabaseHas('transactions', [
        'id' => $donation->transaction_id,
        'type' => 'income',
        'amount' => 125000,
        'description' => 'Infaq online '.$donation->payment_reference,
    ]);
    $this->assertDatabaseHas('activity_logs', [
        'user_id' => $admin->id,
        'action' => 'infaq.diverifikasi',
    ]);

    $this->get(route('donations.receipt', $donation->payment_reference))
        ->assertOk()
        ->assertHeader('content-type', 'application/pdf');

    $this->actingAs($admin)
        ->post(route('admin.donations.verify', $donation))
        ->assertForbidden();
});

it('keeps jamaah infaq history scoped to the signed-in user', function () {
    $jamaah = User::factory()->create(['role' => 'jamaah']);
    $otherJamaah = User::factory()->create(['role' => 'jamaah']);
    $ownDonation = Donation::factory()->for($jamaah)->create();
    $otherDonation = Donation::factory()->for($otherJamaah)->create();

    $this->actingAs($jamaah)
        ->get(route('donations.history'))
        ->assertOk()
        ->assertSee($ownDonation->payment_reference)
        ->assertDontSee($otherDonation->payment_reference);
});

it('supports login logout and public jamaah registration', function () {
    $user = User::factory()->create(['password' => 'secret-pass-123']);

    $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'secret-pass-123',
    ])->assertRedirect(route('dashboard'));

    $this->assertAuthenticatedAs($user);

    $this->post(route('logout'))
        ->assertRedirect(route('home'));

    $this->assertGuest();

    $this->post(route('register.store'), [
        'name' => 'Jamaah Baru',
        'email' => 'jamaah@example.test',
        'password' => 'secret-pass-123',
        'password_confirmation' => 'secret-pass-123',
    ])->assertRedirect(route('dashboard'));

    $this->assertDatabaseHas('users', [
        'email' => 'jamaah@example.test',
        'role' => 'jamaah',
    ]);
});

it('exports filtered reports as PDF and an Excel workbook', function () {
    $category = Category::factory()->create(['name' => 'Infaq Jumat', 'type' => 'income']);
    Transaction::factory()->create([
        'category_id' => $category->id,
        'date' => now()->toDateString(),
        'created_by' => User::factory()->create(['role' => 'bendahara'])->id,
    ]);
    $filters = ['period' => 'bulanan', 'month' => now()->month, 'year' => now()->year];

    $this->get(route('reports.pdf', $filters))
        ->assertOk()
        ->assertHeader('content-type', 'application/pdf');

    $this->get(route('reports.excel', $filters))
        ->assertOk()
        ->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
        ->assertHeader('content-disposition', 'attachment; filename=laporan-kas-'.now()->format('Y-m').'.xlsx');
});

it('includes report totals and prevents spreadsheet formula injection', function () {
    $category = Category::factory()->create(['name' => '=2+2', 'type' => 'income']);
    $transaction = Transaction::factory()->create([
        'category_id' => $category->id,
        'type' => 'income',
        'amount' => 50000,
        'date' => now()->toDateString(),
        'description' => '=HYPERLINK("https://example.test")',
    ])->load('category');
    $export = new ReportExport(
        now()->toDateString(),
        now()->toDateString(),
        10000,
        50000,
        0,
        60000,
    );

    expect($export->headings())->toContain(['Saldo awal', 10000])
        ->toContain(['Saldo akhir', 60000]);
    expect($export->map($transaction))->toBe([
        now()->toDateString(),
        'Pemasukan',
        "'=2+2",
        '\'=HYPERLINK("https://example.test")',
        50000,
    ]);
});

it('restricts mosque settings to administrators and records settings changes', function () {
    $cashier = User::factory()->create(['role' => 'bendahara']);
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($cashier)->get(route('admin.settings.index'))->assertForbidden();

    $this->actingAs($admin)->put(route('admin.settings.mosque'), [
        'name' => 'Masjid Al-Hidayah',
        'address' => 'Jl. Kebaikan No. 8',
        'bank_name' => 'Bank Amanah',
        'bank_account' => '1234567890',
        'account_holder' => 'Masjid Al-Hidayah',
    ])->assertRedirect();

    $this->assertDatabaseHas('mosque_settings', [
        'name' => 'Masjid Al-Hidayah',
        'bank_account' => '1234567890',
    ]);
    $this->assertDatabaseHas('activity_logs', [
        'user_id' => $admin->id,
        'action' => 'pengaturan.masjid.diubah',
    ]);
});
