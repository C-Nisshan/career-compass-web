<?php

namespace App\Http\Controllers\Mentor;

use App\Http\Controllers\Controller;

class MentorDashboardController extends Controller
{
    public function index()
    {
        return view('mentor.dashboard');
    }
}