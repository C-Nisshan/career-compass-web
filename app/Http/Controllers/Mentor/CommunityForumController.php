<?php

namespace App\Http\Controllers\Mentor;

use App\Http\Controllers\Controller;

class CommunityForumController extends Controller
{
    public function index()
    {
        return view('mentor.community-forum');
    }
}