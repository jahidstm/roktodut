<?php

namespace App\Notifications;

use App\Models\Donation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

/**
 * CertificateIssuedNotification
 *
 * Donation verify হলে donor-কে in-app bell notification পাঠায়।
 * তাকে certificate দেখার ও শেয়ার করার সুযোগ দেয়।
 */
class CertificateIssuedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly Donation $donation,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $donor      = $notifiable;
        $bloodGroup = $donor->blood_group?->value ?? (string) $donor->blood_group ?? '';
        $date       = $this->donation->donation_date?->format('d M, Y') ?? now()->format('d M, Y');
        $certId     = 'RKDT-' . now()->format('Y') . '-' . str_pad($this->donation->id, 5, '0', STR_PAD_LEFT);

        return [
            'type'            => 'certificate_issued',
            'donation_id'     => $this->donation->id,
            'certificate_id'  => $certId,
            'blood_group'     => $bloodGroup,
            'donation_date'   => $date,
            'title'           => '🎓 Your Certificate is Ready!',
            'message'         => "Your blood donation certificate ({$certId}) for {$bloodGroup} donation on {$date} has been issued. Download and share it!",
            'url'             => route('certificate.show', $this->donation->certificate_token),
        ];
    }
}
