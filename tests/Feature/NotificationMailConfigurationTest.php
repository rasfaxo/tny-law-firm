<?php

namespace Tests\Feature;

use App\Notifications\ConsultationStatusNotification;
use App\Notifications\QueuedResetPasswordNotification;
use App\Notifications\QueuedVerifyEmailNotification;
use App\Notifications\RescheduleDecisionNotification;
use App\Notifications\VerificationResultNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\Messages\MailMessage;
use Tests\Concerns\CreatesTestingData;
use Tests\TestCase;

class NotificationMailConfigurationTest extends TestCase
{
    use CreatesTestingData;
    use RefreshDatabase;

    public function test_all_transactional_notifications_use_firm_reply_to(): void
    {
        config()->set('mail.reply_to.address', 'tny.partnerhukum@gmail.com');
        config()->set('mail.reply_to.name', 'TNY & PARTNERS');

        $klien = $this->createKlien();
        $pengajuan = $this->createPengajuan($klien, ['status_pengajuan' => 'berkas_lengkap']);
        $booking = $this->createBookingAktif($klien, $pengajuan);
        $reschedule = $this->createReschedulePending($booking, ['status_reschedule' => 'ditolak']);

        $messages = [
            (new QueuedVerifyEmailNotification)->toMail($klien),
            (new QueuedResetPasswordNotification('token-test'))->toMail($klien),
            (new VerificationResultNotification($pengajuan))->toMail($klien),
            (new ConsultationStatusNotification($booking, 'confirmed'))->toMail($klien),
            (new RescheduleDecisionNotification($reschedule))->toMail($klien),
        ];

        foreach ($messages as $message) {
            $this->assertInstanceOf(MailMessage::class, $message);
            $this->assertSame([
                ['tny.partnerhukum@gmail.com', 'TNY & PARTNERS'],
            ], $message->replyTo);
        }

        $this->assertStringContainsString('/brand/logo-email.png', $messages[0]->render()->toHtml());
        $this->assertStringContainsString('TNY &amp; PARTNERS', $messages[0]->render()->toHtml());
    }
}
