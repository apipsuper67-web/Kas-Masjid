<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use App\Models\Transaction;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();

        if ($user->role === 'jamaah') {
            $donations = Donation::query()
                ->whereBelongsTo($user)
                ->latest()
                ->paginate(8);

            return view('dashboard.donatur', [
                'donations' => $donations,
                'totalContribution' => Donation::query()
                    ->whereBelongsTo($user)
                    ->where('status', 'berhasil')
                    ->sum('amount'),
            ]);
        }

        $monthIncome = Transaction::query()
            ->where('type', 'income')
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->sum('amount');
        $monthExpense = Transaction::query()
            ->where('type', 'expense')
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->sum('amount');
        $chartLabels = [];
        $chartIncome = [];
        $chartExpense = [];

        for ($offset = 5; $offset >= 0; $offset--) {
            $month = now()->subMonths($offset);
            $chartLabels[] = $month->translatedFormat('M');
            $chartIncome[] = (int) Transaction::query()
                ->where('type', 'income')
                ->whereYear('date', $month->year)
                ->whereMonth('date', $month->month)
                ->sum('amount');
            $chartExpense[] = (int) Transaction::query()
                ->where('type', 'expense')
                ->whereYear('date', $month->year)
                ->whereMonth('date', $month->month)
                ->sum('amount');
        }

        return view('dashboard.admin', [
            'balance' => Transaction::query()->where('type', 'income')->sum('amount')
                - Transaction::query()->where('type', 'expense')->sum('amount'),
            'monthIncome' => $monthIncome,
            'monthExpense' => $monthExpense,
            'transactions' => Transaction::query()
                ->with('category:id,name')
                ->latest('date')
                ->latest('id')
                ->limit(6)
                ->get(),
            'pendingDonations' => Donation::query()
                ->where('status', 'pending')
                ->latest()
                ->limit(5)
                ->get(),
            'chartLabels' => $chartLabels,
            'chartIncome' => $chartIncome,
            'chartExpense' => $chartExpense,
        ]);
    }
}
