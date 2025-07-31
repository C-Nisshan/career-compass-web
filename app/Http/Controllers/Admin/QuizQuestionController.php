<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class QuizQuestionController extends Controller
{
    public function index()
    {
        return view('admin.quiz-questions');
    }
}