<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Donation;
use Illuminate\Support\Facades\File;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

$donation = Donation::whereNotNull('certificate_token')->with(['donor.district'])->first();
if (!$donation) { die("No donation with cert token found.\n"); }

$templatePath = public_path('assets/images/certificate-template.png');
echo "Template exists: " . (file_exists($templatePath) ? 'YES' : 'NO') . "\n";
echo "Template size: " . (file_exists($templatePath) ? getimagesize($templatePath)[0].'x'.getimagesize($templatePath)[1] : 'N/A') . "\n";

try {
    $manager = new ImageManager(new Driver());
    $img = $manager->read($templatePath);
    echo "Image read OK. Size: " . $img->width() . "x" . $img->height() . "\n";

    $donor = $donation->donor;
    $donorName = strtoupper($donor->name ?? 'DONOR');
    $bloodGroup = $donor->blood_group?->value ?? 'N/A';
    $date = $donation->donation_date?->format('d F, Y') ?? now()->format('d F, Y');
    $certId = 'RKDT-2026-' . str_pad($donation->id, 5, '0', STR_PAD_LEFT);
    $district = $donor->district?->name ?? $donation->district ?? 'Bangladesh';

    // Name overlay
    $img->text($donorName, 512, 542, function($font) {
        $font->size(42);
        $font->color('#fbbf24');
        $font->align('center');
        $font->valign('middle');
    });
    echo "Name text applied OK\n";

    // Details overlay
    $img->text("Blood Group: {$bloodGroup}   |   Date: {$date}   |   District: {$district}", 512, 648, function($font) {
        $font->size(18);
        $font->color('#ffffff');
        $font->align('center');
        $font->valign('middle');
    });
    echo "Details text applied OK\n";

    // Cert ID
    $img->text('Certificate ID: ' . $certId, 105, 850, function($font) {
        $font->size(13);
        $font->color('#fbbf24');
    });

    // QR
    $qrTempPath = storage_path("app/public/temp-qr-test.png");
    QrCode::format('png')->size(130)->margin(1)->generate('https://roktodut.com/test', $qrTempPath);
    if (file_exists($qrTempPath)) {
        $img->drawRectangle(337, 837, function($r) { $r->size(136, 136); $r->background('#ffffff'); });
        $qrImg = $manager->read($qrTempPath);
        $img->place($qrImg, 'top-left', 340, 840);
        unlink($qrTempPath);
        echo "QR applied OK\n";
    }

    $outPath = storage_path('app/public/certificates/cert-test.png');
    File::makeDirectory(dirname($outPath), 0755, true, true);
    $img->toPng()->save($outPath);
    echo "SAVED OK to: " . $outPath . "\n";
    echo "File size: " . filesize($outPath) . " bytes\n";

} catch (\Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}
