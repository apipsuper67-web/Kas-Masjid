<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTransactionRequest;
use App\Http\Requests\UpdateTransactionRequest;
use App\Models\ActivityLog;
use App\Models\Category;
use App\Models\Transaction;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TransactionController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Transaction::class);

        $filters = $request->validate([
            'type' => ['nullable', 'in:income,expense'],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
            'q' => ['nullable', 'string', 'max:100'],
        ]);

        $transactions = Transaction::query()
            ->with(['category:id,name', 'donation:id,transaction_id'])
            ->when($filters['type'] ?? null, fn ($query, $type) => $query->where('type', $type))
            ->when($filters['category_id'] ?? null, fn ($query, $categoryId) => $query->where('category_id', $categoryId))
            ->when($filters['from'] ?? null, fn ($query, $from) => $query->whereDate('date', '>=', $from))
            ->when($filters['to'] ?? null, fn ($query, $to) => $query->whereDate('date', '<=', $to))
            ->when($filters['q'] ?? null, fn ($query, $search) => $query->where('description', 'like', '%'.$search.'%'))
            ->latest('date')
            ->latest('id')
            ->paginate(12)
            ->withQueryString();

        return view('transactions.index', [
            'transactions' => $transactions,
            'categories' => Category::query()->orderBy('type')->orderBy('name')->get(),
            'filters' => $filters,
        ]);
    }

    public function create(): View
    {
        Gate::authorize('create', Transaction::class);

        return view('transactions.form', [
            'transaction' => new Transaction,
            'categories' => Category::query()->orderBy('type')->orderBy('name')->get(),
        ]);
    }

    public function store(StoreTransactionRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['proof_file'] = $request->file('proof_file')?->store('transaction-proofs', 'local');
        $data['created_by'] = $request->user()->id;
        $transaction = Transaction::create($data);

        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'transaksi.dibuat',
            'description' => 'Membuat transaksi #'.$transaction->id.'.',
            'subject_type' => Transaction::class,
            'subject_id' => $transaction->id,
        ]);

        return redirect()->route('transactions.index')->with('success', 'Transaksi berhasil ditambahkan.');
    }

    public function edit(Transaction $transaction): View
    {
        Gate::authorize('update', $transaction);

        return view('transactions.form', [
            'transaction' => $transaction,
            'categories' => Category::query()->orderBy('type')->orderBy('name')->get(),
        ]);
    }

    public function update(UpdateTransactionRequest $request, Transaction $transaction): RedirectResponse
    {
        $data = $request->validated();
        $oldProof = $transaction->proof_file;

        if ($request->hasFile('proof_file')) {
            $data['proof_file'] = $request->file('proof_file')->store('transaction-proofs', 'local');
        } else {
            unset($data['proof_file']);
        }

        $transaction->update($data);

        if ($oldProof && $request->hasFile('proof_file')) {
            Storage::disk('local')->delete($oldProof);
        }

        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'transaksi.diubah',
            'description' => 'Mengubah transaksi #'.$transaction->id.'.',
            'subject_type' => Transaction::class,
            'subject_id' => $transaction->id,
        ]);

        return redirect()->route('transactions.index')->with('success', 'Transaksi berhasil diperbarui.');
    }

    public function destroy(Request $request, Transaction $transaction): RedirectResponse
    {
        Gate::authorize('delete', $transaction);

        $proof = $transaction->proof_file;
        $transactionId = $transaction->id;
        $transaction->delete();

        if ($proof) {
            Storage::disk('local')->delete($proof);
        }

        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'transaksi.dihapus',
            'description' => 'Menghapus transaksi #'.$transactionId.'.',
            'subject_type' => Transaction::class,
            'subject_id' => $transactionId,
        ]);

        return redirect()->route('transactions.index')->with('success', 'Transaksi berhasil dihapus.');
    }

    public function proof(Transaction $transaction): StreamedResponse
    {
        Gate::authorize('view', $transaction);

        abort_unless(
            $transaction->proof_file && Storage::disk('local')->exists($transaction->proof_file),
            404,
        );

        return Storage::disk('local')->download($transaction->proof_file);
    }
}
