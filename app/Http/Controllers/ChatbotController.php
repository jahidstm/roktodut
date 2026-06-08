<?php

namespace App\Http\Controllers;

use App\Models\BloodInventory;
use App\Models\District;
use App\Models\User;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatbotController extends Controller
{
    // ── Intent keywords for live-data injection ───────────────────────────────

    private const INVENTORY_KEYWORDS = [
        'ব্লাড ব্যাংক', 'রক্ত পাওয়া', 'রক্ত পাব', 'ইনভেন্টরি', 'স্টক',
        'blood bank', 'donate spot', 'কোথায় donate', 'কোথায় দেব',
    ];

    private const NEED_BLOOD_KEYWORDS = [
        'রক্ত লাগবে', 'রক্ত চাই', 'জরুরি রক্ত', 'রক্তের আবেদন',
        'request করতে', 'রিকোয়েস্ট করতে', 'blood needed', 'need blood',
    ];

    private const DONOR_KEYWORDS = [
        'ডোনার খুঁজ', 'donor খুঁজ', 'রক্তদাতা', 'ডোনার পাব',
        'কে দেবে', 'donor পাব', 'donor search',
    ];

    // ─────────────────────────────────────────────────────────────────────────
    public function ask(Request $request)
    {
        $userMessage = trim((string) $request->input('message', ''));
        $rawHistory  = (array) $request->input('history', []);

        if ($userMessage === '') {
            return response()->json(['reply' => 'অনুগ্রহ করে আপনার প্রশ্নটি লিখুন।'], 422);
        }

        if (mb_strlen($userMessage) > 1000) {
            return response()->json(['reply' => 'প্রশ্নটি একটু ছোট করে লিখুন (সর্বোচ্চ ১০০০ অক্ষর)।'], 422);
        }

        $apiKey = (string) env('GEMINI_API_KEY', '');
        $model  = trim((string) env('GEMINI_MODEL', 'gemini-2.5-flash'));
        $maxOutputTokens = max(256, min((int) env('GEMINI_MAX_OUTPUT_TOKENS', 768), 4096));

        if ($apiKey === '') {
            return response()->json(['reply' => 'GEMINI_API_KEY সেট করা নেই।'], 500);
        }

        // ── 1. Build personalized system instruction ──────────────────────────
        $systemInstruction = $this->buildSystemPrompt($userMessage);

        // ── 2. Build multi-turn conversation history (max 10 turns) ──────────
        $contents = $this->buildContents($rawHistory, $userMessage);

        // ── 3. Detect intent → action payload ────────────────────────────────
        $action = $this->detectAction($userMessage);

        // ── 4. Call Gemini API ────────────────────────────────────────────────
        $endpoint = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent";

        try {
            $response = Http::timeout(45)->post("{$endpoint}?key={$apiKey}", [
                'system_instruction' => [
                    'parts' => [['text' => $systemInstruction]],
                ],
                'contents'         => $contents,
                'generationConfig' => [
                    'temperature'     => 0.35,
                    'maxOutputTokens' => $maxOutputTokens,
                ],
            ])->throw();
        } catch (ConnectionException) {
            return response()->json(['reply' => 'এই মুহূর্তে এআই সার্ভারের সাথে সংযোগ করা যাচ্ছে না। একটু পরে আবার চেষ্টা করুন।'], 503);
        } catch (RequestException $e) {
            $status     = $e->response?->status();
            $apiMessage = (string) data_get($e->response?->json(), 'error.message', '');

            Log::warning('Gemini request failed', ['status' => $status, 'model' => $model, 'api_message' => $apiMessage]);

            return match ($status) {
                404 => response()->json(['reply' => 'Gemini model পাওয়া যায়নি।'], 502),
                403 => response()->json(['reply' => 'Gemini API key অনুমোদিত না।'], 502),
                429 => response()->json(['reply' => 'অনেক বেশি রিকোয়েস্ট হয়েছে। কিছুক্ষণ পর আবার চেষ্টা করুন।'], 429),
                default => response()->json(['reply' => 'এআই উত্তর তৈরিতে সমস্যা হয়েছে।'], 502),
            };
        }

        // ── 5. Parse & clean reply ────────────────────────────────────────────
        $parts = (array) $response->json('candidates.0.content.parts', []);
        $reply = collect($parts)->pluck('text')->filter()->implode("\n");
        $reply = $this->cleanReply($reply);

        return response()->json([
            'reply'  => $reply,
            'action' => $action,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Private helpers
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Build the system prompt, injecting live user context + live inventory data.
     */
    private function buildSystemPrompt(string $userMessage): string
    {
        // ── User personalization ──────────────────────────────────────────────
        $userContext = "ইউজার: অতিথি (লগইন করা নেই)।";

        if ($user = Auth::user()) {
            /** @var User $user */
            $bloodGroup  = $user->blood_group instanceof \BackedEnum
                ? $user->blood_group->value
                : (string) ($user->blood_group ?? 'অজানা');

            $districtName = $user->district?->bn_name ?? $user->district?->name ?? 'অজানা';

            $donorStatus = $user->is_donor
                ? "সক্রিয় ডোনার (মোট ডোনেশন: {$user->total_donations} বার)"
                : "ডোনার নন (recipient)";

            $lastDonated = $user->last_donated_at
                ? \Carbon\Carbon::parse($user->last_donated_at)->diffForHumans()
                : 'এখন পর্যন্ত কোনো ডোনেশন নেই';

            $userContext = <<<TEXT
            লগইন করা ইউজারের তথ্য:
            - নাম: {$user->name}
            - রক্তের গ্রুপ: {$bloodGroup}
            - জেলা: {$districtName}
            - স্ট্যাটাস: {$donorStatus}
            - সর্বশেষ ডোনেশন: {$lastDonated}
            TEXT;
        }

        // ── Live inventory context (inject only when relevant) ─────────────────
        $inventoryContext = '';
        if ($this->matchesKeywords($userMessage, self::INVENTORY_KEYWORDS)) {
            $inventoryContext = $this->buildInventoryContext();
        }

        // ── Live shortage alert (for need-blood queries) ───────────────────────
        $shortageContext = '';
        if ($this->matchesKeywords($userMessage, self::NEED_BLOOD_KEYWORDS)) {
            $shortageContext = $this->buildShortageContext();
        }

        return <<<TEXT
        তুমি রক্তদূত (RoktoDut) প্ল্যাটফর্মের AI সহকারী। তোমার কাজ:
        ১) রক্তদান ও স্বাস্থ্য সচেতনতা বিষয়ে নিরাপদ, তথ্যভিত্তিক উত্তর দেওয়া।
        ২) রক্তদূত ওয়েবসাইট ব্যবহারে গাইড করা।
        ৩) ইউজারের ব্যক্তিগত তথ্য ব্যবহার করে প্রাসঙ্গিক উত্তর দেওয়া।

        ওয়েবসাইটের ফিচারসমূহ:
        - স্মার্ট ডোনার সার্চ: /search
        - রক্তের আবেদন (Request): /requests/create
        - দীর্ঘমেয়াদী সাবস্ক্রিপশন (Thalassemia/Dialysis): /my-subscriptions
        - ব্লাড ব্যাংক ইনভেন্টরি: /blood-bank
        - লিডারবোর্ড: /leaderboard
        - ডোনার ড্যাশবোর্ড: /donor/dashboard
        - প্রোফাইল এডিট: /profile

        {$userContext}

        {$inventoryContext}

        {$shortageContext}

        নিরাপত্তা নীতি:
        - রোগ নির্ণয় বা ওষুধের পরামর্শ দেবে না।
        - জরুরি উপসর্গে দ্রুত চিকিৎসক/হাসপাতালে যেতে বলবে।

        উত্তর বাংলায় দাও, ২-৪ বাক্যে compact রাখো।
        Markdown ব্যবহার করবে না (*, **, #, bullet, code block নয়)।
        অপ্রয়োজনীয় ফাঁকা লাইন দেবে না।
        TEXT;
    }

    /**
     * Build Gemini contents array from history + new message.
     * Max last 10 exchanges (20 turns).
     */
    private function buildContents(array $rawHistory, string $newMessage): array
    {
        $contents = [];

        // Take last 10 turns max (5 user + 5 bot = 10 messages)
        $slice = array_slice($rawHistory, -10);

        foreach ($slice as $entry) {
            $role = ($entry['isUser'] ?? false) ? 'user' : 'model';
            $text = trim((string) ($entry['text'] ?? ''));
            if ($text === '') continue;
            $contents[] = ['role' => $role, 'parts' => [['text' => $text]]];
        }

        // Add current user message
        $contents[] = ['role' => 'user', 'parts' => [['text' => $newMessage]]];

        return $contents;
    }

    /**
     * Detect intent from user message → return a structured Action object or null.
     */
    private function detectAction(string $message): ?array
    {
        if ($this->matchesKeywords($message, self::NEED_BLOOD_KEYWORDS)) {
            return [
                'type'  => 'link',
                'label' => '🩸 রক্তের আবেদন করুন',
                'url'   => '/requests/create',
                'style' => 'danger',
            ];
        }

        if ($this->matchesKeywords($message, self::DONOR_KEYWORDS)) {
            return [
                'type'  => 'link',
                'label' => '🔍 ডোনার খুঁজুন',
                'url'   => '/search',
                'style' => 'primary',
            ];
        }

        if ($this->matchesKeywords($message, self::INVENTORY_KEYWORDS)) {
            return [
                'type'  => 'link',
                'label' => '🏥 ব্লাড ব্যাংক দেখুন',
                'url'   => '/blood-bank',
                'style' => 'info',
            ];
        }

        return null;
    }

    /**
     * Build live inventory context string.
     */
    private function buildInventoryContext(): string
    {
        try {
            $inventories = BloodInventory::with('organization')
                ->orderBy('units_available', 'desc')
                ->limit(8)
                ->get();

            if ($inventories->isEmpty()) {
                return "লাইভ ইনভেন্টরি: এই মুহূর্তে কোনো ব্লাড ব্যাংক ডেটা নেই।";
            }

            $lines = $inventories->map(function ($inv) {
                $org = $inv->organization?->name ?? 'অজানা';
                return "{$org}: {$inv->blood_group} — {$inv->units_available} ব্যাগ ({$inv->stock_label})";
            })->implode('; ');

            return "লাইভ ব্লাড ব্যাংক ইনভেন্টরি (এইমাত্র আপডেটেড): {$lines}";
        } catch (\Throwable) {
            return '';
        }
    }

    /**
     * Build shortage context string showing critically low groups.
     */
    private function buildShortageContext(): string
    {
        try {
            $shortages = BloodInventory::where('units_available', '<', 3)
                ->with('organization')
                ->limit(5)
                ->get();

            if ($shortages->isEmpty()) {
                return "বর্তমানে কোনো গুরুতর রক্ত সংকট নেই। তবুও জরুরি অবস্থায় রক্তের আবেদন করুন।";
            }

            $groups = $shortages->pluck('blood_group')->unique()->implode(', ');
            return "⚠️ সংকটাপন্ন রক্তের গ্রুপ (৩ ব্যাগের কম): {$groups}। এই গ্রুপের রক্তের জন্য দ্রুত আবেদন করুন।";
        } catch (\Throwable) {
            return '';
        }
    }

    /**
     * Case-insensitive keyword matching.
     */
    private function matchesKeywords(string $text, array $keywords): bool
    {
        $lowerText = mb_strtolower($text);
        foreach ($keywords as $keyword) {
            if (str_contains($lowerText, mb_strtolower($keyword))) {
                return true;
            }
        }
        return false;
    }

    /**
     * Strip markdown artifacts and truncate to 5 sentences.
     */
    private function cleanReply(string $reply): string
    {
        $reply = str_replace(["\u{00A0}", '*', '#', '`', '_'], ' ', $reply);
        $reply = preg_replace('/^[\-\*\x{2022}]\s+/mu', '', $reply) ?? $reply;
        $reply = preg_replace('/^[\h]+/mu', '', $reply) ?? $reply;
        $reply = preg_replace("/\R{2,}/u", "\n", $reply) ?? $reply;
        $reply = preg_replace('/[ \t]{2,}/u', ' ', $reply) ?? $reply;
        $reply = trim($reply);

        $sentences = preg_split('/(?>=[।!?])\s+/u', $reply, -1, PREG_SPLIT_NO_EMPTY);
        if (is_array($sentences) && count($sentences) > 5) {
            $reply = implode(' ', array_slice($sentences, 0, 5));
        }

        return $reply !== '' ? $reply : 'দুঃখিত, এই মুহূর্তে উত্তর তৈরি করা যাচ্ছে না। অনুগ্রহ করে আবার চেষ্টা করুন।';
    }
}
