<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;

class ReportController extends Controller
{
    public function index()
    {
        return view('student.reports');
    }
}