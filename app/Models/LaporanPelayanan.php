<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LaporanPelayanan extends Model
{
    protected $table = 'laporan_pelayanan';

    protected $fillable = [
        'tanggal',
        'sesi',
        'pemusik',
        'sl1',
        'sl2',
        'is_confirmed',
        'is_locked',
    ];

    protected $dates = [
        'tanggal',
    ];

    public function getTanggalFormattedAttribute()
    {
        return $this->tanggal->format('d-m-Y');
    }

    public function jadwal()
    {
        return $this->belongsTo(JadwalPelayanan::class, 'id_jadwal');
    }
    public function pemusik()
    {
        return $this->belongsTo(User::class, 'pemusik');
    }
    public function sl1()
    {
        return $this->belongsTo(User::class, 'sl1');
    }
    public function sl2()
    {
        return $this->belongsTo(User::class, 'sl2');
    }
}
