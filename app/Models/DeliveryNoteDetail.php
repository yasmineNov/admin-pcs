<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryNoteDetail extends Model
{
    protected $fillable = [
        'delivery_note_id',
        'order_detail_id',
        'keterangan',
    ];

    public function deliveryNote()
    {
        return $this->belongsTo(DeliveryNote::class);
    }

    public function orderDetail()
    {
        return $this->belongsTo(OrderDetail::class);
    }
    public function stockMovements()
    {
        return $this->hasMany(MutasiBarang::class);
    }

}
