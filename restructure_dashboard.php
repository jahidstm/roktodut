<?php
$file = 'resources/views/dashboard.blade.php';
$content = file_get_contents($file);

function extract_section($content, $start, $end) {
    $s = strpos($content, $start);
    if ($s === false) return "";
    $e = strpos($content, $end, $s);
    if ($e === false) return "";
    $e += strlen($end);
    return substr($content, $s, $e - $s);
}

// Lines 1-160
$header_end = "{{-- 1. Real-Time Status / Eligibility --}}";
$header_part = substr($content, 0, strpos($content, $header_end));

$eligibility_end = "{{-- 2. Core Action (CTA Row) --}}";
$el_st = strpos($content, $header_end);
$el_en = strpos($content, $eligibility_end);
$eligibility_part = substr($content, $el_st, $el_en - $el_st);

$cta_end = "{{-- 3. Actionable Queue --}}";
$cta_st = strpos($content, $eligibility_end);
$cta_en = strpos($content, $cta_end);
$cta_part = substr($content, $cta_st, $cta_en - $cta_st);

$queue_end = "{{-- 🎯 ডোনারের ড্যাশবোর্ডে অ্যাকসেপ্ট করা রিকোয়েস্টের লিস্ট --}}";
$queue_st = strpos($content, $cta_end);
$queue_en = strpos($content, $queue_end);
$queue_part = substr($content, $queue_st, $queue_en - $queue_st);

$acc_end = "{{-- 4. User Impact (Stats Grid) --}}";
$acc_st = strpos($content, $queue_end);
$acc_en = strpos($content, $acc_end);

$stats_end = "{{-- 5. Motivation Engine (Gamification Summary) --}}";
$stats_st = strpos($content, $acc_end);
$stats_en = strpos($content, $stats_end);
$stats_part = substr($content, $stats_st, $stats_en - $stats_st);

$card_end = "{{-- ══════════════════════════════════════════\n         🏆 গ্যামিফিকেশন উইজেট\n    ══════════════════════════════════════════ --}}";
$card_st = strpos($content, $stats_end);
$card_en = strpos($content, $card_end);
$card_part = substr($content, $card_st, $card_en - $card_st);

$gami_end = "{{-- 6. Growth Loop (Referral / Invite) --}}";
$gami_st = strpos($content, $card_end);
$gami_en = strpos($content, $gami_end);
$gami_part = substr($content, $gami_st, $gami_en - $gami_st);

$ref_end = "@if(isset(\$recentRequests))";
$ref_st = strpos($content, $gami_end);
$ref_en = strpos($content, $ref_end);
$ref_part = substr($content, $ref_st, $ref_en - $ref_st);

$footer_part = substr($content, $ref_en);

// A) Card modification
$card_part = str_replace("mb-10 relative overflow-hidden", "mb-10 mt-6 relative overflow-hidden", $card_part);
$card_part = str_replace("{{-- 5. Motivation Engine (Gamification Summary) --}}", "", $card_part);
$card_part = str_replace("{{-- ══════════════════════════════════════════\n         🪪 Digital Smart Card — QR Verified Identity\n    ══════════════════════════════════════════ --}}", "{{-- 🪪 A) Digital Smart Card (Top Level Identity) --}}", $card_part);

