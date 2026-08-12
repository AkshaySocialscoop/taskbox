<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToCompany;

class ScheduledPost extends Model
{ 
    use BelongsToCompany;
    protected $guarded = [];
    public function account()
    {
        return $this->belongsTo(SocialAccount::class, 'account_id');
    }
    
    public function media()
    {
        return $this->belongsTo(Media::class, 'media_id');
    }
}
