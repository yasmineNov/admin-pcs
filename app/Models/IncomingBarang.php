<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IncomingBarang extends Model
{
    protected $fillable = ['tgl_masuk', 'barang_id', 'qty', 'harga', 'supplier_id'];

    protected $casts = [
    'tgl_masuk' => 'datetime',
    ];

    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}
