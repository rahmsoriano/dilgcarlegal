<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AmicusSection extends Model
{
    protected $fillable = [
        'section_title',
        'category',
        'section_content',
    ];
}
