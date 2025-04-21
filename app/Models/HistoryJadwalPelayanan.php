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
}
