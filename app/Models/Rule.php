<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rule extends Model
{
    use HasFactory;

    public function tags() {
        /* return $this->belongsToMany(
            RelatedModel, 
            pivot_table_name, 
            foreign_key_of_current_model_in_pivot_table, 
            foreign_key_of_other_model_in_pivot_table
        ); */
        return $this->belongsToMany(
            Tag::class,
            'rules_tags',
            'rule_id',
            'tag_id');
    }

    /** 
     * Event handler to remove any tags related to this model on deleteion
     */
    public static function boot() {
        parent::boot();

        static::deleting(function($rule) {
             $rule->tags()->detach();
        });
    }
}
