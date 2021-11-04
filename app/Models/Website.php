<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Website extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'url'
    ];


    /**
     * Team relationship
     */
    public function team()
    {
        return $this->belongsTo(
            Team::class,
            'team_id'
        );
    }

    
    /**
     * Url relationship
     */
    public function urls()
    {
        return $this->hasMany(Url::class);
    }
    

    /**
     * Scan relationship
     */
    public function scans()
    {
        return $this->hasMany(Scan::class);
    }

    
    /**
     * Get the user's most recent order.
     */
    public function latestScan()
    {
        return $this->hasOne(Scan::class)->latestOfMany();
    }

    public function currentScanAccessibilityResults()
    {
        return $this->latestScan->first()->with('urlScanAccessibilityResults');
    }
}
