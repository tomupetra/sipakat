<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ruangan extends Model
{
    use HasFactory;

    /**
     * Kolom yang dapat diisi (fillable) secara massal.
     *
     * @var array<string>
     */
    protected $table = 'ruangan';
    protected $fillable = [
        'name',        // Nama ruangan
        'color', // Deskripsi ruangan
    ];

    /**
     * Relasi ke model Booking.
     * Satu ruangan bisa memiliki banyak peminjaman.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function bookings()
    {
        return $this->hasMany(PinjamRuangan::class, 'room_id');
    }
}