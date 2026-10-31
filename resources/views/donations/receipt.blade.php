<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Bukti Infaq {{ $donation->payment_reference }}</title>
    <style>
        body { color: #273328; font-family: DejaVu Sans, sans-serif; font-size: 13px; padding: 28px; }
        .receipt { border: 1px solid #dfe5d6; border-radius: 12px; margin: 0 auto; max-width: 640px; padding: 30px; }
        h1 { color: #405533; font-size: 24px; margin: 0; }
        .muted { color: #6b7567; }
        .line { border-bottom: 1px solid #e6e9e1; display: flex; justify-content: space-between; padding: 12px 0; }
        .amount { color: #405533; font-size: 20px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="receipt">
        <h1>Bukti Infaq Masjid</h1>
        <p class="muted">{{ $settings?->name ?? 'Kas Masjid' }}</p>
        <p class="muted">Terima kasih atas kepercayaan dan kebaikan Anda.</p>
        <div class="line"><span>Nomor referensi</span><strong>{{ $donation->payment_reference }}</strong></div>
        <div class="line"><span>Tanggal diterima</span><strong>{{ $donation->paid_at?->format('d/m/Y H:i') }}</strong></div>
        <div class="line"><span>Donatur</span><strong>{{ $donation->is_anonymous ? 'Hamba Allah' : ($donation->donor_name ?? $donation->user?->name ?? 'Donatur') }}</strong></div>
        <div class="line"><span>Tujuan</span><strong>{{ str_replace('_', ' ', ucfirst($donation->purpose)) }}</strong></div>
        <div class="line"><span>Nominal</span><strong class="amount">Rp{{ number_format($donation->amount, 0, ',', '.') }}</strong></div>
        <p class="muted">Bukti ini diterbitkan secara digital oleh sistem kas masjid.</p>
    </div>
</body>
</html>
