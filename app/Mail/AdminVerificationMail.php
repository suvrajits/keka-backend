<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\AdminUser;

class AdminVerificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $admin;

    public function __construct($verificationCode)
    {
        $this->verificationCode = $verificationCode;
    }

    public function build()
    {
        return $this->subject('Your Verification Code')
                    ->view('emails.verification')
                    ->with(['code' => $this->verificationCode]);
    }
}
