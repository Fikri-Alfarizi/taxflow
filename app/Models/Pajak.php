<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pajak extends Model
{
    protected $fillable = [
        'nama_perusahaan',
        'status_perusahaan',
        'npwp',
        'nib',
        'no_akta',
        'sk_kemenkumham',
        'tahun_berdiri',
        'alamat_lengkap',
        'nama_pic',
        'jabatan_pic',
        'kontak_pic',
        'email_pic',
        'website',
        'kpp_pratama',
        'kategori_usaha',
        'id_transaksi_source',
        'logo_url',
        'jenis_pajak',
        'jenis_pajak_rincian',
        'periode',
        'tanggal_input',
        'tanggal_jatuh_tempo',
        'status',
        'keterangan',
        'keterangan_penolakan',
        'user_id',
        // NEW: Approval workflow fields (sesuai dokumentasi)
        'status_verifikasi',
        'status_validasi', 
        'status_approval',
        'verified_by',
        'approved_by',
        'tanggal_verifikasi',
        'tanggal_validasi',
        'tanggal_approval',
    ];

    protected $casts = [
        'tanggal_input' => 'datetime',
        'tanggal_jatuh_tempo' => 'datetime',
        'tanggal_verifikasi' => 'datetime',
        'tanggal_validasi' => 'datetime',
        'tanggal_approval' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // NEW: Approval workflow relationships (sesuai dokumentasi)
    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function monitorings()
    {
        return $this->hasMany(Monitoring::class);
    }

    public function dokumens()
    {
        return $this->hasMany(Dokumen::class);
    }

    public function catatanPerbaikans()
    {
        return $this->hasMany(CatatanPerbaikan::class);
    }

    public function laporanPajaks()
    {
        return $this->hasMany(LaporanPajak::class);
    }

    public function getSisaHariAttribute()
    {
        if ($this->status === 'selesai') return 0;
        
        $today = now()->startOfDay();
        $deadline = \Carbon\Carbon::parse($this->tanggal_jatuh_tempo)->startOfDay();
        
        return (int) $today->diffInDays($deadline, false);
    }

    public function getSisaHariTextAttribute()
    {
        $days = $this->sisa_hari;
        if ($this->status === 'selesai') return 'Selesai';
        if ($days < 0) return 'Terlambat ' . abs($days) . ' hari';
        if ($days == 0) return 'Hati-hati, Hari Ini';
        return $days . ' hari lagi';
    }
}
