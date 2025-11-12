<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sale extends Model
{
    protected $fillable = [
        'sale_number',
        'cashier_id',
        'subtotal',
        'discount',
        'vat',
        'total',
        'status',
        'mpesa_transaction_id',
        'mpesa_receipt_number',
        'customer_phone',
        'mpesa_checkout_request_id',
        'payment_confirmed_at',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'vat' => 'decimal:2',
        'total' => 'decimal:2',
        'payment_confirmed_at' => 'datetime',
    ];

    protected $appends = [
        'status_display',
        'status_color'
    ];

    /**
     * Get the cashier that owns the sale
     */
    public function cashier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }

    /**
     * Get the sale items for the sale
     */
    public function saleItems(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    /**
     * Get the products for the sale through sale items
     */
    public function products()
    {
        return $this->belongsToMany(Product::class, 'sale_items')
                    ->withPivot('quantity', 'unit_price', 'total_price')
                    ->withTimestamps();
    }

        /**
     * Generate sale number
     */
    public static function generateSaleNumber(): string
    {
        $today = now()->format('Y-m-d');
        $count = static::whereDate('created_at', $today)->count() + 1;
        return 'SALE-' . now()->format('Y-m-d') . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Check if sale is completed (paid)
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Check if sale is awaiting payment
     */
    public function isAwaitingPayment(): bool
    {
        return $this->status === 'awaiting_payment';
    }

    /**
     * Check if sale is a draft (no items or empty)
     */
    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    /**
     * Get status display name
     */
    public function getStatusDisplayAttribute(): string
    {
        switch($this->status) {
            case 'draft':
                return 'Adding Items';
            case 'awaiting_payment':
                return 'Awaiting Payment';
            case 'completed':
                return 'Completed';
            case 'cancelled':
                return 'Cancelled';
            default:
                return ucfirst($this->status);
        }
    }

    /**
     * Get status color for UI
     */
    public function getStatusColorAttribute(): string
    {
        switch($this->status) {
            case 'draft':
                return '#f59e0b'; // amber
            case 'awaiting_payment':
                return '#3b82f6'; // blue
            case 'completed':
                return '#10b981'; // green
            case 'cancelled':
                return '#ef4444'; // red
            default:
                return '#6b7280'; // gray
        }
    }

    /**
     * Get formatted total
     */
    public function getFormattedTotalAttribute(): string
    {
        return 'Ksh ' . number_format($this->total, 2);
    }

    /**
     * Scope for active sales (non-completed)
     */
    public function scopeActive($query)
    {
        return $query->whereIn('status', ['draft', 'awaiting_payment']);
    }

    /**
     * Mark sale as awaiting payment (when items added and total > 0)
     */
    public function markAsAwaitingPayment()
    {
        if ($this->total > 0 && $this->status === 'draft') {
            $this->update(['status' => 'awaiting_payment']);
        }
    }

    /**
     * Mark sale as completed (when payment confirmed)
     */
    public function markAsCompleted()
    {
        $this->update(['status' => 'completed']);
    }
}
