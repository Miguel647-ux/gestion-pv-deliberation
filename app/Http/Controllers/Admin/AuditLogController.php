<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditLogController extends Controller
{
    public function index(Request $request): View
    {
        $logs = AuditLog::with(['user', 'deliberationPv'])
            ->when($request->filled('action'), function ($q) use ($request) {
                $q->where('action', $request->input('action'));
            })
            ->latest()
            ->paginate(50)
            ->withQueryString();

        return view('admin.logs.index', [
            'logs'    => $logs,
            'actions' => [
                AuditLog::ACTION_CREATED,
                AuditLog::ACTION_UPDATED,
                AuditLog::ACTION_ARCHIVED,
                AuditLog::ACTION_VIEWED,
                AuditLog::ACTION_DELETED,
            ],
            'currentAction' => $request->input('action'),
        ]);
    }
}