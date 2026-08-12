<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToCompany;

class Shift extends Model
{
    use BelongsToCompany;
    protected $fillable = ['name', 'start_time', 'end_time', 'company_id'];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
