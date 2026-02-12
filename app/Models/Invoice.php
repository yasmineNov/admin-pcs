<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'no',
        'no_so',
        'tgl',
        'dpp',
        'ppn',
        'grand_total',
        'jatuh_tempo',
        'status',
        'paid',
        'type',
        'customer_id',
        'supplier_id',
    ];

    public function payments()
    {
        return $this->hasMany(PaymentDetail::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}

