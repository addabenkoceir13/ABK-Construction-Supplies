<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewLoginAlertMail extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;
    public array $loginData;

    /**
     * Create a new message instance.
     *
     * @param User $user
     * @param array $loginData [ip, user_agent, time]
     */
    public function __construct(User $user, array $loginData)
    {
        $this->user = $user;
        $this->loginData = $loginData;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('تنبيه أمان: تم تسجيل دخول جديد إلى حسابك - A.B.K Construction Supplies')
                    ->view('emails.new-login-alert');
    }
}
