<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Kas Masjid</title>
    <style>
        body { color: #263126; font-family: DejaVu Sans, sans-serif; font-size: 11px; }
        h1 { font-size: 22px; margin-bottom: 4px; }
        .muted { color: #667064; }
        .summary { border-collapse: collapse; margin: 20px 0; width: 100%; }
        .summary td { background: #f3f5ed; border: 1px solid #e1e6d9; padding: 10px; width: 25%; }
        table.transactions { border-collapse: collapse; width: 100%; }
        .transactions th, .transactions td { border-bottom: 1px solid #e4e8e0; padding: 8px 6px; text-align: left; }
        .transactions th { background: #f3f5ed; }
        .amount { text-align: right !important; white-space: nowrap; }
    </style>
</head>
<body>
    <h1>Laporan Keuangan Masjid</h1>
    <p class="muted">{{ $filters['period'] === 'tahunan' ? 'Tahun '.$filters['year'] : \Illuminate\Support\Carbon::create($filters['year'], $filters['month'])->translatedFormat('F Y') }}</p>
    <table class="summary">
        <tr>
            <td>Saldo awal<br><strong>Rp{{ number_format($openingBalance, 0, ',', '.') }}</strong></td>
            <td>Pemasukan<br><strong>Rp{{ number_format($income, 0, ',', '.') }}</strong></td>
            <td>Pengeluaran<br><strong>Rp{{ number_format($expense, 0, ',', '.') }}</strong></td>
            <td>Saldo akhir<br><strong>Rp{{ number_format($closingBalance, 0, ',', '.') }}</strong></td>
        </tr>
    </table>
    <table class="transactions">
        <thead>
            <tr><th>Tanggal</th><th>Jenis</th><th>Kategori</th><th>Keterangan</th><th class="amount">Jumlah</th></tr>
        </thead>
        <tbody>
            @forelse ($transactions as $transaction)
                <tr>
                    <td>{{ $transaction->date->format('d/m/Y') }}</td>
                    <td>{{ $transaction->type === 'income' ? 'Pemasukan' : 'Pengeluaran' }}</td>
                    <td>{{ $transaction->category->name }}</td>
                    <td>{{ $transaction->description }}</td>
                    <td class="amount">Rp{{ number_format($transaction->amount, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr><td colspan="5">Tidak ada transaksi pada periode ini.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
