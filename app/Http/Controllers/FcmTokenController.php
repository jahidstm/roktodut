<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateFcmTokenRequest;
use Illuminate\Http\JsonResponse;

class FcmTokenController extends Controller
{
    public function __invoke(UpdateFcmTokenRequest $request): JsonResponse
    {
        $user = $request->user();
        $user->fcm_token = $request->validated('fcm_token');
        $user->save();

        return response()->json([
            'status' => 'ok',
            'message' => 'FCM token updated successfully.',
        ]);
    }
}
