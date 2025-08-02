<?php

namespace App\Http\Controllers\Mentor;

use App\Http\Controllers\Controller;

class MentorPendingController extends Controller
{
    public function index()
    {
        return view('mentor.mentor-pending');
    }
}