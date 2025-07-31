<?php

namespace App\Http\Controllers\Mentor;

use App\Http\Controllers\Controller;

class AnalyticsController extends Controller
{
    public function index()
    {
        return view('mentor.analytics');
    }
}