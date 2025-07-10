<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RegistrationStatus extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $status;

    public function __construct(User $user, string $status)
    {
        $this->user = $user;
        $this->status = $status;
    }

    public function build()
    {
        $subject = match ($this->status) {
            'pending' => __('Registration Submitted'),
            'approved' => __('Registration Approved'),
            'rejected' => __('Registration Rejected'),
            default => __('Registration Update')
        };

        return $this->subject($subject)
                    ->view('emails.registration-status')
                    ->with([
                        'user' => $this->user,
                        'status' => $this->status,
                    ]);
    }
}