<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>রক্তদূত — অবৈধ QR কোড</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Inter', 'Hind Siliguri', sans-serif;
            background: #f9fafb;
            min-height: 100dvh;
            display: grid;
            place-items: center;
            padding: 1.5rem;
        }
        .card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 1.25rem;
            width: 100%;
            max-width: 22rem;
            padding: 2.5rem 2rem;
            text-align: center;
            box-shadow: 0 4px 24px rgba(0,0,0,.07);
        }
        .icon { font-size: 3.5rem; margin-bottom: 1rem; }
        h1 { font-size: 1.15rem; font-weight: 700; color: #111827; margin-bottom: .5rem; }
        p  { font-size: .875rem; color: #6b7280; line-height: 1.6; }
        .back-link {
            display: inline-block;
            margin-top: 1.5rem;
            padding: .55rem 1.25rem;
            background: #dc2626;
            color: #fff;
            border-radius: 999px;
            font-size: .85rem;
            font-weight: 600;
            text-decoration: none;
            transition: background .2s;
        }
        .back-link:hover { background: #b91c1c; }
    </style>
</head>
<body>
    <div class="card" role="alert" aria-live="assertive">
        <div class="icon" aria-hidden="true">⚠️</div>
        <h1>QR কোড অবৈধ</h1>
        <p>{{ $message ?? 'এই QR কোডটি বৈধ নয় বা মেয়াদোত্তীর্ণ।' }}</p>
        <a href="{{ url('/') }}" class="back-link">হোমপেজে ফিরুন</a>
    </div>
</body>
</html>
