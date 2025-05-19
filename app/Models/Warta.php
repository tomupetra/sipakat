<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Warta extends Model
{
    use HasFactory;

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
