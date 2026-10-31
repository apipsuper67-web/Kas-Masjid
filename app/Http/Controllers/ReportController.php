<?php

namespace App\Http\Controllers;

use App\Exports\ReportExport;
use App\Models\Transaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\Response;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $report = $this->reportContext($request);
        $transactions = $report['transactionsQuery']
            ->latest('date')
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        return view('reports.index', [
            ...$report,
            'transactions' => $transactions,
        ]);
    }

    public function pdf(Request $request): Response
    {
        $report = $this->reportContext($request);
        $report['transactions'] = $report['transactionsQuery']
            ->latest('date')
            ->latest('id')
            ->get();
        unset($report['transactionsQuery'], $report['startDate'], $report['endDate']);

        return Pdf::loadView('reports.pdf', $report)
            ->setPaper('a4', 'portrait')
            ->download($this->exportFilename($report['filters'], 'pdf'));
    }

    public function excel(Request $request): Response
    {
        $report = $this->reportContext($request);
        unset($report['transactionsQuery']);

        return Excel::download(new ReportExport(
            $report['startDate'],
            $report['endDate'],
            $report['openingBalance'],
            $report['income'],
            $report['expense'],
            $report['closingBalance'],
        ), $this->exportFilename($report['filters'], 'xlsx'));
    }

    /**
     * @return array{
     *     filters: array{period: string, month: int, year: int},
     *     openingBalance: int,
     *     income: int,
     *     expense: int,
     *     closingBalance: int,
     *     startDate: string,
     *     endDate: string,
     *     transactionsQuery: Builder<Transaction>
     * }
     */
    private function reportContext(Request $request): array
    {
        $validated = $request->validate([
            'period' => ['nullable', 'in:bulanan,tahunan'],
            'month' => ['nullable', 'integer', 'between:1,12'],
            'year' => ['nullable', 'integer', 'between:2020,'.(now()->year + 1)],
        ]);
        $period = $validated['period'] ?? 'bulanan';
        $year = (int) ($validated['year'] ?? now()->year);
        $month = (int) ($validated['month'] ?? now()->month);
        $start = $period === 'tahunan'
            ? Carbon::create($year, 1, 1)->startOfDay()
            : Carbon::create($year, $month, 1)->startOfDay();
        $end = $period === 'tahunan'
            ? $start->copy()->endOfYear()
            : $start->copy()->endOfMonth();
        $openingIncome = Transaction::query()
            ->where('type', 'income')
            ->whereDate('date', '<', $start->toDateString())
            ->sum('amount');
        $openingExpense = Transaction::query()
            ->where('type', 'expense')
            ->whereDate('date', '<', $start->toDateString())
            ->sum('amount');
        $income = Transaction::query()
            ->where('type', 'income')
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->sum('amount');
        $expense = Transaction::query()
            ->where('type', 'expense')
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->sum('amount');

        return [
            'filters' => ['period' => $period, 'month' => $month, 'year' => $year],
            'openingBalance' => $openingIncome - $openingExpense,
            'income' => $income,
            'expense' => $expense,
            'closingBalance' => $openingIncome - $openingExpense + $income - $expense,
            'startDate' => $start->toDateString(),
            'endDate' => $end->toDateString(),
            'transactionsQuery' => Transaction::query()
                ->with('category:id,name')
                ->whereBetween('date', [$start->toDateString(), $end->toDateString()]),
        ];
    }

    /**
     * @param  array{period: string, month: int, year: int}  $filters
     */
    private function exportFilename(array $filters, string $extension): string
    {
        $period = $filters['period'] === 'tahunan'
            ? (string) $filters['year']
            : sprintf('%04d-%02d', $filters['year'], $filters['month']);

        return 'laporan-kas-'.$period.'.'.$extension;
    }
}
