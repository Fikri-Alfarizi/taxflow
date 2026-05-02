<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LaporanPajak extends Model
{
    protected $fillable = [
        'pajak_id',
        'generated_by',
        'periode_laporan',
        'jenis_laporan',
        'file_laporan',
        'konten_laporan',
        'tanggal_generate',
    ];

    protected $casts = [
        'periode_laporan' => 'date',
        'tanggal_generate' => 'datetime',
    ];

    // Relationships
    public function pajak()
    {
        return $this->belongsTo(Pajak::class);
    }

    public function generator()
    {
        return $this->belongsTo(User::class, 'generated_by');
    }

    // Jenis laporan options sesuai flowchart
    const JENIS_PDF = 'PDF';
    const JENIS_EXCEL = 'Excel';

    public static function getJenisOptions()
    {
        return [
            self::JENIS_PDF => 'PDF',
            self::JENIS_EXCEL => 'Excel',
        ];
    }

    // Helper method untuk generate file path
    public function getFilePath()
    {
        return storage_path('app/public/laporan-pajak/' . $this->file_laporan);
    }

    // Helper method untuk generate URL
    public function getFileUrl()
    {
        return asset('storage/laporan-pajak/' . $this->file_laporan);
    }
}