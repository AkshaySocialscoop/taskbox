<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index()
    {
        $query = AuditLog::with('user')->latest();

        if (request('module')) {
            $query->where('module', 'like', '%' . request('module') . '%');
        }

        if (request('action')) {
            $query->where('action', 'like', '%' . request('action') . '%');
        }

        if (request('user')) {
            $query->whereHas('user', function ($q) {
                $q->where('name', 'like', '%' . request('user') . '%');
            });
        }

        if (request('date')) {
            $query->whereDate('created_at', request('date'));
        }

        $auditLogs = $query->get();

        return view('super-admin.audit-logs.index', compact('auditLogs'));
    }
}
