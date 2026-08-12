<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $fillable = [
        'company_id',
        'user_id',
        'affected_user_id',
        'module',
        'action',
        'event',
        'record_id',
        'field_name',
        'description',
        'old_value',
        'new_value',
        'url',
        'method',
        'ip_address',
        'user_agent'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
}
