<?php

namespace App\Notifications;

use App\Models\BookingKonsultasi;
use App\Notifications\Concerns\BuildsFirmMailMessage;
use Illuminate\Notifications\Messages\MailMessage;

class ConsultationStatusNotification extends QueuedNotification
{
    use BuildsFirmMailMessage;

    public function __construct(
        public BookingKonsultasi $booking,
        public string $event,
    ) {
        parent::__construct();
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        [$subject, $line] = match ($this->event) {
            'completed' => ['Konsultasi telah diselesaikan', 'Tahap konsultasi pada pra-pendaftaran Anda telah dinyatakan selesai.'],
            'updated' => ['Detail konsultasi diperbarui', 'Detail teknis konsultasi Anda telah diperbarui oleh Admin.'],
            default => ['Konsultasi telah dikonfirmasi', 'Detail teknis konsultasi Anda telah dikonfirmasi oleh Admin.'],
        };

        return $this->firmMailMessage()
            ->subject($subject)
            ->greeting('Halo '.$notifiable->nama.',')
            ->line($line)
            ->line('Masuk ke aplikasi untuk melihat informasi lengkap secara aman.')
            ->action('Lihat Konsultasi', route('klien.booking-konsultasi.show', $this->booking))
            ->salutation('Salam, '.config('firm.name'));
    }
}
