<?php

namespace App\Exports;

use App\Models\Transaction;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use UnexpectedValueException;

class ReportExport implements FromQuery, WithHeadings, WithMapping
{
    public function __construct(
        private string $startDate,
        private string $endDate,
        private int $openingBalance,
        private int $income,
        private int $expense,
        private int $closingBalance,
    ) {}

    /**
     * @return Builder<Transaction>
     */
    public function query(): Builder
    {
        return Transaction::query()
            ->with('category:id,name')
            ->whereBetween('date', [$this->startDate, $this->endDate])
            ->orderBy('date')
            ->orderBy('id');
    }

    /**
     * @return array<int, array<int, int|string>>
     */
    public function headings(): array
    {
        return [
            ['Laporan Kas Masjid'],
            ['Saldo awal', $this->openingBalance],
            ['Pemasukan', $this->income],
            ['Pengeluaran', $this->expense],
            ['Saldo akhir', $this->closingBalance],
            [],
            ['Tanggal', 'Jenis', 'Kategori', 'Keterangan', 'Jumlah (Rp)'],
        ];
    }

    /**
     * @return array<int, int|string>
     */
    public function map(mixed $row): array
    {
        if (! $row instanceof Transaction) {
            throw new UnexpectedValueException('Data ekspor laporan harus berupa transaksi kas.');
        }

        return [
            $row->date->format('Y-m-d'),
            $row->type === 'income' ? 'Pemasukan' : 'Pengeluaran',
            $this->spreadsheetText($row->category->name),
            $this->spreadsheetText($row->description ?? ''),
            $row->amount,
        ];
    }

    private function spreadsheetText(string $value): string
    {
        return preg_match('/^[\s]*[=+\-@]/u', $value) === 1 ? "'".$value : $value;
    }
}
