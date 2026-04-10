<?php

$file_path = 'resources/views/dashboard.blade.php';
$lines = file($file_path);

function get_block($lines, $start, $end) {
    return implode("", array_slice($lines, $start, $end - $start));
}

$header = get_block($lines, 0, 159);
$eligibility = get_block($lines, 159, 200);
$radar = get_block($lines, 200, 375);
$stats = get_block($lines, 375, 412);
$smart_card = get_block($lines, 412, 571);
$gamification = get_block($lines, 571, 671);
$cta = get_block($lines, 671, 683);
$referral = get_block($lines, 683, 741);
$recent_requests = get_block($lines, 741, 807);
$accepted_donations = get_block($lines, 807, 860);
$static_info = get_block($lines, 860, 969);
$tail = get_block($lines, 969, count($lines));

// Modify accepted_donations rename the header
$accepted_donations = str_replace(
    'আপনি যেসব রিকোয়েস্ট অ্যাকসেপ্ট করেছেন', 
    'আপনার রেসপন্স করা রিকোয়েস্ট',
    $accepted_donations
);

$new_content = 
    $header . 
    "\n    {{-- 1. Real-Time Status / Eligibility --}}\n" .
    $eligibility .
    "\n    {{-- 2. Core Action (CTA Row) --}}\n" .
    $cta .
    "\n    {{-- 3. Actionable Queue --}}\n" .
    $radar . 
    $accepted_donations .
    "\n    {{-- 4. User Impact (Stats Grid) --}}\n" .
    $stats .
    "\n    {{-- 5. Motivation Engine (Gamification Summary) --}}\n" .
    $smart_card . 
    $gamification .
    "\n    {{-- 6. Growth Loop (Referral / Invite) --}}\n" .
    $referral . 
    $recent_requests .
    "\n    {{-- 7. Static Info (Points & Badges Rules) --}}\n" .
    $static_info .
    $tail;

file_put_contents($file_path, $new_content);

echo "Reorder successful!\n";
