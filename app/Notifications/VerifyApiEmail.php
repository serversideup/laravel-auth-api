<?php

namespace App\Notifications;

use App\Mail\EmailVerification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;
use Illuminate\Auth\Notifications\VerifyEmail as VerifyEmailBase;

class VerifyApiEmail extends VerifyEmailBase
{
    protected function verificationUrl($notifiable)
    {   
        $prefix = env('FRONTEND_URL').'/account/verify/';

        $temporarySignedUrl = URL::temporarySignedRoute(
            'verificationapi.verify', Carbon::now()->addMinutes(60), ['id' => $notifiable->getKey()]
        );

        return $prefix . '?verify_url=' . urlencode( $temporarySignedUrl );
    }

    public function toMail( $notifiable )
    {
        $url = self::verificationUrl( $notifiable );
        $name = $notifiable->name;
        
        return (new EmailVerification( $url, $name ) )->to( $notifiable->email );
    }
}