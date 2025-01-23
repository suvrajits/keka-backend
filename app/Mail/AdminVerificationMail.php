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

    public function __construct(AdminUser $admin)
    {
        $this->admin = $admin;
    }

    public function build()
    {
        return $this->subject('Admin Verification Code')
                    ->view('emails.admin_verification')
                    ->with(['verification_code' => $this->admin->verification_code]);
    }
}
