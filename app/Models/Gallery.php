<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gallery extends Model
{
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
