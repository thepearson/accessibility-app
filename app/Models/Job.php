<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Job extends Model
{
    use HasFactory;

    /**
     * Returns a random string for use as a token
     * 
     * @return string
     */
    public static function createToken() {
        return Str::random(env('WORKER_TOKEN_LENGTH', 64));
    }
}
