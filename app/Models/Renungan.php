<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Renungan extends Model
{
    protected $table = 'renungan';
    protected $fillable = ['date', 'ayat_harian', 'bacaan_pagi', 'bacaan_malam', 'lagu_ende', 'title', 'content', 'image'];

    static public function getRecord()
    {
        return Renungan::orderBy('date', 'desc')->get();
    }

    static public function getSingle($id)
    {
        return self::find($id);
    }
}
