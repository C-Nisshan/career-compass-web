<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;

class SuccessStoryController extends Controller
{
    public function index()
    {
        return view('student.success-stories');
    }
}