// B) Eligibility toggle
$eligibility_part = str_replace("{{-- 1. Real-Time Status / Eligibility --}}", "{{-- B) Eligibility + Availability --}}", $eligibility_part);
$availability_toggle = '
    <div class="mb-4 bg-white p-6 rounded-3xl border border-slate-200 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
        <div>
            <h3 class="text-base font-black text-slate-900">আপনার বর্তমান অবস্থা</h3>
            <p class="text-sm font-semibold text-slate-500">জরুরি প্রয়োজনে আপনি কি রক্ত দিতে প্রস্তুত?</p>
        </div>
        <form action="{{ route(\'profile.emergency.toggle\') }}" method="POST">
            @csrf
            <button type="submit" class="flex items-center gap-3 px-6 py-3 rounded-2xl font-black transition-all shadow-sm border {{ $user->is_available ? \'bg-emerald-50 border-emerald-200 text-emerald-700 hover:bg-emerald-100\' : \'bg-slate-50 border-slate-200 text-slate-700 hover:bg-slate-100\' }}">
                <span class="relative flex h-3 w-3">
                    @if($user->is_available)
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
                    @else
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500"></span>
                    @endif
                </span>
                {{ $user->is_available ? \'আমি এখন উপলব্ধ\' : \'আমি ব্যস্ত\' }}
            </button>
        </form>
    </div>
';
$eligibility_part = $availability_toggle . "\n" . $eligibility_part;

// D) Queue
$queue_part = str_replace("লোকাল ইমার্জেন্সি রাডার", "আপনার এলাকায় জরুরি অনুরোধ", $queue_part);
$queue_part = str_replace("{{ auth()->user()->district?->name ?? 'আপনার জেলা' }}-তে সক্রিয় জরুরি রিকোয়েস্টসমূহ", "আপনার এলাকায় স্ক্যান করা হচ্ছে…", $queue_part);
$queue_part = str_replace("আপনার এলাকায় এখন কোনো জরুরি রিকোয়েস্ট নেই", "এই মুহূর্তে জরুরি অনুরোধ নেই—আপনার এলাকা পর্যবেক্ষণে আছে।", $queue_part);

// E) Ongoing
$ongoing_part = '
    {{-- E) My Commitments (Ongoing) --}}
    @if(isset($ongoingCommitments) && $ongoingCommitments->count() > 0)
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden mb-10">
        <div class="px-6 py-5 border-b border-slate-100 flex justify-between items-center bg-blue-50/50">
            <div>
                <h2 class="text-lg font-extrabold text-blue-900 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                    আমার চলমান কমিটমেন্ট
                </h2>
                <p class="text-sm text-blue-700/70 font-medium mt-1">যে রিকোয়েস্টগুলোতে আপনি রক্ত দেওয়ার প্রতিশ্রুতি দিয়েছেন</p>
            </div>
            <a href="{{ route(\'requests.index\') }}" class="text-xs font-bold text-blue-600 hover:underline">সব দেখুন</a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <tbody class="divide-y divide-slate-100 text-sm">
                    @foreach($ongoingCommitments as $commitment)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-6 py-4">
                                <div class="font-extrabold text-slate-900">রোগী: {{ $commitment->bloodRequest->patient_name ?? \'N/A\' }}</div>
                                <div class="text-xs font-bold text-slate-500 mt-0.5">গ্রুপ: <span class="text-blue-600">{{ $commitment->bloodRequest->blood_group?->value ?? $commitment->bloodRequest->blood_group }}</span></div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-semibold text-slate-700">{{ $commitment->bloodRequest->hospital ?? \'N/A\' }}</div>
                                <div class="text-xs font-bold text-slate-500 mt-0.5">{{ $commitment->bloodRequest->district?->name ?? \'অজানা জেলা\' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                @if($commitment->verification_status === \'pending\')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-extrabold bg-blue-100 text-blue-800">চলমান</span>
                                @elseif($commitment->verification_status === \'claimed\')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-extrabold bg-emerald-100 text-emerald-800">রিভিউ হচ্ছে</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route(\'requests.show\', $commitment->blood_request_id) }}" class="inline-flex items-center justify-center px-3 py-1.5 border border-slate-200 rounded-lg text-xs font-extrabold text-slate-700 hover:bg-slate-100 transition">
                                    ডিটেইলস
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
';

// F) Impact Proof Zone
$impact_part = '
    {{-- F) Impact Proof Zone --}}
    <div class="mb-10">
        <div class="bg-gradient-to-br from-slate-900 to-slate-800 rounded-3xl p-8 shadow-xl text-white mb-6">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-6">
                <div>
                    <h2 class="text-3xl font-black text-rose-500 mb-2 flex items-center gap-3">
                        <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                        {{ $livesSaved ?? 0 }} জনের জীবন বাঁচিয়েছেন
                    </h2>
                    <p class="text-slate-300 font-medium text-sm max-w-lg">
                        আপনার প্রতিটি রক্তদান গড়ে ৩ জনের জীবন বাঁচাতে পারে। আপনার ভেরিফাইড ডোনেশনের ভিত্তিতে এই পরিসংখ্যান তৈরি হয়েছে। 
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100">
                <h3 class="text-lg font-extrabold text-slate-900">ভেরিফাইড রক্তদান হিস্ট্রি</h3>
                <p class="text-sm text-slate-500 font-medium mt-1">আপনার অতীতের সফল রক্তদানের লগ</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100 text-xs uppercase tracking-wider font-bold text-slate-500">
                            <th class="px-6 py-4">তারিখ</th>
                            <th class="px-6 py-4">হাসপাতাল ও লোকেশন</th>
                            <th class="px-6 py-4">রিকোয়েস্ট রেফারেন্স</th>
                            <th class="px-6 py-4 text-right">স্ট্যাটাস</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        @if(isset($donationHistory))
                            @forelse($donationHistory as $history)
                                <tr class="hover:bg-slate-50 transition">
                                    <td class="px-6 py-4">
                                        <div class="font-extrabold text-slate-900">{{ $history->fulfilled_at ? $history->fulfilled_at->format(\'d M, Y\') : \'N/A\' }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-semibold text-slate-700">{{ $history->bloodRequest->hospital ?? \'N/A\' }}</div>
                                        <div class="text-xs text-slate-500 font-medium">{{ $history->bloodRequest->district?->name ?? \'N/A\' }}, {{ $history->bloodRequest->upazila?->name ?? \'\' }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="text-xs font-bold text-slate-400 font-mono bg-slate-100 px-2 py-1 rounded-md">
                                            REQ-{{ str_pad($history->blood_request_id, 4, \'0\', STR_PAD_LEFT) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-black bg-emerald-100 text-emerald-800 uppercase tracking-widest">
                                            সম্পন্ন
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center">
                                        <div class="text-4xl mb-3">📋</div>
                                        <p class="font-bold text-slate-600">কোনো হিস্ট্রি পাওয়া যায়নি</p>
                                        <p class="text-xs text-slate-500 mt-1">আপনার প্রথম রক্তদানের পর এখানে তা সংরক্ষিত থাকবে।</p>
                                    </td>
                                </tr>
                            @endforelse
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
';

$gami_part = str_replace("sm:flex-row items-start sm:items-center justify-between gap-6", "sm:flex-row items-center justify-between gap-4", $gami_part);
$gami_part = str_replace("text-3xl", "text-xl", $gami_part);
$gami_part = str_replace("text-lg sm:text-xl", "text-base sm:text-lg", $gami_part);

    
$new_content = $header_part . $card_part . $eligibility_part . $cta_part . $queue_part . $ongoing_part . $impact_part . $stats_part . $gami_part . $ref_part . $footer_part;

file_put_contents($file, $new_content);

echo "Dashboard restructuring completed.\n";
