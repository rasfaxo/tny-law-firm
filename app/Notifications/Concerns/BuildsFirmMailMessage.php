<?php

namespace App\Notifications\Concerns;

use Illuminate\Notifications\Messages\MailMessage;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Part\DataPart;
use Symfony\Component\Mime\Part\File;

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

        $logo = public_path('brand/logo-email.png');

        if (is_file($logo)) {
            $message->withSymfonyMessage(static function (Email $email) use ($logo): void {
                $email->addPart(
                    (new DataPart(new File($logo), 'logo-email.png', 'image/png'))
                        ->asInline()
                        ->setContentId('tny-logo@tnypartners.com'),
                );
            });
        }

        return $message;
    }
}
