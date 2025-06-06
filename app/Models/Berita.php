<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Berita extends Model
{
    use HasFactory;
    protected $table = 'berita';
    protected $fillable = ['date', 'title', 'content', 'image'];

    public static function getRecord($id = null)
    {
        if ($id == null) {
            return self::orderBy('date', 'asc')->get();
        } else {
            return self::where('id', $id)->first();
        }
    }
}
