<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PremiHadir extends Model
{
    protected $table = 'premi_hadir';
    protected $fillable = [
        'user_id', 
        'absensi_id', 
        'total_hadir', 
        'nominal_per_hadir', 
        'total_premi', 
        'status'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function absensi(): BelongsTo
    {
        return $this->belongsTo(Absensi::class, 'absensi_id');
    }
}