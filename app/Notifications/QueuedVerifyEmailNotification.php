<?php

namespace App\Notifications;

use App\Notifications\Concerns\BuildsFirmMailMessage;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class QueuedVerifyEmailNotification extends VerifyEmail implements ShouldQueue
{
    use BuildsFirmMailMessage, Queueable;

    public int $tries = 3;

    public int $timeout = 60;

    public function __construct()
    {
        $this->afterCommit();
    }

    public function backoff(): array
    {
        return [60, 300, 900];
    }

    public function toMail($notifiable): MailMessage
    {
        return $this->firmMailMessage()
            ->subject('Verifikasi alamat email Anda')
            ->greeting('Halo '.$notifiable->nama.',')
            ->line('Verifikasi alamat email untuk mengaktifkan akses ke layanan Klien.')
            ->action('Verifikasi Email', $this->verificationUrl($notifiable))
            ->line('Tautan ini memiliki batas waktu. Abaikan email ini jika Anda tidak membuat akun.')
            ->salutation('Salam, '.config('firm.name'));
    }
}
