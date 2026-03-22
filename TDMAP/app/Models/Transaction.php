<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'user_id',
        'vip_level',
        'amount',
        'transaction_code',
        'status',
        'user_confirmed_paid',
        'user_confirmed_paid_at',
    ];

    protected $casts = [
        'user_confirmed_paid' => 'boolean',
        'user_confirmed_paid_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}