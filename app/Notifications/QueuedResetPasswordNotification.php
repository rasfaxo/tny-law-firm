<?php

namespace App\Notifications;

use App\Notifications\Concerns\BuildsFirmMailMessage;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class QueuedResetPasswordNotification extends ResetPassword implements ShouldQueue
{
    use BuildsFirmMailMessage, Queueable;

    public int $tries = 3;

    public int $timeout = 60;

    public function __construct(#[\SensitiveParameter] string $token)
    {
        parent::__construct($token);
        $this->afterCommit();
    }

    public function backoff(): array
    {
        return [60, 300, 900];
    }

    public function toMail($notifiable): MailMessage
    {
        return $this->firmMailMessage()
            ->subject('Atur ulang kata sandi')
            ->greeting('Halo '.$notifiable->nama.',')
            ->line('Kami menerima permintaan untuk mengatur ulang kata sandi akun Anda.')
            ->action('Atur Ulang Kata Sandi', $this->resetUrl($notifiable))
            ->line('Tautan akan kedaluwarsa dalam '.config('auth.passwords.'.config('auth.defaults.passwords').'.expire').' menit.')
            ->line('Abaikan email ini jika Anda tidak membuat permintaan tersebut.')
            ->salutation('Salam, '.config('firm.name'));
    }
}
