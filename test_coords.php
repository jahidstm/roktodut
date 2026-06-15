<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

$manager = new ImageManager(new Driver());
$img = $manager->read(public_path('assets/images/certificate-template.png'));

$fontBold = public_path('fonts/HindSiliguri-Bold.ttf');
$fontReg  = public_path('fonts/HindSiliguri-Regular.ttf');

$img->text('RKDT-2026-30001', 260, 871, function ($font) use ($fontBold) {
    if (file_exists($fontBold)) $font->file($fontBold);
    $font->size(18);
    $font->color('#fbbf24');
    $font->align('left');
    $font->valign('middle');
});

$img->text('roktodut.com/c/44d7c27f', 200, 912, function ($font) use ($fontReg) {
    if (file_exists($fontReg)) $font->file($fontReg);
    $font->size(18);
    $font->color('#fca5a5');
    $font->align('left');
    $font->valign('middle');
});

$img->toPng()->save(storage_path('app/public/test_cert_coords6.png'));
echo "Saved test6\n";
