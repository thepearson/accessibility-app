<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UrlScanAccessibilityResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'url_scan_id', 'url_id', 'rule_id', 'result', 'impact', 'html', 'message'
    ];

    /**
     * Url relationship
     */
    public function url()
    {
        return $this->belongsTo(Url::class);
    }

    /**
     * UrlScan relationship
     */
    public function urlScan() {
        return $this->belongsTo(UrlScan::class);
    }

    /**
     * UrlScan relationship
     */
    public function scan() {
        return $this->belongsTo(Scan::class);
    }
}
