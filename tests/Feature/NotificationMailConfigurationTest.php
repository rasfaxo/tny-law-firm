<?php

namespace Tests\Feature;

use App\Notifications\ConsultationStatusNotification;
use App\Notifications\QueuedResetPasswordNotification;
use App\Notifications\QueuedVerifyEmailNotification;
use App\Notifications\RescheduleDecisionNotification;
use App\Notifications\VerificationResultNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\Messages\MailMessage;
use Symfony\Component\Mime\Email;
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

        $this->assertStringContainsString('cid:tny-logo@tnypartners.com', $messages[0]->render()->toHtml());
        $this->assertStringContainsString('TNY &amp; PARTNERS', $messages[0]->render()->toHtml());

        $email = new Email;
        foreach ($messages[0]->callbacks as $callback) {
            $callback($email);
        }

        $attachments = $email->getAttachments();
        $this->assertCount(1, $attachments);
        $this->assertSame('tny-logo@tnypartners.com', $attachments[0]->getContentId());
    }
}
