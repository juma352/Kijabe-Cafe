<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MpesaPayment extends Model
{
    protected $fillable = [
        'transaction_code',
        'customer_name',
        'phone_number',
        'amount',
        'transaction_time',
        'status',
        'paybill_account',
        'raw_mpesa_data',
        'sale_id',
        'linked_at',
        'linked_by',
        'payment_method',
        'checkout_request_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'transaction_time' => 'datetime',
        'linked_at' => 'datetime',
    ];

    /**
     * Get the sale that this payment is linked to
     */
    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    /**
     * Get the user who linked this payment
     */
    public function linkedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'linked_by');
    }

    /**
     * Scope to get only pending payments
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope to get payments by amount
     */
    public function scopeByAmount($query, $amount)
    {
        return $query->where('amount', $amount);
    }

    /**
     * Scope to search payments by phone or name or transaction code
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('customer_name', 'like', "%{$search}%")
              ->orWhere('phone_number', 'like', "%{$search}%")
              ->orWhere('transaction_code', 'like', "%{$search}%");
        });
    }

    /**
     * Check if payment is available (not linked)
     */
    public function isAvailable(): bool
    {
        return $this->status === 'pending' && is_null($this->sale_id);
    }

    /**
     * Link payment to a sale
     */
    public function linkToSale(Sale $sale, User $user): void
    {
        $this->update([
            'sale_id' => $sale->id,
            'status' => 'linked',
            'linked_at' => now(),
            'linked_by' => $user->id,
        ]);
    }

    /**
     * Get formatted amount
     */
    public function getFormattedAmountAttribute(): string
    {
        return number_format($this->amount, 0);
    }

    /**
     * Get masked phone number
     */
    public function getMaskedPhoneAttribute(): string
    {
        if (strlen($this->phone_number) >= 7) {
            return substr($this->phone_number, 0, 4) . ' **** ' . substr($this->phone_number, -3);
        }
        return $this->phone_number;
    }
}
