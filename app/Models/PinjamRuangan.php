<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PinjamRuangan extends Model
{
    use HasFactory;

    /**
     * Kolom yang dapat diisi (fillable) secara massal.
     *
     * @var array<string>
     */
    protected $table = 'pinjamruangan';
    protected $fillable = [
        'room_id',    // ID ruangan yang dipinjam
        'user_id',    // ID pengguna yang meminjam
        'kegiatan',   // Kegiatan yang dilakukan
        'start_time', // Waktu mulai peminjaman
        'end_time',   // Waktu selesai peminjaman
        'status',     // Status peminjaman
    ];

    /**
     * Relasi ke model Room.
     * Satu peminjaman hanya terkait dengan satu ruangan.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function ruangan()
    {
        return $this->belongsTo(Ruangan::class, 'room_id');
    }

    /**
     * Relasi ke model User.
     * Satu peminjaman hanya terkait dengan satu pengguna.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}