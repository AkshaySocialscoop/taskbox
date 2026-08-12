<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToCompany;

class Note extends Model
{
    //
    use BelongsToCompany;
    protected $fillable = ['title', 'content', 'color'];
}
