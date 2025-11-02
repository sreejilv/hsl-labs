<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Get doctor statistics
        $doctorStats = [
            'total_doctors' => User::role('surgeon')->count(),
            'active_doctors' => User::role('surgeon')->where('is_active', true)->count(),
            'inactive_doctors' => User::role('surgeon')->where('is_active', false)->count(),
        ];

        // Get total users
        $totalUsers = User::count();

        return view('admin.dashboard', compact(
            'doctorStats', 
            'totalUsers'
        ));
    }
}
