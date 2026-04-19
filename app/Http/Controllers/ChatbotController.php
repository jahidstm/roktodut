<?php

namespace App\Http\Controllers;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatbotController extends Controller
{
    public function ask(Request $request)
    {
        $userMessage = trim((string) $request->input('message', ''));

        if ($userMessage === '') {
            return response()->json([
                'reply' => 'অনুগ্রহ করে আপনার প্রশ্নটি লিখুন।',
            ], 422);
        }

        if (mb_strlen($userMessage) > 1000) {
            return response()->json([
                'reply' => 'প্রশ্নটি একটু ছোট করে লিখুন (সর্বোচ্চ ১০০০ অক্ষর)।',
            ], 422);
        }

        $apiKey = (string) env('GEMINI_API_KEY', '');
        $model = trim((string) env('GEMINI_MODEL', 'gemini-2.5-flash'));
        $maxOutputTokens = (int) env('GEMINI_MAX_OUTPUT_TOKENS', 768);
        $maxOutputTokens = max(256, min($maxOutputTokens, 4096));

        if ($apiKey === '') {
            return response()->json([
                'reply' => 'GEMINI_API_KEY সেট করা নেই। অনুগ্রহ করে সার্ভার কনফিগারেশন চেক করুন।',
            ], 500);
        }

        $systemInstruction = <<<'TEXT'
তুমি রক্তদূত (RoktoDut) প্ল্যাটফর্মের AI সহকারী।
তোমার কাজ:
1) রক্তদান ও সাধারণ স্বাস্থ্য সচেতনতা বিষয়ে নিরাপদ, তথ্যভিত্তিক উত্তর দেওয়া।
2) রক্তদূত ওয়েবসাইট ব্যবহারের গাইড দেওয়া।

ওয়েবসাইট সম্পর্কিত জানা তথ্য:
- ডোনার সার্চ: স্মার্ট ডোনার সার্চ পেজ
- রক্ত অনুরোধ: রক্ত দিন / requests ফিচার
- লিডারবোর্ড, ব্লগ, কন্ট্যাক্ট, অর্গানাইজেশন রেজিস্ট্রেশন
- প্রোফাইল, নোটিফিকেশন এবং ভেরিফিকেশন ফিচার

নিরাপত্তা নীতি:
- রোগ নির্ণয় করবে না।
- ওষুধের নাম বা প্রেসক্রিপশন দেবে না।
- জরুরি উপসর্গ হলে দ্রুত নিকটস্থ হাসপাতাল/চিকিৎসকের পরামর্শ নিতে বলবে।
- প্রয়োজন হলে সংক্ষিপ্ত ডিসক্লেমার দেবে।

উত্তর বাংলায় দাও, ২-৫টি ছোট বাক্যে compact রাখো।
Markdown ব্যবহার করবে না (কোনো *, **, #, বুলেট বা code block নয়)।
শুরুর আগে/মাঝে অপ্রয়োজনীয় ফাঁকা লাইন দেবে না।
TEXT;

        $endpoint = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent";

        try {
            $response = Http::timeout(45)->post("{$endpoint}?key={$apiKey}", [
                'system_instruction' => [
                    'parts' => [
                        ['text' => $systemInstruction],
                    ],
                ],
                'contents' => [
                    [
                        'role' => 'user',
                        'parts' => [
                            ['text' => $userMessage],
                        ],
                    ],
                ],
                'generationConfig' => [
                    'temperature' => 0.4,
                    'maxOutputTokens' => $maxOutputTokens,
                ],
            ])->throw();
        } catch (ConnectionException $e) {
            return response()->json([
                'reply' => 'এই মুহূর্তে এআই সার্ভারের সাথে সংযোগ করা যাচ্ছে না। একটু পরে আবার চেষ্টা করুন।',
            ], 503);
        } catch (RequestException $e) {
            $status = $e->response?->status();
            $apiMessage = (string) data_get($e->response?->json(), 'error.message', '');

            Log::warning('Gemini request failed', [
                'status' => $status,
                'model' => $model,
                'api_message' => $apiMessage,
            ]);

            if ($status === 404) {
                return response()->json([
                    'reply' => 'Gemini model পাওয়া যায়নি। .env ফাইলে GEMINI_MODEL=gemini-2.5-flash সেট করে আবার চেষ্টা করুন।',
                ], 502);
            }

            if ($status === 403) {
                return response()->json([
                    'reply' => 'Gemini API key অনুমোদিত না। API key এবং project permission চেক করুন।',
                ], 502);
            }

            if ($status === 429) {
                return response()->json([
                    'reply' => 'অনেক বেশি রিকোয়েস্ট হয়েছে। কিছুক্ষণ পর আবার চেষ্টা করুন।',
                ], 429);
            }

            return response()->json([
                'reply' => 'এআই উত্তর তৈরিতে সমস্যা হয়েছে। একটু পরে আবার চেষ্টা করুন।',
            ], 502);
        }

        $parts = (array) $response->json('candidates.0.content.parts', []);
        $reply = collect($parts)
            ->pluck('text')
            ->filter()
            ->implode("\n");

        $reply = str_replace(["\u{00A0}", '*', '#', '`', '_'], ' ', $reply);
        $reply = preg_replace('/^[\-\*\x{2022}]\s+/mu', '', $reply) ?? $reply;
        $reply = preg_replace('/^[\h]+/mu', '', $reply) ?? $reply;
        $reply = preg_replace("/\R{2,}/u", "\n", $reply) ?? $reply;
        $reply = preg_replace('/[ \t]{2,}/u', ' ', $reply) ?? $reply;
        $reply = trim($reply);

        $sentences = preg_split('/(?<=[।!?])\s+/u', $reply, -1, PREG_SPLIT_NO_EMPTY);
        if (is_array($sentences) && count($sentences) > 5) {
            $reply = implode(' ', array_slice($sentences, 0, 5));
        }

        if ($reply === '') {
            $reply = 'দুঃখিত, এই মুহূর্তে উত্তর তৈরি করা যাচ্ছে না। অনুগ্রহ করে আবার চেষ্টা করুন।';
        }

        return response()->json(['reply' => $reply]);
    }
}
