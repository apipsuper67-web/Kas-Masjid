<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDonationRequest;
use App\Models\ActivityLog;
use App\Models\Category;
use App\Models\Donation;
use App\Models\MosqueSetting;
use App\Models\Transaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DonationController extends Controller
{
    public function create(): View
    {
        return view('donations.create', [
            'settings' => MosqueSetting::query()->first(),
        ]);
    }

    public function store(StoreDonationRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['user_id'] = $request->user()?->id;
        $data['donor_name'] = $data['is_anonymous']
            ? null
            : ($data['donor_name'] ?? $request->user()?->name);
        $data['payment_method'] = 'manual';
        $data['status'] = 'pending';
        $data['payment_reference'] = 'INF-'.Str::upper((string) Str::uuid());
        $data['proof_file'] = $request->file('proof_file')?->store('donation-proofs', 'local');

        $donation = Donation::create($data);

        ActivityLog::create([
            'user_id' => $request->user()?->id,
            'action' => 'infaq.diajukan',
            'description' => 'Pengajuan infaq '.$donation->payment_reference.'.',
            'subject_type' => Donation::class,
            'subject_id' => $donation->id,
        ]);

        return redirect()->route('donations.confirmation', $donation->payment_reference);
    }

    public function confirmation(string $paymentReference): View
    {
        return view('donations.confirmation', [
            'donation' => Donation::query()
                ->where('payment_reference', $paymentReference)
                ->firstOrFail(),
            'settings' => MosqueSetting::query()->first(),
        ]);
    }

    public function history(Request $request): View
    {
        $donations = Donation::query()
            ->whereBelongsTo($request->user())
            ->latest()
            ->paginate(12);

        return view('donations.history', compact('donations'));
    }

    public function proof(Donation $donation): StreamedResponse
    {
        Gate::authorize('view', $donation);

        abort_unless(
            $donation->proof_file && Storage::disk('local')->exists($donation->proof_file),
            404,
        );

        return Storage::disk('local')->download($donation->proof_file);
    }

    public function receipt(string $paymentReference): Response
    {
        $donation = Donation::query()
            ->where('payment_reference', $paymentReference)
            ->where('status', 'berhasil')
            ->firstOrFail();

        return Pdf::loadView('donations.receipt', [
            'donation' => $donation,
            'settings' => MosqueSetting::query()->first(),
        ])->download('bukti-infaq-'.$donation->payment_reference.'.pdf');
    }

    public function verify(Request $request, Donation $donation): RedirectResponse
    {
        Gate::authorize('verify', $donation);

        DB::transaction(function () use ($request, $donation): void {
            $lockedDonation = Donation::query()
                ->whereKey($donation->id)
                ->lockForUpdate()
                ->firstOrFail();

            abort_unless($lockedDonation->status === 'pending', 409, 'Infaq ini sudah diproses.');

            $category = Category::query()
                ->where('name', 'Donasi Infaq')
                ->where('type', 'income')
                ->firstOrFail();

            $transaction = Transaction::create([
                'type' => 'income',
                'category_id' => $category->id,
                'amount' => $lockedDonation->amount,
                'date' => now()->toDateString(),
                'description' => 'Infaq online '.$lockedDonation->payment_reference,
                'proof_file' => $lockedDonation->proof_file,
                'created_by' => $request->user()->id,
            ]);

            $lockedDonation->update([
                'status' => 'berhasil',
                'paid_at' => now(),
                'transaction_id' => $transaction->id,
            ]);

            ActivityLog::create([
                'user_id' => $request->user()->id,
                'action' => 'infaq.diverifikasi',
                'description' => 'Memverifikasi infaq '.$lockedDonation->payment_reference.'.',
                'subject_type' => Donation::class,
                'subject_id' => $lockedDonation->id,
            ]);
        });

        return back()->with('success', 'Infaq berhasil diverifikasi dan dicatat sebagai pemasukan.');
    }

    public function reject(Request $request, Donation $donation): RedirectResponse
    {
        Gate::authorize('verify', $donation);

        DB::transaction(function () use ($request, $donation): void {
            $lockedDonation = Donation::query()
                ->whereKey($donation->id)
                ->lockForUpdate()
                ->firstOrFail();

            abort_unless($lockedDonation->status === 'pending', 409, 'Infaq ini sudah diproses.');

            $lockedDonation->update(['status' => 'gagal']);

            ActivityLog::create([
                'user_id' => $request->user()->id,
                'action' => 'infaq.ditolak',
                'description' => 'Menolak infaq '.$lockedDonation->payment_reference.'.',
                'subject_type' => Donation::class,
                'subject_id' => $lockedDonation->id,
            ]);
        });

        return back()->with('success', 'Pengajuan infaq ditandai gagal.');
    }
}
