<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalPelayanan extends Model
{
    use HasFactory;

    protected $fillable = [
        'date', 'jadwal', 'id_pemusik', 'id_sl1', 'id_sl2', 'status'
    ];

    // Relasi ke tabel users (pemusik)
    public function pemusik()
    {
        return $this->belongsTo(User::class, 'id_pemusik');
    }

    // Relasi ke tabel users (song leader 1)
    public function songLeader1()
    {
        return $this->belongsTo(User::class, 'id_sl1');
    }

    // Relasi ke tabel users (song leader 2)
    public function songLeader2()
    {
        return $this->belongsTo(User::class, 'id_sl2');
    }
}