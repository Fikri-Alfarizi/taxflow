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
        // NEW: Validation fields (sesuai UC-008)
        'status_validasi',
        'validated_by',
        'tanggal_validasi',
        'keterangan_validasi',
        'tipe_file',
    ];

    protected $casts = [
        'tanggal_upload' => 'datetime',
        'tanggal_validasi' => 'datetime',
    ];

    protected $appends = ['file_url', 'is_remote'];
    
    public function getHumanSizeAttribute()
    {
        $size = (float) $this->ukuran_file;
        if ($size <= 0) return '0 B';
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        for ($i = 0; $size >= 1024 && $i < count($units) - 1; $i++) { $size /= 1024; }
        return round($size, 2) . ' ' . $units[$i];
    }

    public function getFileUrlAttribute()
    {
        if (filter_var($this->file_dokumen, FILTER_VALIDATE_URL)) {
            return $this->file_dokumen;
        }
        return \Illuminate\Support\Facades\Storage::url($this->file_dokumen);
    }

    public function getIsRemoteAttribute()
    {
        return filter_var($this->file_dokumen, FILTER_VALIDATE_URL);
    }

    public function pajak()
    {
        return $this->belongsTo(Pajak::class);
    }

    // NEW: Validation relationship (sesuai UC-008)
    public function validatedBy()
    {
        return $this->belongsTo(User::class, 'validated_by');
    }
}
