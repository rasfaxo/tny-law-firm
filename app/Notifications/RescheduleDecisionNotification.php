<?php

namespace App\Notifications;

use App\Models\PermintaanReschedule;
use App\Notifications\Concerns\BuildsFirmMailMessage;
use Illuminate\Notifications\Messages\MailMessage;

class RescheduleDecisionNotification extends QueuedNotification
{
    use BuildsFirmMailMessage;

    public function __construct(public PermintaanReschedule $permintaan)
    {
        parent::__construct();
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $disetujui = $this->permintaan->status_reschedule === 'disetujui';

        return $this->firmMailMessage()
            ->subject($disetujui ? 'Permintaan reschedule disetujui' : 'Permintaan reschedule ditolak')
            ->greeting('Halo '.$notifiable->nama.',')
            ->line($disetujui
                ? 'Permintaan perubahan jadwal konsultasi Anda telah disetujui.'
                : 'Permintaan perubahan jadwal konsultasi Anda belum dapat disetujui.')
            ->line('Masuk ke aplikasi untuk melihat keputusan dan tindak lanjut secara aman.')
            ->action('Lihat Permintaan', route('klien.permintaan-reschedule.show', $this->permintaan))
            ->salutation('Salam, '.config('firm.name'));
    }
}
