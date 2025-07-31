<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;

class SkillQuizController extends Controller
{
    public function index()
    {
        return view('student.skill-quizzes');
    }
}