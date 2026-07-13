<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Document;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->isAdmin()) {
            // Admin melihat statistik SEMUA dokumen di sistem
            $stats = [
                'total'     => Document::count(),
                'pending'   => Document::where('status', 'waiting_signature')->count(),
                'completed' => Document::where('status', 'completed')->count(),
                'locked'    => Document::where('status', 'locked')->count(),
            ];

            $recentActivity = AuditLog::with('user')
                ->latest('created_at')
                ->limit(10)
                ->get();
        } else {
            // User biasa hanya melihat statistik miliknya
            $stats = [
                'total'     => $user->documents()->count(),
                'pending'   => $user->pendingSignatures()->count(),
                'completed' => $user->documents()->where('status', 'completed')->count(),
                'locked'    => $user->documents()->where('status', 'locked')->count(),
            ];

            $recentActivity = AuditLog::where('user_id', $user->id)
                ->latest('created_at')
                ->limit(10)
                ->get();
        }

        // Dokumen yang menunggu tanda tangan dari user yang sedang login
        $pendingToSign = $user->documentSigners()
            ->where('status', 'pending')
            ->with('document')
            ->limit(5)
            ->get();

        return view('dashboard.index', compact('stats', 'recentActivity', 'pendingToSign'));
    }
}
