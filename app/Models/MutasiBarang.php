<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MutasiBarang extends Model
{
    protected $fillable = ['tgl_mutasi','barang_id','qty','tipe','keterangan'];

    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }
}

