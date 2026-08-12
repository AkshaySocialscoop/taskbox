<?php

use App\Models\AuditLog;

if (!function_exists('audit_log')) {

    function audit_log(
        $module,
        $action,
        $event = null,
        $record_id = null,
        $field_name = null,
        $description = null,
        $old_value = null,
        $new_value = null,
        $affected_user_id = null
    ) {

        AuditLog::create([
            'company_id'       => auth()->check() ? auth()->user()->company_id : null,
            'user_id'          => auth()->id(),
            'affected_user_id' => $affected_user_id,
            'module'           => $module,
            'action'           => $action,
            'event'            => $event,
            'record_id'        => $record_id,
            'field_name'       => $field_name,
            'description'      => $description,
            'old_value'        => $old_value,
            'new_value'        => $new_value,
            'url'              => request()->fullUrl(),
            'method'           => request()->method(),
            'ip_address'       => request()->ip(),
            'user_agent'       => request()->userAgent(),
        ]);
    }
}