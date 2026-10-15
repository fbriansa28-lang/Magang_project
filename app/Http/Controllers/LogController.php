<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class LogController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:view log sistem|view all log sistem', only: ['index']),
        ];
    }

    public function index(Request $request)
    {
        $canViewAll = auth()->user()->can('view all log sistem');
        $filters = $request->only(['user_id', 'action', 'search', 'date_from', 'date_to']);

        $logs = ActivityLog::getLog($canViewAll, auth()->id(), $filters);

        $users = $canViewAll
            ? User::orderBy('name')->get(['id', 'name', 'username'])
            : collect();

        return view('logactivity.index', [
            'logs' => $logs,
            'users' => $users,
            'actions' => ActivityLog::ACTIONS,
            'filters' => $filters,
            'canViewAll' => $canViewAll,
        ]);
    }
}