<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AdminVerificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $code; // Explicit property declaration

    public function __construct($code)
    {
        $this->code = $code;
    }

    public function build()
    {
        return $this->subject('Your Verification Code')
                    ->view('emails.admin_verification')
                    ->with([
                        'code' => $this->code,
                        'adminName' => 'Admin', // Replace with actual name if available
                    ]);
    }
}