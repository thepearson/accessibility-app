<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Scan extends Model
{
    use HasFactory;

    /**
     * Team relationship
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
     * Returns true if the scan is active, ie has any pending tasks
     * @return bool
     */
    public function isActive()
    {
        foreach ($this->urlScans as $scan) {
            if (in_array($scan->status, ['queued', 'processing'])) {
                return true;
            }
        }
        return false;
    }

    /**
     * UrlScan relationship
     */
    public function urlScanAccessibilityResults()
    {
        return $this->hasMany(UrlScanAccessibilityResult::class);
    }
}
