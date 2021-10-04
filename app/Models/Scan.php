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
}
