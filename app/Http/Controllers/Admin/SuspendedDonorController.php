<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SuspendedDonorController extends Controller
{
    public function download(User $user): StreamedResponse
    {
        if (!$user->medical_clearance_document || !Storage::disk('local')->exists($user->medical_clearance_document)) {
            abort(404);
        }

        return Storage::disk('local')->download($user->medical_clearance_document);
    }

    public function reactivate(User $user): RedirectResponse
    {
        $path = $user->medical_clearance_document;

        $user->update([
            'is_donor' => true,
            'suspension_reason' => null,
            'medical_clearance_document' => null,
        ]);

        if ($path && Storage::disk('local')->exists($path)) {
            Storage::disk('local')->delete($path);
        }

        return back()->with('success', '✅ ডোনারকে পুনরায় সক্রিয় করা হয়েছে এবং PHI ডকুমেন্ট মুছে ফেলা হয়েছে।');
    }
}
