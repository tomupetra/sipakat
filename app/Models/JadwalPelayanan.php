<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\HistoryJadwalPelayanan;

class JadwalPelayanan extends Model
{
    use HasFactory;
    protected $table = 'jadwal_pelayanan';
    protected $fillable = [
        'date',
        'jadwal',
        'id_pemusik',
        'id_sl1',
        'id_sl2',
        'status_pemusik',
        'status_sl1',
        'status_sl2',
        'is_confirmed',
        'is_locked',
        'confirmation_deadline',
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

    // Relasi ke tabel history_jadwal_pelayanan
    public function history()
    {
        return $this->hasMany(HistoryJadwalPelayanan::class);
    }
}
