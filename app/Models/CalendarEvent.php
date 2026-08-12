<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToCompany;

class CalendarEvent extends Model
{
    use BelongsToCompany;
    protected $fillable = [
        'user_id',
        'brand_name',
        'posting_date',
        'post_type',
        'concept',
        'content',
        'reference',
        'comment',
        'status',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
