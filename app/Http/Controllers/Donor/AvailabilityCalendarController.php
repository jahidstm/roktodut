<?php

namespace App\Http\Controllers\Donor;

use App\Http\Controllers\Controller;
use App\Models\DonorAvailability;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AvailabilityCalendarController extends Controller
{
    /**
     * GET /donor/availability
     * Donor-এর সব availability rules দেখানো।
     */
    public function index()
    {
        $user  = Auth::user();
        $rules = DonorAvailability::where('user_id', $user->id)
            ->orderByDesc('is_active')
            ->orderByDesc('created_at')
            ->get();

        // পরবর্তী ৩০ দিনের calendar preview
        $calendarDays = $this->buildCalendarPreview($rules);

        return view('donor.availability.index', compact('rules', 'calendarDays'));
    }

    /**
     * POST /donor/availability
     * নতুন availability rule যোগ করা।
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'type'             => ['required', 'in:weekly,specific_date,date_range'],
            'weekdays'         => ['required_if:type,weekly', 'array'],
            'weekdays.*'       => ['integer', 'between:0,6'],
            'specific_date'    => ['required_if:type,specific_date', 'date', 'after_or_equal:today'],
            'date_from'        => ['required_if:type,date_range', 'date', 'after_or_equal:today'],
            'date_to'          => ['required_if:type,date_range', 'date', 'after_or_equal:date_from'],
            'time_from'        => ['nullable', 'date_format:H:i'],
            'time_to'          => ['nullable', 'date_format:H:i', 'after:time_from'],
            'note'             => ['nullable', 'string', 'max:200'],
        ]);

        // Convert weekday array → bitmask integer
        $bitmask = null;
        if ($validated['type'] === 'weekly' && isset($validated['weekdays'])) {
            $bitmask = 0;
            foreach ($validated['weekdays'] as $day) {
                $bitmask |= (1 << (int) $day);
            }
            if ($bitmask === 0) {
                return back()->withErrors(['weekdays' => 'অন্তত একটি দিন নির্বাচন করুন।'])->withInput();
            }
        }

        DonorAvailability::create([
            'user_id'          => $user->id,
            'type'             => $validated['type'],
            'weekdays_bitmask' => $bitmask,
            'specific_date'    => $validated['specific_date'] ?? null,
            'date_from'        => $validated['date_from'] ?? null,
            'date_to'          => $validated['date_to'] ?? null,
            'time_from'        => isset($validated['time_from']) ? $validated['time_from'] . ':00' : null,
            'time_to'          => isset($validated['time_to']) ? $validated['time_to'] . ':00' : null,
            'note'             => $validated['note'] ?? null,
            'is_active'        => true,
        ]);

        return redirect()->route('donor.availability.index')
            ->with('success', 'নতুন availability rule যোগ হয়েছে!');
    }

    /**
     * DELETE /donor/availability/{availability}
     * Rule মুছে ফেলা।
     */
    public function destroy(DonorAvailability $availability)
    {
        $this->authorize('delete', $availability);
        $availability->delete();

        return redirect()->route('donor.availability.index')
            ->with('success', 'Rule মুছে ফেলা হয়েছে।');
    }

    /**
     * PATCH /donor/availability/{availability}/toggle
     * Rule on/off করা।
     */
    public function toggle(DonorAvailability $availability)
    {
        $this->authorize('update', $availability);
        $availability->update(['is_active' => !$availability->is_active]);

        return redirect()->route('donor.availability.index')
            ->with('success', $availability->is_active ? 'Rule সক্রিয় করা হয়েছে।' : 'Rule নিষ্ক্রিয় করা হয়েছে।');
    }

    /**
     * পরবর্তী ৩০ দিনের calendar preview তৈরি করা।
     * প্রতিটি দিনের জন্য: available = true/false
     */
    private function buildCalendarPreview($rules): array
    {
        $days   = [];
        $active = $rules->where('is_active', true);

        if ($active->isEmpty()) {
            // কোনো rule নেই — সব দিন "default" দেখাবে
            for ($i = 0; $i < 30; $i++) {
                $dt     = now()->setTimezone('Asia/Dhaka')->addDays($i)->startOfDay();
                $days[] = ['date' => $dt, 'available' => null, 'is_today' => $i === 0];
            }
            return $days;
        }

        for ($i = 0; $i < 30; $i++) {
            $dt        = now()->setTimezone('Asia/Dhaka')->addDays($i)->startOfDay();
            $available = $active->some(fn ($rule) => $rule->isAvailableAt($dt));
            $days[]    = ['date' => $dt, 'available' => $available, 'is_today' => $i === 0];
        }

        return $days;
    }
}
