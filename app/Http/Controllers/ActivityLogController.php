<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * ActivityLogController — Tampilan audit trail (read-only).
 */
class ActivityLogController extends Controller
{
    public function index(Request $request): View
    {
        $query = ActivityLog::with('user')->latest('created_at');

        // Filter: aksi (created/updated/deleted)
        if ($request->filled('action')) {
            $query->ofAction($request->action);
        }

        // Filter: rentang tanggal
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->paginate(20)->withQueryString();

        return view('activity-logs.index', compact('logs'));
    }
}
