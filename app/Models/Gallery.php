<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Gallery extends Model
{
    use HasFactory;
    protected $table = 'gallery';
    protected $fillable = ['image'];

    static public function getRecord()
    {
        return self::orderBy('id', 'desc')->get();
    }

    static public function getSingle($id)
    {
        return self::find($id);
    }
}
