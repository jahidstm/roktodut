<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

/**
 * CertificateController
 *
 * প্রতিটি verified donation-এর জন্য একটি আলাদা PNG certificate তৈরি করে।
 * - show()     : Public share page (OG meta tags সহ)
 * - download() : Certificate PNG direct download
 */
class CertificateController extends Controller
{
    // Certificate canvas dimensions (A4 landscape ratio)
    private const W = 1200;
    private const H = 848;

    // ─────────────────────────────────────────────────────────────────────────
    // Public share page — Facebook/WhatsApp শেয়ার করা যাবে
    // ─────────────────────────────────────────────────────────────────────────
    public function show(string $token)
    {
        $donation = Donation::where('certificate_token', $token)
            ->with(['donor.district', 'bloodRequest'])
            ->firstOrFail();

        $donor        = $donation->donor;
        $bloodGroup   = $donor->blood_group?->value ?? (string) $donor->blood_group ?? 'N/A';
        $donorName    = strtoupper($donor->name ?? 'DONOR');
        $districtName = $donor->district?->name ?? $donation->district ?? 'Bangladesh';
        $donationDate = $donation->donation_date
            ? $donation->donation_date->format('d F, Y')
            : now()->format('d F, Y');
        $totalCount   = $donor->total_verified_donations ?? 1;

        // Certificate ID (e.g. RKDT-2026-00042)
        $certId = 'RKDT-' . now()->format('Y') . '-' . str_pad($donation->id, 5, '0', STR_PAD_LEFT);

        $shareTitle = "{$donorName} donated blood ({$bloodGroup}) on {$donationDate}";
        $shareDesc  = "This person has donated blood {$totalCount} time(s) through Roktodut Blood Donation Platform.";
        $imageUrl   = route('certificate.download', $token);
        $shareUrl   = route('certificate.show', $token);

        return view('certificate.show', compact(
            'donation', 'donor', 'bloodGroup', 'donorName',
            'districtName', 'donationDate', 'totalCount',
            'certId', 'token', 'shareTitle', 'shareDesc',
            'imageUrl', 'shareUrl'
        ));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Download PNG — cached server-side
    // ─────────────────────────────────────────────────────────────────────────
    public function download(string $token)
    {
        $donation = Donation::where('certificate_token', $token)
            ->with(['donor.district', 'bloodRequest'])
            ->firstOrFail();

        $directory = storage_path('app/public/certificates');
        $imagePath = "{$directory}/cert-{$donation->id}.png";

        // Serve from cache if already generated
        if (!File::exists($imagePath)) {
            if (!File::isDirectory($directory)) {
                File::makeDirectory($directory, 0755, true, true);
            }
            $this->generateCertificate($donation, $imagePath);
        }

        $donor    = $donation->donor;
        $filename = 'roktodut-certificate-' . ($donor->name ? strtolower(str_replace(' ', '-', $donor->name)) : $donation->id) . '.png';

        return response()->download($imagePath, $filename, [
            'Content-Type'  => 'image/png',
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Core: Intervention/Image দিয়ে certificate generate করা
    // ─────────────────────────────────────────────────────────────────────────
    private function generateCertificate(Donation $donation, string $savePath): void
    {
        $donor      = $donation->donor;
        $bloodGroup = $donor->blood_group?->value ?? (string) $donor->blood_group ?? 'N/A';
        $donorName  = strtoupper($donor->name ?? 'DONOR');
        $district   = $donor->district?->name ?? $donation->district ?? 'Bangladesh';
        $date       = $donation->donation_date?->format('d F, Y') ?? now()->format('d F, Y');
        $total      = $donor->total_verified_donations ?? 1;
        $certId     = 'RKDT-' . now()->format('Y') . '-' . str_pad($donation->id, 5, '0', STR_PAD_LEFT);
        $hospital   = $donation->hospital ?? 'N/A';

        $fontBold   = public_path('fonts/Inter-Black.ttf');
        $fontReg    = public_path('fonts/Inter-Regular.ttf');
        // Fallback: if fonts don't exist, GD will use built-in bitmap font

        $manager = new ImageManager(new Driver());

        // ── Step 1: Base canvas (dark red gradient via layered rectangles) ──
        $img = $manager->create(self::W, self::H);
        $img->fill('#7f1d1d'); // dark red base

        // Gradient simulation: draw progressively lighter horizontal strips
        for ($i = 0; $i < self::H; $i++) {
            $factor = $i / self::H;
            $r = (int) (127 + ($factor * 80));  // 127→207 (dark red → medium red)
            $g = (int) (29  + ($factor * 10));
            $b = (int) (29  + ($factor * 5));
            $hex = sprintf('#%02x%02x%02x', min(220, $r), min(39, $g), min(34, $b));
            $img->drawLine(function ($line) use ($i, $hex) {
                $line->from(0, $i)->to(self::W, $i)->color($hex);
            });
        }

        // ── Step 2: Decorative elements ──────────────────────────────────────
        // Top gold accent bar
        $img->drawRectangle(0, 0, function ($rect) {
            $rect->size(self::W, 8);
            $rect->background('#fbbf24');
        });
        // Bottom gold accent bar
        $img->drawRectangle(0, self::H - 8, function ($rect) {
            $rect->size(self::W, 8);
            $rect->background('#fbbf24');
        });
        // Left vertical gold accent
        $img->drawRectangle(0, 0, function ($rect) {
            $rect->size(8, self::H);
            $rect->background('#fbbf24');
        });
        // Right vertical gold accent
        $img->drawRectangle(self::W - 8, 0, function ($rect) {
            $rect->size(8, self::H);
            $rect->background('#fbbf24');
        });

        // Inner border (white, semi-transparent-ish via a lighter shade)
        $img->drawLine(function ($line) { $line->from(20, 20)->to(self::W - 20, 20)->color('#ffffff'); });
        $img->drawLine(function ($line) { $line->from(20, self::H - 20)->to(self::W - 20, self::H - 20)->color('#ffffff'); });
        $img->drawLine(function ($line) { $line->from(20, 20)->to(20, self::H - 20)->color('#ffffff'); });
        $img->drawLine(function ($line) { $line->from(self::W - 20, 20)->to(self::W - 20, self::H - 20)->color('#ffffff'); });

        // ── Step 3: Header — Platform Branding ───────────────────────────────
        // Blood drop emoji text + platform name
        $img->text('ROKTODUT', (int)(self::W / 2), 60, function ($font) use ($fontBold) {
            if (file_exists($fontBold)) $font->file($fontBold);
            $font->size(28);
            $font->color('#fbbf24'); // gold
            $font->align('center');
            $font->valign('middle');
        });
        $img->text('BLOOD DONATION PLATFORM  |  roktodut.com', (int)(self::W / 2), 90, function ($font) use ($fontReg) {
            if (file_exists($fontReg)) $font->file($fontReg);
            $font->size(14);
            $font->color('#fca5a5'); // light red
            $font->align('center');
            $font->valign('middle');
        });

        // Separator line
        $img->drawLine(function ($line) {
            $line->from(60, 115)->to(self::W - 60, 115)->color('#ffffff')->width(1);
        });

        // ── Step 4: Title ─────────────────────────────────────────────────────
        $img->text('Certificate of Appreciation', (int)(self::W / 2), 160, function ($font) use ($fontBold) {
            if (file_exists($fontBold)) $font->file($fontBold);
            $font->size(38);
            $font->color('#ffffff');
            $font->align('center');
            $font->valign('middle');
        });
        $img->text('Blood Donation Service Recognition', (int)(self::W / 2), 200, function ($font) use ($fontReg) {
            if (file_exists($fontReg)) $font->file($fontReg);
            $font->size(16);
            $font->color('#fca5a5');
            $font->align('center');
            $font->valign('middle');
        });

        // ── Step 5: "This certifies that" ────────────────────────────────────
        $img->text('This certifies that', (int)(self::W / 2), 255, function ($font) use ($fontReg) {
            if (file_exists($fontReg)) $font->file($fontReg);
            $font->size(18);
            $font->color('#fecaca');
            $font->align('center');
            $font->valign('middle');
        });

        // ── Step 6: Donor Name (large, gold) ─────────────────────────────────
        $img->text('✦  ' . $donorName . '  ✦', (int)(self::W / 2), 310, function ($font) use ($fontBold) {
            if (file_exists($fontBold)) $font->file($fontBold);
            $font->size(46);
            $font->color('#fbbf24');
            $font->align('center');
            $font->valign('middle');
        });

        // ── Step 7: Donation Details ──────────────────────────────────────────
        $detailLine1 = "has successfully donated blood ({$bloodGroup}) on {$date}";
        $img->text($detailLine1, (int)(self::W / 2), 370, function ($font) use ($fontReg) {
            if (file_exists($fontReg)) $font->file($fontReg);
            $font->size(19);
            $font->color('#ffffff');
            $font->align('center');
            $font->valign('middle');
        });

        $detailLine2 = "at {$district}, contributing to save a human life.";
        $img->text($detailLine2, (int)(self::W / 2), 400, function ($font) use ($fontReg) {
            if (file_exists($fontReg)) $font->file($fontReg);
            $font->size(19);
            $font->color('#ffffff');
            $font->align('center');
            $font->valign('middle');
        });

        // Hospital info (if available)
        if ($hospital && $hospital !== 'N/A') {
            $img->text("Hospital: {$hospital}", (int)(self::W / 2), 430, function ($font) use ($fontReg) {
                if (file_exists($fontReg)) $font->file($fontReg);
                $font->size(15);
                $font->color('#fca5a5');
                $font->align('center');
                $font->valign('middle');
            });
        }

        // ── Step 8: Total Donations Badge ────────────────────────────────────
        $badgeY   = 490;
        $badgeText = "Total Lifetime Donations: {$total}  |  Blood Component: Whole Blood";
        $badgeX = (int)((self::W - 600) / 2);
        $badgeTopY = $badgeY - 18;
        $img->drawRectangle($badgeX, $badgeTopY, function ($rect) {
            $rect->size(600, 36);
            $rect->background('#991b1b');
        });
        $img->drawLine(function($l) use($badgeX, $badgeTopY) { $l->from($badgeX, $badgeTopY)->to($badgeX + 600, $badgeTopY)->color('#fbbf24'); });
        $img->drawLine(function($l) use($badgeX, $badgeTopY) { $l->from($badgeX, $badgeTopY + 36)->to($badgeX + 600, $badgeTopY + 36)->color('#fbbf24'); });
        $img->drawLine(function($l) use($badgeX, $badgeTopY) { $l->from($badgeX, $badgeTopY)->to($badgeX, $badgeTopY + 36)->color('#fbbf24'); });
        $img->drawLine(function($l) use($badgeX, $badgeTopY) { $l->from($badgeX + 600, $badgeTopY)->to($badgeX + 600, $badgeTopY + 36)->color('#fbbf24'); });
        $img->text($badgeText, (int)(self::W / 2), $badgeY + 1, function ($font) use ($fontBold) {
            if (file_exists($fontBold)) $font->file($fontBold);
            $font->size(14);
            $font->color('#fbbf24');
            $font->align('center');
            $font->valign('middle');
        });

        // Separator line
        $img->drawLine(function ($line) {
            $line->from(60, 540)->to(self::W - 60, 540)->color('#ffffff')->width(1);
        });

        // ── Step 9: QR Code (bottom-left) ────────────────────────────────────
        $verifyUrl  = route('certificate.show', $donation->certificate_token);
        $qrTempPath = storage_path("app/public/temp-qr-cert-{$donation->id}.png");

        try {
            QrCode::format('png')->size(150)->margin(1)->generate($verifyUrl, $qrTempPath);
            if (File::exists($qrTempPath)) {
                $qrImg = $manager->read($qrTempPath);
                // White background box for QR
                $img->drawRectangle(50, 560, function ($rect) {
                    $rect->size(160, 160);
                    $rect->background('#ffffff');
                });
                $img->place($qrImg, 'top-left', 55, 565);
                File::delete($qrTempPath);
            }
        } catch (\Throwable $e) {
            // QR generation failed — skip silently
        }

        // ── Step 10: Certificate ID & Footer ─────────────────────────────────
        $img->text('Certificate ID:', 230, 580, function ($font) use ($fontReg) {
            if (file_exists($fontReg)) $font->file($fontReg);
            $font->size(13);
            $font->color('#fca5a5');
        });
        $img->text($certId, 230, 600, function ($font) use ($fontBold) {
            if (file_exists($fontBold)) $font->file($fontBold);
            $font->size(20);
            $font->color('#fbbf24');
        });

        $img->text('Issued by: Roktodut Blood Donation Platform', 230, 635, function ($font) use ($fontReg) {
            if (file_exists($fontReg)) $font->file($fontReg);
            $font->size(13);
            $font->color('#ffffff');
        });

        $img->text('Verify at: ' . $verifyUrl, 230, 658, function ($font) use ($fontReg) {
            if (file_exists($fontReg)) $font->file($fontReg);
            $font->size(12);
            $font->color('#fca5a5');
        });

        $img->text('Issued on: ' . now()->format('d F, Y'), 230, 680, function ($font) use ($fontReg) {
            if (file_exists($fontReg)) $font->file($fontReg);
            $font->size(12);
            $font->color('#fecaca');
        });

        // Right side: Platform tagline
        $img->text('"Donate Blood, Save Lives"', self::W - 60, 595, function ($font) use ($fontBold) {
            if (file_exists($fontBold)) $font->file($fontBold);
            $font->size(20);
            $font->color('#fbbf24');
            $font->align('right');
        });
        $img->text('রক্তদূত — রক্তদান সেবা প্ল্যাটফর্ম', self::W - 60, 625, function ($font) use ($fontReg) {
            if (file_exists($fontReg)) $font->file($fontReg);
            $font->size(14);
            $font->color('#fca5a5');
            $font->align('right');
        });
        $img->text('Website: roktodut.com', self::W - 60, 655, function ($font) use ($fontReg) {
            if (file_exists($fontReg)) $font->file($fontReg);
            $font->size(13);
            $font->color('#ffffff');
            $font->align('right');
        });

        // ── Save ──────────────────────────────────────────────────────────────
        $img->toPng()->save($savePath);
    }
}
