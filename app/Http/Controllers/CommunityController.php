<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ForumPost;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CommunityController extends Controller
{
    public function forum()
    {
        return view('community.forum');
    }

    public function create()
    {
        return view('community.create_posts');
    }

    public function store(Request $request)
    {
        // This method may be redundant with the API; consider redirecting to API or keeping for non-API form submissions
        return redirect()->route('community.forum');
    }

    public function show($uuid)
    {
        return view('community.show_post', ['uuid' => $uuid]);
    }
}