<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryNote extends Model
{
    protected $fillable = [
        'no',
        'tgl',
        'keterangan',
        'alamat_kirim',
        'order_id',
    ];

    public function order()
    {
        return $this->belongsTo(Orders::class);
    }

    public function details()
    {
        return $this->hasMany(DeliveryNoteDetail::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

}
