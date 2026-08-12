<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToCompany;

class Attendance extends Model
{
    use BelongsToCompany;
    protected $fillable = [
    'company_id',
    'user_id',
    'shift_id',
    'date',
    'check_in',
    'check_out',
    'working_hours',
    'overtime_hours',
    'status',
    'lat',
    'lng'
];
public function user()
{
    return $this->belongsTo(User::class);
}
}
