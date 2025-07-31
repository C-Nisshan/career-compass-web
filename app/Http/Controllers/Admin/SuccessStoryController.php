<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class SuccessStoryController extends Controller
{
    public function index()
    {
        return view('admin.success-stories');
    }
}