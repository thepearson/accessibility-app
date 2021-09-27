<?php

namespace App\Models;

use App\Exceptions\InvalidPropertiesException;
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

    public static function create($type) 
    {
        if (!in_array($type, ['scan', 'crawl'])) 
            throw new InvalidPropertiesException('Invalid type');

        $job = new self;
        $job->token = self::createToken();
        $job->status = 'queued';
        $job->type = $type;

        return $job;

    }
}
