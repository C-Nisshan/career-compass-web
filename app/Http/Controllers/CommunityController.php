<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ForumPost;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CommunityController extends Controller
{
    public function index()
    {
        return view('contact');
    }

    /**
     * Display the community forum page.
     *
     * @return \Illuminate\View\View
     */
    public function forum()
    {
        $posts = ForumPost::with('user')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return view('community.forum', [
            'posts' => $posts,
            'user' => Auth::user(),
        ]);
    }

    /**
     * Show the form to create a new forum post.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('community.create_post');
    }

    /**
     * Store a new forum post.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string|max:5000',
        ]);

        ForumPost::create([
            'uuid' => Str::uuid(),
            'user_id' => Auth::id(),
            'title' => $validated['title'],
            'body' => $validated['body'],
        ]);

        return redirect()->route('community.forum')->with('success', 'Post created successfully!');
    }
}