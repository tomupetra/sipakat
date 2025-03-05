<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Warta extends Model
{
    protected $table = 'warta';
    protected $fillable = ['date', 'file_name'];

    static public function getRecord()
    {
        return self::orderBy('date', 'desc')->get();
    }

    static public function getSingle($id)
    {
        return self::find($id);
    }
}
