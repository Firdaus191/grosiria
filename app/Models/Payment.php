<?php
// File: app/Models/Payment.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'order_id',
        'midtrans_transaction_id',
        'snap_token',
        'payment_type',
        'gross_amount',
        'transaction_status',
        'raw_webhook_payload',
        'paid_at'
    ];

    protected $casts = [
        'raw_webhook_payload' => 'array',
        'paid_at' => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
