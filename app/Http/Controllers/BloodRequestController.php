<?php

namespace App\Http\Controllers;

use App\Models\BloodRequest;
use Illuminate\Http\Request;

class BloodRequestController extends Controller
{
    public function index(Request $request)
    {
        $requests = BloodRequest::query()
            ->with(['requester:id,name'])
            ->where('status', 'pending')
            ->orderByRaw('needed_at is null asc')
            ->orderBy('needed_at')
            ->orderByDesc('created_at')
            ->paginate(12);

        return view('requests.index', [
            'requests' => $requests,
        ]);
    }
}