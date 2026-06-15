<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use Illuminate\Support\Facades\File;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

/**
 * CertificateController
 *
 * Loads a static premium template (PNG) and overlays
 * only the dynamic donor data at fixed X,Y coordinates.
 * No background rendering in code — designer controls the look.
 */
class CertificateController extends Controller
{
    // Path to the static template (designed in Canva/AI)
    private const TEMPLATE = 'assets/images/certificate-template.png';

    // Template actual size (1024×1024 from AI generation)
    // All X,Y coordinates below are relative to this size.
    private const TW = 1024;
    private const TH = 1024;

    // ─── Text Overlay Coordinates (measured from template) ───────────────────
    // Donor Name
    private const NAME_X = 512;
    private const NAME_Y = 570;
    private const NAME_SIZE = 64;

    // Blood Group + Date
    private const DETAILS_X = 512;
    private const DETAILS_Y = 690;
    private const DETAILS_SIZE = 24;

    // Bottom-left block (values only, labels are in template)
    private const CERTID_X   = 260;
    private const CERTID_Y   = 871;
    private const VERIFY_X   = 210;
    private const VERIFY_Y   = 912;

    // QR Code placement (centered in the bottom blank space)
    private const QR_X = 470;
    private const QR_Y = 840;
    private const QR_SIZE = 130;

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
        $donationDate = $donation->donation_date?->format('d F, Y') ?? now()->format('d F, Y');
        $totalCount   = $donor->total_verified_donations ?? 1;
        $certId       = 'RKDT-' . now()->format('Y') . '-' . str_pad($donation->id, 5, '0', STR_PAD_LEFT);

        $shareTitle = "{$donorName} donated blood ({$bloodGroup}) on {$donationDate}";
        $shareDesc  = "This person has donated blood {$totalCount} time(s) through Roktodut Blood Donation Platform.";
        $imageUrl   = route('certificate.download', $token) . '?v=' . time();
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

        if (!File::exists($imagePath)) {
            if (!File::isDirectory($directory)) {
                File::makeDirectory($directory, 0755, true, true);
            }
            $this->generateCertificate($donation, $imagePath);
        }

        $donor    = $donation->donor;
        $safeName = $donor->name ? strtolower(str_replace([' ', '.'], ['-', ''], $donor->name)) : $donation->id;
        $filename = "roktodut-certificate-{$safeName}.png";

        return response()->download($imagePath, $filename, [
            'Content-Type'  => 'image/png',
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Core: Load template → overlay text → save
    // ─────────────────────────────────────────────────────────────────────────
    private function generateCertificate(Donation $donation, string $savePath): void
    {
        $templatePath = public_path(self::TEMPLATE);

        if (!File::exists($templatePath)) {
            throw new \RuntimeException('Certificate template not found at: ' . $templatePath);
        }

        $donor      = $donation->donor;
        $bloodGroup = $donor->blood_group?->value ?? (string) $donor->blood_group ?? 'N/A';
        $donorName  = strtoupper($donor->name ?? 'DONOR');
        $district   = $donor->district?->name ?? $donation->district ?? 'Bangladesh';
        $date       = $donation->donation_date?->format('d F, Y') ?? now()->format('d F, Y');
        $certId     = 'RKDT-' . now()->format('Y') . '-' . str_pad($donation->id, 5, '0', STR_PAD_LEFT);

        // Fonts (HindSiliguri supports Bengali)
        $fontBold = public_path('fonts/HindSiliguri-Bold.ttf');
        $fontReg  = public_path('fonts/HindSiliguri-Regular.ttf');

        $manager = new ImageManager(new Driver());
        $img     = $manager->read($templatePath);

        // ── 1. Donor Name (large, centered, gold/cream) ───────────────────
        $img->text($donorName, self::NAME_X, self::NAME_Y, function ($font) use ($fontBold) {
            if (file_exists($fontBold)) $font->file($fontBold);
            $font->size(self::NAME_SIZE);
            $font->color('#fbbf24'); // gold
            $font->align('center');
            $font->valign('middle');
        });

        // ── 2. Blood Group + Date (centered, white) ────────────────────────
        $details = "Blood Group: {$bloodGroup}   |   Date: {$date}";
        $img->text($details, self::DETAILS_X, self::DETAILS_Y, function ($font) use ($fontReg) {
            if (file_exists($fontReg)) $font->file($fontReg);
            $font->size(self::DETAILS_SIZE);
            $font->color('#ffffff');
            $font->align('center');
            $font->valign('middle');
        });

        // ── 3. Bottom-left: Certificate ID ────────────────────────────────
        $img->text($certId, self::CERTID_X, self::CERTID_Y, function ($font) use ($fontBold) {
            if (file_exists($fontBold)) $font->file($fontBold);
            $font->size(18);
            $font->color('#fbbf24');
            $font->align('left');
            $font->valign('middle');
        });

        // ── 4. Verify at (shortened URL) ─────────────────────────────────
        $shortUrl = 'roktodut.com/c/' . substr($donation->certificate_token, 0, 8);
        $img->text($shortUrl, self::VERIFY_X, self::VERIFY_Y, function ($font) use ($fontReg) {
            if (file_exists($fontReg)) $font->file($fontReg);
            $font->size(18);
            $font->color('#fca5a5');
            $font->align('left');
            $font->valign('middle');
        });

        // ── 6. QR Code overlay ────────────────────────────────────────────
        $verifyUrl  = route('certificate.show', $donation->certificate_token);
        $qrTempPath = storage_path("app/temp-qr-cert-{$donation->id}.png");

        try {
            QrCode::format('png')->size(self::QR_SIZE)->margin(1)->generate($verifyUrl, $qrTempPath);

            if (File::exists($qrTempPath)) {
                // White padding box behind QR for visibility on dark background
                $img->drawRectangle(self::QR_X - 3, self::QR_Y - 3, function ($rect) {
                    $rect->size(self::QR_SIZE + 6, self::QR_SIZE + 6);
                    $rect->background('#ffffff');
                });
                $qrImg = $manager->read($qrTempPath);
                $img->place($qrImg, 'top-left', self::QR_X, self::QR_Y);
                File::delete($qrTempPath);
            }
        } catch (\Throwable $e) {
            // QR failure is non-fatal
        }

        // ── Save final PNG ─────────────────────────────────────────────────
        $img->toPng()->save($savePath);
    }
}
