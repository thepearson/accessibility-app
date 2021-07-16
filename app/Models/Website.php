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
     * Team relationship
     */
    public function urls()
    {
        return $this->hasMany(Url::class);
    }
}
