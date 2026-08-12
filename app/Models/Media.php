<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToCompany;

class Media extends Model
{
    use BelongsToCompany;
   protected $fillable = [
        'user_id',
        'file_name',
        'file_path',
        'type'
    ];
    
    public function scheduledPost()
    {
        return $this->hasOne(ScheduledPost::class, 'media_id');
    }
}
