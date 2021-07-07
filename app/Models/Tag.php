<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use HasFactory;

    public function rules() {
        /* return $this->belongsToMany(
            RelatedModel, 
            pivot_table_name, 
            foreign_key_of_current_model_in_pivot_table, 
            foreign_key_of_other_model_in_pivot_table
        ); */
        return $this->belongsToMany(
            Rule::class,
            'rules_tags',
            'tag_id',
            'rule_id');
    }
}
