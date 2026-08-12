<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToCompany;

class SocialAccount extends Model
{
    use BelongsToCompany;
   protected $fillable = [
    'client_id',
    'platform',
    'ig_business_id',
    'page_id',
    'access_token'
];
}
