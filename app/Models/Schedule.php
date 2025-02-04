<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'address', 'hour'];

    public static function scopeAvailable($hour): bool
    {
        return self::where('hour', $hour)->doesntExist();
    }
}
