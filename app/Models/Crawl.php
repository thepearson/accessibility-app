<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Exceptions\InvalidPropertiesException;

class Crawl extends Model
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

    public static function create() 
    {
        $crawl = new self;
        $crawl->token = self::createToken();
        $crawl->status = 'queued';
        $crawl->total = null;
        $crawl->complete = null;
        $crawl->messages = "{}";
        return $crawl;

    }
}
