<?php

namespace App\Notifications\Concerns;

use Illuminate\Notifications\Messages\MailMessage;

trait BuildsFirmMailMessage
{
    protected function firmMailMessage(): MailMessage
    {
        $message = new MailMessage;
        $address = config('mail.reply_to.address');

        if (is_string($address) && $address !== '') {
            $name = config('mail.reply_to.name');
            $message->replyTo($address, is_string($name) && $name !== '' ? $name : null);
        }

        return $message;
    }
}
