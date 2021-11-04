<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Url extends Model
{
    use HasFactory;

    protected $fillable = [
        'url', 'website_id'
    ];

    /**
     * Website relationship
     */
    public function website()
    {
        return $this->belongsTo(Website::class);
    }

    /**
     * UrlScan relationship
     */
    public function urlScans()
    {
        return $this->hasMany(UrlScan::class);
    }

    /**
     * LatestUrlScan relationship
     */
    public function latestUrlScan()
    {
        return $this->hasOne(UrlScan::class)->latestOfMany();
    }

    /**
     * UrlScanAccessibilityResult relationship
     */
    public function urlScanAccessibilityResults()
    {
        return $this->hasMany(UrlScanAccessibilityResult::class);
    }
}
