<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class MentorApprovalController extends Controller
{
    public function index()
    {
        return view('admin.mentor-approvals');
    }
}