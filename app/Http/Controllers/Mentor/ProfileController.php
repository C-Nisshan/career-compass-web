<?php

namespace App\Http\Controllers\Mentor;

use App\Http\Controllers\Controller;

class ProfileController extends Controller
{
    public function index()
    {
        return view('mentor.profile');
    }
}