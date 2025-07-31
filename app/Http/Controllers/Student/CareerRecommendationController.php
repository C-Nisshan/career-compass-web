<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;

class CareerRecommendationController extends Controller
{
    public function index()
    {
        return view('student.career-recommendations');
    }
}