<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class TenantScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        // only apply when a current_company is bound
        $company = app()->bound('current_company') ? app('current_company') : null;
        if ($company && $company->id) {
            $builder->where($model->getTable() . '.company_id', $company->id);
        }
    }
}
