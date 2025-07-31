<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class ForumModerationController extends Controller
{
    public function index()
    {
        return view('admin.forum-moderation');
    }
}