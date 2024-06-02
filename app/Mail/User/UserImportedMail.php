<?php

namespace App\Mail\User;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class UserImportedMail extends Mailable
{
    use SerializesModels;

    private $user;
    private $password;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $password = null)
    {
        $this->user = $user;
        $this->password = $password;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return
        $this
        ->markdown('emails.users.imported')
        ->subject('Welcome To Qreeb')
        ->with([
            'user'     => $this->user,
            'password' => $this->password
        ]);
    }
}
