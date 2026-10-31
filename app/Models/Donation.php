<?php

namespace App\Models;

use Database\Factories\DonationFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'donor_name', 'is_anonymous', 'amount', 'purpose', 'message', 'payment_method', 'status', 'payment_reference', 'proof_file', 'paid_at', 'transaction_id'])]
class Donation extends Model
{
    /** @use HasFactory<DonationFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'is_anonymous' => 'boolean',
            'amount' => 'integer',
            'paid_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }
}
