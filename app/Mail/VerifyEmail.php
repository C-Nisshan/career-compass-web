<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VerifyEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function build()
    {
        $verificationUrl = url('/api/auth/verify-email?token=' . $this->user->remember_token);

        return $this->subject('Verify Your Email Address')
                    ->view('emails.verify')
                    ->with(['verification_url' => $verificationUrl]);
    }
}