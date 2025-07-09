<?php

namespace App\Listeners;

use App\Events\UserRegistered;
use Illuminate\Support\Facades\Mail;

class SendVerificationEmail
{
    public function handle(UserRegistered $event)
    {
        // Send email verification (e.g., using Laravel Mail)
        Mail::to($event->user->email)->send(new \App\Mail\VerifyEmail($event->user));
    }
}