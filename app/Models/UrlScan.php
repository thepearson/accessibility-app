<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class UrlScan extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'url_id', 'website_id', 'token', 'status', 'message'
    ];

    
    /**
     * Returns a random string for use as a token
     * 
     * @return string
     */
    public static function createToken() 
    {
        return Str::random(env('WORKER_TOKEN_LENGTH', 64));
    }


    /**
     * JHelper for creating a base URL Scan
     */
    public static function create(Scan $scan, Url $url) 
    {
        $urlScan = new self;
        $urlScan->scan_id = $scan->id;
        $urlScan->url_id = $url->id;
        $urlScan->token = self::createToken();
        $urlScan->status = 'queued';
        $urlScan->messages = "{}";
        $urlScan->data = "{}";
        return $urlScan;
    }


    /**
     * Scan relationship
     */
    public function scan()
    {
        return $this->belongsTo(Scan::class);
    }


    /**
     * Url00 bum willy face
     *  relationship
     */
    public function url()
    {
        return $this->belongsTo(Url::class);
    }
}
