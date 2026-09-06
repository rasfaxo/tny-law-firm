<?php

namespace App\Notifications;

use App\Models\PraPendaftaranPerkara;
use App\Notifications\Concerns\BuildsFirmMailMessage;
use Illuminate\Notifications\Messages\MailMessage;

class VerificationResultNotification extends QueuedNotification
{
    use BuildsFirmMailMessage;

    public function __construct(public PraPendaftaranPerkara $pengajuan)
    {
        parent::__construct();
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $lengkap = $this->pengajuan->status_pengajuan === 'berkas_lengkap';

        return $this->firmMailMessage()
            ->subject($lengkap ? 'Berkas pra-pendaftaran telah lengkap' : 'Berkas pra-pendaftaran perlu diperbaiki')
            ->greeting('Halo '.$notifiable->nama.',')
            ->line($lengkap
                ? 'Pemeriksaan berkas Anda telah selesai dan berkas dinyatakan lengkap.'
                : 'Pemeriksaan berkas Anda telah selesai dan terdapat bagian yang perlu diperbaiki.')
            ->line('Masuk ke aplikasi untuk melihat status dan tindak lanjut secara aman.')
            ->action('Lihat Pengajuan', route('klien.pra-pendaftaran.show', $this->pengajuan))
            ->salutation('Salam, '.config('firm.name'));
    }
}
