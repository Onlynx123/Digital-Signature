<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;

class AuditLogController extends Controller
{
    /**
     * Audit log milik user yang sedang login saja.
     */
    public function index()
    {
        $logs = AuditLog::where('user_id', auth()->id())
            ->latest('created_at')
            ->paginate(20);

        return view('audit.index', compact('logs'));
    }

    /**
     * Audit log SEMUA user — hanya untuk admin.
     */
    public function adminIndex()
    {
        $logs = AuditLog::with('user')
            ->latest('created_at')
            ->paginate(20);

        return view('audit.index', compact('logs'));
    }
}
