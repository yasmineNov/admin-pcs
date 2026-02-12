<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Orders extends Model
{
    protected $fillable = [
        'no',
        'type',
        'tgl',
        'pajak',
        'dpp',
        'total',
        'keterangan',
        'status',
        'customer_id',
        'supplier_id',
    ];

    public function details()
    {
        return $this->hasMany(OrderDetail::class);
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
