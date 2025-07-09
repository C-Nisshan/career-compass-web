<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;

class StudentDashboardController extends Controller
{
    public function index()
    {
        return view('student.dashboard');
    }
}