<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UrlScanResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'url_scan_id', 'rule_id', 'result', 'impact', 'html', 'message'
    ];
}
