<?php

namespace App\Http\Controllers\Medical;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $isSurgeon = $user->hasRole('surgeon');
        $isStaff = $user->hasRole('staff');
        
        return view('medical.dashboard', compact('user', 'isSurgeon', 'isStaff'));
    }
}
