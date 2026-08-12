<?php

namespace App\Models\Traits;

use App\Models\Traits\TenantScope;

trait BelongsToCompany
{
    public static function bootBelongsToCompany()
    {
        static::addGlobalScope(new TenantScope);

        static::creating(function ($model) {
            $company = app()->bound('current_company') ? app('current_company') : null;
            if ($company && empty($model->company_id)) {
                $model->company_id = $company->id;
            }
        });
    }

    public function company()
    {
        return $this->belongsTo(\App\Models\Company::class);
    }
}
