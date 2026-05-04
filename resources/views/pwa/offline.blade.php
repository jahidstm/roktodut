<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ইন্টারনেট সংযোগ নেই — রক্তদূত</title>
    <link rel="icon" href="/images/image_14.png" type="image/png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root { font-family: 'Hind Siliguri', system-ui, sans-serif; }
        body {
            min-height: 100dvh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #fff1f2 0%, #fff 50%, #f0f9ff 100%);
            padding: 2rem;
            color: #0f172a;
        }

        .card {
            background: white;
            border-radius: 24px;
            padding: 3rem 2rem;
            max-width: 420px;
            width: 100%;
            text-align: center;
            box-shadow: 0 20px 60px -10px rgba(0,0,0,0.1);
            border: 1px solid #f1f5f9;
        }

        .icon-wrap {
            width: 90px;
            height: 90px;
            background: #fff1f2;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.06); }
        }

        .icon-wrap img {
            width: 52px;
            height: 52px;
            object-fit: contain;
        }

        h1 {
            font-size: 1.5rem;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 0.75rem;
        }

        p {
            font-size: 0.95rem;
            color: #64748b;
            line-height: 1.7;
            margin-bottom: 2rem;
        }

        .status-bar {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 0.75rem 1.25rem;
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            font-size: 0.85rem;
            font-weight: 600;
            color: #dc2626;
        }

        .dot {
            width: 8px;
            height: 8px;
            background: #dc2626;
            border-radius: 50%;
            animation: blink 1.2s ease-in-out infinite;
        }

        @keyframes blink {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.2; }
        }

        .btn {
            display: inline-block;
            background: #dc2626;
            color: white;
            font-weight: 700;
            font-size: 0.9rem;
            padding: 0.85rem 2rem;
            border-radius: 12px;
            border: none;
            cursor: pointer;
            text-decoration: none;
            transition: background 0.2s, transform 0.15s;
            width: 100%;
        }
        .btn:hover { background: #b91c1c; transform: translateY(-1px); }
        .btn:active { transform: scale(0.97); }

        .footer-note {
            margin-top: 2rem;
            font-size: 0.78rem;
            color: #94a3b8;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon-wrap">
            <img src="/images/image_14.png" alt="রক্তদূত">
        </div>

        <h1>ইন্টারনেট সংযোগ নেই</h1>

        <p>আপনি এখন অফলাইনে আছেন। রক্তদূত ব্যবহার করতে ইন্টারনেট সংযোগ প্রয়োজন।</p>

        <div class="status-bar">
            <span class="dot"></span>
            অফলাইন মোড সক্রিয়
        </div>

        <button class="btn" onclick="window.location.reload()">
            পুনরায় চেষ্টা করুন ↻
        </button>

        <p class="footer-note">
            সংযোগ ফিরে এলে পেজটি স্বয়ংক্রিয়ভাবে আবার লোড হবে।
        </p>
    </div>

    <script>
        // ইন্টারনেট ফিরে আসলে auto-reload
        window.addEventListener('online', () => {
            window.location.href = '/';
        });
    </script>
</body>
</html>
