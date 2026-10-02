<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $query = AuditLog::with('user')->latest();

        // Filter by User ID
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by Action
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        // Filter by Specific Date
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        // Filter by Month
        if ($request->filled('month')) {
            $query->whereMonth('created_at', $request->month);
        }

        // Filter by Year
        if ($request->filled('year')) {
            $query->whereYear('created_at', $request->year);
        }

        $logs = $query->paginate(15)->appends($request->query());
        
        // Fetch all users for the user dropdown filter
        $users = User::orderBy('name')->get();

        return view('logs.index', compact('logs', 'users'));
    }

    public function print(Request $request)
    {
        $query = AuditLog::with('user')->latest();

        // 1. Apply your filters to the query
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }
        if ($request->filled('month')) {
            $query->whereMonth('created_at', $request->month);
        }
        if ($request->filled('year')) {
            $query->whereYear('created_at', $request->year);
        }

        $logs = $query->get();
        
        // 2. Build the clean cascading filter text
        $filterText = "All-Time Audit Logs Report";

        $userName = null;
        if ($request->filled('user_id')) {
            $user = \App\Models\User::find($request->user_id);
            $userName = $user ? $user->name : 'Selected User';
        }

        if ($request->filled('user_id') && $request->filled('month') && $request->filled('year')) {
            $monthName = date('F', mktime(0, 0, 0, $request->month, 1));
            $filterText = "Logs for {$userName} — {$monthName} {$request->year}";
        } elseif ($request->filled('user_id') && $request->filled('year')) {
            $filterText = "Logs for {$userName} — Year {$request->year}";
        } elseif ($request->filled('user_id') && $request->filled('month')) {
            $monthName = date('F', mktime(0, 0, 0, $request->month, 1));
            $filterText = "Logs for {$userName} — Month of {$monthName}";
        } elseif ($request->filled('user_id')) {
            $filterText = "Logs for User: {$userName}";
        } elseif ($request->filled('month') && $request->filled('year')) {
            $monthName = date('F', mktime(0, 0, 0, $request->month, 1));
            $filterText = "Audit Logs for {$monthName} {$request->year}";
        } elseif ($request->filled('year')) {
            $filterText = "Audit Logs for Year {$request->year}";
        } elseif ($request->filled('month')) {
            $monthName = date('F', mktime(0, 0, 0, $request->month, 1));
            $filterText = "Audit Logs for the month of {$monthName}";
        } elseif ($request->filled('action')) {
            $filterText = "Audit Logs for Action: '{$request->action}'";
        }

        return view('logs.print', compact('logs', 'filterText'));
    }

}