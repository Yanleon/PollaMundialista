<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MatchGame;
use App\Models\Prediction;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $metrics = [
            'total_users' => User::count(),
            'total_matches' => MatchGame::count(),
            'total_predictions' => Prediction::count(),
            'pending_matches' => MatchGame::whereIn('status', ['scheduled', 'live'])->count(),
            'finished_matches' => MatchGame::where('status', 'finished')->count(),
        ];

        return view('admin.dashboard', compact('metrics'));
    }
}
