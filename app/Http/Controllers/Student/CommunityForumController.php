<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;

class CommunityForumController extends Controller
{
    public function index()
    {
        return view('student.community-forum');
    }
}