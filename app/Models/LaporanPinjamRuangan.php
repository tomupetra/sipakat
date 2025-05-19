<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LaporanPinjamRuangan extends Model
{
    protected $table = 'laporan_pinjam_ruangan';

    protected $fillable = [
        'tanggal',
        'nama_peminjam',
        'ruangan',
        'waktu_mulai',
        'waktu_selesai',
        'keterangan',
        'status',
    ];

    protected $dates = [
        'tanggal',
        'waktu_mulai',
        'waktu_selesai',
    ];
}
