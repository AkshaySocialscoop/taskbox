<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToCompany;

class Project extends Model
{
    use BelongsToCompany;
   protected $fillable = [
        'brand_name',
        'format',
        'link',
        'requirement',
        'comments',
        'status',
        'user_id'
    ];
}
