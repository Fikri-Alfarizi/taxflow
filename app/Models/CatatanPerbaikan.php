<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CatatanPerbaikan extends Model
{
    protected $fillable = [
        'pajak_id',
        'created_by',
        'catatan_perbaikan',
        'status',
        'tanggal_catatan',
        'tanggal_perbaikan',
    ];

    protected $casts = [
        'tanggal_catatan' => 'datetime',
        'tanggal_perbaikan' => 'datetime',
    ];

    // Relationships
    public function pajak()
    {
        return $this->belongsTo(Pajak::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Status options sesuai flowmap
    const STATUS_BELUM_DIPERBAIKI = 'belum_diperbaiki';
    const STATUS_SEDANG_DIPERBAIKI = 'sedang_diperbaiki';
    const STATUS_SELESAI_DIPERBAIKI = 'selesai_diperbaiki';

    public static function getStatusOptions()
    {
        return [
            self::STATUS_BELUM_DIPERBAIKI => 'Belum Diperbaiki',
            self::STATUS_SEDANG_DIPERBAIKI => 'Sedang Diperbaiki',
            self::STATUS_SELESAI_DIPERBAIKI => 'Selesai Diperbaiki',
        ];
    }
}