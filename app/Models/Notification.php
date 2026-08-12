<?php

namespace App\Models;
use App\Models\Notification;
use App\Models\UserInfo;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToCompany;

class Notification extends Model
{
    use BelongsToCompany;
   protected $fillable = [
        'user_id', 
        'created_by',
        'task_name',
        'brand_name',
        'read_at'
    ];

    public function userinfo()
    {
        return $this->belongsTo(UserInfo::class, 'user_id');
    } 
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    } 
}
