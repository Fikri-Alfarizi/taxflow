<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pajak extends Model
{
    protected $fillable = [
        'nama_perusahaan',
        'npwp',
        'alamat_lengkap',
        'nama_pic',
        'kontak_pic',
        'email_pic',
        'kpp_pratama',
        'kategori_usaha',
        'id_transaksi_source',
        'logo_url',
        'jenis_pajak',
        'periode',
        'tanggal_input',
        'tanggal_jatuh_tempo',
        'status',
        'keterangan',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function monitorings()
    {
        return $this->hasMany(Monitoring::class);
    }

    public function dokumens()
    {
        return $this->hasMany(Dokumen::class);
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
