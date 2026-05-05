<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class SmartCardImageController extends Controller
{
    /**
     * Asset 1: The Offline Wallet Pass (Vertical 9:16)
     * Has a big QR Code. Made for downloading and printing.
     */
    public function downloadPass($token)
    {
        $user = clone User::where('qr_token', $token)->firstOrFail();
        
        $directory = storage_path('app/public/smart-cards/passes');
        $imagePath = $directory . "/pass-{$user->id}.png";

        if (!File::exists($imagePath)) {
            if (!File::isDirectory($directory)) {
                File::makeDirectory($directory, 0755, true, true);
            }

            // Fallback template generation if it doesn't exist
            $templatePath = public_path('images/smart-card-pass-template.png');
            if (!file_exists($templatePath)) {
                if (!File::isDirectory(public_path('images'))) {
                    File::makeDirectory(public_path('images'), 0755, true, true);
                }
                $img = imagecreatetruecolor(1080, 1920); // 9:16
                imagefill($img, 0, 0, imagecolorallocate($img, 15, 23, 42)); // dark background
                imagepng($img, $templatePath);
                imagedestroy($img);
            }

            $fontPath = public_path('fonts/Inter-Black.ttf');
            $manager = new ImageManager(new Driver());
            $image = $manager->read($templatePath);

            // 1. Write the User Name
            $userName = strtoupper($user->name);
            $image->text($userName, 540, 400, function($font) use ($fontPath) {
                if (file_exists($fontPath)) {
                    $font->file($fontPath);
                }
                $font->size(80);
                $font->color('#ffffff');
                $font->align('center');
                $font->valign('middle');
            });

            // 2. Write the Blood Group
            $bloodGroup = $user->blood_group?->value ?? $user->blood_group ?? 'N/A';
            $image->text($bloodGroup, 540, 550, function($font) use ($fontPath) {
                if (file_exists($fontPath)) {
                    $font->file($fontPath);
                }
                $font->size(120);
                $font->color('#ef4444');
                $font->align('center');
                $font->valign('middle');
            });

            // 3. Generate QR Code and Overlay it
            $verifyUrl = route('public.verify', $user->qr_token);
            $qrCodePath = storage_path("app/public/temp-qr-{$user->id}.png");
            QrCode::format('png')->size(600)->margin(2)->generate($verifyUrl, $qrCodePath);

            $qrImage = $manager->read($qrCodePath);
            // Insert QR code in the middle of the vertical pass
            $image->place($qrImage, 'center', 0, 300);

            // Clean up temp QR
            if (File::exists($qrCodePath)) {
                File::delete($qrCodePath);
            }

            $image->toPng()->save($imagePath);
        }

        return response()->download($imagePath, "roktodut-smart-card-{$user->id}.png", [
            'Content-Type' => 'image/png',
            'Cache-Control' => 'public, max-age=864000'
        ]);
    }

    /**
     * Asset 2: The Social OG Card (Horizontal 16:9)
     * No QR Code. Large CTA Text. Made for Facebook.
     */
    public function socialOg($token)
    {
        $user = clone User::where('qr_token', $token)->firstOrFail();
        
        $directory = storage_path('app/public/smart-cards/og-images');
        $imagePath = $directory . "/og-{$user->id}.png";

        if (!File::exists($imagePath)) {
            if (!File::isDirectory($directory)) {
                File::makeDirectory($directory, 0755, true, true);
            }

            // Fallback template generation
            $templatePath = public_path('images/smart-card-og-template.png');
            if (!file_exists($templatePath)) {
                if (!File::isDirectory(public_path('images'))) {
                    File::makeDirectory(public_path('images'), 0755, true, true);
                }
                $img = imagecreatetruecolor(1200, 630); // 16:9
                imagefill($img, 0, 0, imagecolorallocate($img, 15, 23, 42)); // dark background
                imagepng($img, $templatePath);
                imagedestroy($img);
            }

            $fontPath = public_path('fonts/Inter-Black.ttf');
            $manager = new ImageManager(new Driver());
            $image = $manager->read($templatePath);

            // Write the User Name
            $userName = strtoupper($user->name);
            $image->text($userName, 600, 250, function($font) use ($fontPath) {
                if (file_exists($fontPath)) {
                    $font->file($fontPath);
                }
                $font->size(90);
                $font->color('#ffffff');
                $font->align('center');
                $font->valign('middle');
            });

            // Write the Blood Group
            $bloodGroup = $user->blood_group?->value ?? $user->blood_group ?? 'N/A';
            $image->text($bloodGroup, 600, 400, function($font) use ($fontPath) {
                if (file_exists($fontPath)) {
                    $font->file($fontPath);
                }
                $font->size(150);
                $font->color('#ef4444');
                $font->align('center');
                $font->valign('middle');
            });

            // In an actual design, the template itself would have "Click to View Verified Profile" baked in.

            $image->toPng()->save($imagePath);
        }

        return response()->file($imagePath, [
            'Content-Type' => 'image/png',
            'Cache-Control' => 'public, max-age=864000'
        ]);
    }
}
