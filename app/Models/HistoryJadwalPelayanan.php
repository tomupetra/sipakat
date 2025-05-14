<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoryJadwalPelayanan extends Model
{
    use HasFactory;

    protected $table = 'history_jadwal_pelayanan';

    protected $fillable = [
        'jadwal_pelayanan_id',
        'date',
        'jadwal',
        'id_pemusik',
        'id_sl1',
        'id_sl2',
        'is_confirmed',
        'is_locked',
    ];

    // Relasi ke model User untuk pemusik
    public function pemusik()
    {
        return $this->belongsTo(User::class, 'id_pemusik');
    }

    // Relasi ke model User untuk Song Leader 1
    public function songLeader1()
    {
        return $this->belongsTo(User::class, 'id_sl1');
    }

    // Relasi ke model User untuk Song Leader 2
    public function songLeader2()
    {
        return $this->belongsTo(User::class, 'id_sl2');
    }
}
