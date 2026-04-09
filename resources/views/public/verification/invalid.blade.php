<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>রক্তদূত — অবৈধ QR কোড</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;500;600;700&family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg:     #070d1a;
            --card:   #111827;
            --border: rgba(255,255,255,.07);
            --red:    #dc2626;
            --muted:  #4b5563;
        }

        html, body { min-height: 100%; background: var(--bg); }

        body {
            font-family: 'Inter', 'Hind Siliguri', system-ui, sans-serif;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 1.5rem 1rem 2rem;
            position: relative;
            overflow-x: hidden;
        }

        /* Ambient background glow */
        body::before {
            content: '';
            position: fixed;
            top: -20vw; left: -10vw;
            width: 60vw; height: 60vw;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(220,38,38,.07) 0%, transparent 65%);
            pointer-events: none;
        }

        /* ── Card ── */
        .card {
            width: 100%;
            max-width: 22rem;
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 1.75rem;
            overflow: hidden;
            box-shadow:
                0 0 0 1px rgba(220,38,38,.1),
                0 28px 56px rgba(0,0,0,.7);
            text-align: center;
            animation: card-rise .5s cubic-bezier(.16,1,.3,1) both;
        }
        @keyframes card-rise {
            from { opacity:0; transform:translateY(24px) scale(.97); }
            to   { opacity:1; transform:translateY(0) scale(1); }
        }

        /* ── Header Band ── */
        .card-header {
            position: relative;
            background: linear-gradient(150deg, #1a0505 0%, #2d0808 50%, #1c0a0a 100%);
            border-bottom: 1px solid rgba(220,38,38,.18);
            padding: 2rem 1.5rem 1.75rem;
            overflow: hidden;
        }
        .card-header::before {
            content: '';
            position: absolute; inset: 0;
            background-image:
                linear-gradient(rgba(220,38,38,.04) 1px, transparent 1px),
                linear-gradient(90deg, rgba(220,38,38,.04) 1px, transparent 1px);
            background-size: 20px 20px;
            pointer-events: none;
        }

        /* Brand row */
        .brand-row {
            position: relative; z-index: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: .5rem;
            margin-bottom: 1.5rem;
        }
        .brand-dot {
            width: 1.9rem; height: 1.9rem;
            background: rgba(220,38,38,.18);
            border: 1.5px solid rgba(220,38,38,.3);
            border-radius: .55rem;
            display: grid; place-items: center;
            font-size: .95rem;
        }
        .brand-name {
            font-size: .72rem;
            font-weight: 700;
            color: rgba(248,113,113,.75);
            letter-spacing: .07em;
            text-transform: uppercase;
        }

        /* Warning icon */
        .icon-wrap {
            position: relative; z-index: 1;
            width: 4.5rem; height: 4.5rem;
            margin: 0 auto 1rem;
            background: rgba(220,38,38,.12);
            border: 2px solid rgba(220,38,38,.28);
            border-radius: 50%;
            display: grid; place-items: center;
            animation: shake-entry .6s .25s cubic-bezier(.36,.07,.19,.97) both;
        }
        @keyframes shake-entry {
            0%   { transform: scale(.4) rotate(-8deg); opacity: 0; }
            60%  { transform: scale(1.08) rotate(3deg); opacity: 1; }
            80%  { transform: scale(.96) rotate(-2deg); }
            100% { transform: scale(1) rotate(0); opacity: 1; }
        }
        .icon-wrap svg { width: 2.25rem; height: 2.25rem; color: #f87171; }

        .header-title {
            position: relative; z-index: 1;
            font-size: 1.1rem;
            font-weight: 800;
            color: #fca5a5;
            letter-spacing: -.02em;
        }

        /* ── Body ── */
        .card-body {
            padding: 1.5rem;
        }

        .error-message {
            font-size: .9rem;
            color: #6b7280;
            line-height: 1.7;
            font-weight: 500;
            margin-bottom: 1.5rem;
        }

        /* Possible reasons list */
        .reasons-box {
            background: rgba(255,255,255,.03);
            border: 1px solid rgba(255,255,255,.07);
            border-radius: 1rem;
            padding: 1rem 1.1rem;
            text-align: left;
            margin-bottom: 1.5rem;
        }
        .reasons-box p {
            font-size: .7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .09em;
            color: #374151;
            margin-bottom: .6rem;
        }
        .reasons-box ul {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: .4rem;
        }
        .reasons-box ul li {
            font-size: .78rem;
            color: #4b5563;
            font-weight: 500;
            display: flex;
            align-items: flex-start;
            gap: .5rem;
        }
        .reasons-box ul li::before {
            content: '›';
            color: #374151;
            font-weight: 800;
            flex-shrink: 0;
        }

        /* Back button */
        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: .5rem;
            padding: .7rem 1.5rem;
            background: var(--red);
            color: #fff;
            font-size: .85rem;
            font-weight: 700;
            border-radius: 999px;
            text-decoration: none;
            transition: background .2s, transform .15s;
            box-shadow: 0 4px 16px rgba(220,38,38,.3);
        }
        .back-btn:hover {
            background: #b91c1c;
            transform: translateY(-1px);
        }
        .back-btn svg { width: .95rem; height: .95rem; }

        /* ── Footer ── */
        .card-footer {
            padding: .85rem 1.5rem;
            border-top: 1px solid rgba(255,255,255,.05);
            background: rgba(0,0,0,.2);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: .35rem;
            font-size: .68rem;
            color: #1f2937;
            font-weight: 600;
            letter-spacing: .04em;
        }
        .card-footer svg { width: .75rem; height: .75rem; color: #374151; }

        /* Privacy note */
        .privacy-note {
            margin-top: 1.25rem;
            width: 100%;
            max-width: 22rem;
            text-align: center;
            font-size: .67rem;
            color: #111827;
            font-weight: 500;
        }
    </style>
</head>
<body>

<div class="card" role="alert" aria-live="assertive" aria-label="অবৈধ QR কোড">

    {{-- ══ HEADER ══ --}}
    <div class="card-header">

        <div class="brand-row">
            <div class="brand-dot" aria-hidden="true">🩸</div>
            <span class="brand-name">রক্তদূত &middot; RoktoDut</span>
        </div>

        <div class="icon-wrap" aria-hidden="true">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                      d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667
                         1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464
                         0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
        </div>

        <p class="header-title">QR কোড অবৈধ</p>
    </div>

    {{-- ══ BODY ══ --}}
    <div class="card-body">

        <p class="error-message">
            {{ $message ?? 'এই QR কোডটি বৈধ নয় বা মেয়াদোত্তীর্ণ হয়ে গেছে।' }}
        </p>

        <div class="reasons-box">
            <p>সম্ভাব্য কারণসমূহ</p>
            <ul>
                <li>ডোনারের পরিচয় এখনো যাচাই হয়নি (NID Pending)</li>
                <li>QR কোডটি পুরনো বা পরিবর্তিত হয়েছে</li>
                <li>লিঙ্কটি আংশিক বা ভুলভাবে স্ক্যান হয়েছে</li>
                <li>অ্যাকাউন্টটি বর্তমানে সাসপেন্ড করা আছে</li>
            </ul>
        </div>

        <a href="{{ url('/') }}" class="back-btn">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                      d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            হোমপেজে ফিরুন
        </a>
    </div>

    {{-- ══ FOOTER ══ --}}
    <div class="card-footer">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0
                     00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
        </svg>
        roktodut.com &middot; Secure Verification System
    </div>

</div>

<p class="privacy-note">
    🔒 নিরাপত্তার কারণে বিস্তারিত তথ্য প্রদর্শন করা হয়নি।
</p>

</body>
</html>
