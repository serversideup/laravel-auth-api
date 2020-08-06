<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmailVerification extends Mailable
{
    use Queueable, SerializesModels;

    public $verifyURL;
    public $name;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct( $url, $name )
    {
        $this->verifyURL = $url;
        $this->name = $name;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.verify')
                    ->text('emails.verify_plain')
                    ->subject('Account Activation')
                    ->from('noreply@laravel-api.com', 'Laravel API');
    }
}
