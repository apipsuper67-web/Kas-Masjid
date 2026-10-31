<?php

namespace App\Http\Controllers;

use App\Models\MosqueSetting;
use App\Models\Transaction;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $monthStart = now()->startOfMonth()->toDateString();
        $monthEnd = now()->endOfMonth()->toDateString();
        $totalIncome = Transaction::query()->where('type', 'income')->sum('amount');
        $totalExpense = Transaction::query()->where('type', 'expense')->sum('amount');

        return view('home', [
            'settings' => MosqueSetting::query()->first(),
            'balance' => $totalIncome - $totalExpense,
            'monthIncome' => Transaction::query()
                ->where('type', 'income')
                ->whereBetween('date', [$monthStart, $monthEnd])
                ->sum('amount'),
            'monthExpense' => Transaction::query()
                ->where('type', 'expense')
                ->whereBetween('date', [$monthStart, $monthEnd])
                ->sum('amount'),
            'transactions' => Transaction::query()
                ->with('category:id,name')
                ->latest('date')
                ->latest('id')
                ->limit(4)
                ->get(),
        ]);
    }
}
