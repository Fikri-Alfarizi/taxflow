<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dokumen extends Model
{
    protected $fillable = [
        'pajak_id',
        'nama_dokumen',
        'file_dokumen',
        'ukuran_file',
        'tanggal_upload',
    ];

    public function getHumanSizeAttribute()
    {
        $size = $this->ukuran_file;
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        for ($i = 0; $size > 1024; $i++) { $size /= 1024; }
        return round($size, 2) . ' ' . $units[$i];
    }

    public function pajak()
    {
        return $this->belongsTo(Pajak::class);
    }
}
