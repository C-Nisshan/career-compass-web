<?php

namespace App\Http\Controllers\Mentor;

use App\Http\Controllers\Controller;

class SettingsController extends Controller
{
    public function index()
    {
        return view('mentor.settings');
    }
}