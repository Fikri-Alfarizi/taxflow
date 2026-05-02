<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Monitoring extends Model
{
    protected $fillable = [
        'pajak_id',
        'user_id',
        'status_proses',
        'catatan',
        'tanggal_update',
    ];

    protected $casts = [
        'tanggal_update' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pajak()
    {
        return $this->belongsTo(Pajak::class);
    }
}
