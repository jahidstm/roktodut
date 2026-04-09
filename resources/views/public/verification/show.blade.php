<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>রক্তদূত — ডোনার স্মার্ট কার্ড যাচাই</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --red:      #dc2626;
            --red-dark: #991b1b;
            --card-bg:  #fff;
            --bg:       #f9fafb;
            --text:     #111827;
            --muted:    #6b7280;
            --border:   #e5e7eb;
            --green:    #16a34a;
            --amber:    #d97706;
        }

        body {
            font-family: 'Inter', 'Hind Siliguri', sans-serif;
            background: var(--bg);
            min-height: 100dvh;
            display: grid;
            place-items: center;
            padding: 1.5rem;
        }

        .card {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 1.25rem;
            width: 100%;
            max-width: 26rem;
            overflow: hidden;
            box-shadow: 0 4px 24px rgba(0,0,0,.08);
        }

        /* ── Header Band ── */
        .card-header {
            background: linear-gradient(135deg, var(--red-dark) 0%, var(--red) 100%);
            padding: 1.5rem 1.5rem 1rem;
            color: #fff;
            display: flex;
            align-items: center;
            gap: .75rem;
        }
        .card-header .logo-circle {
            width: 2.75rem; height: 2.75rem;
            background: rgba(255,255,255,.18);
            border-radius: 50%;
            display: grid; place-items: center;
            font-size: 1.35rem;
            flex-shrink: 0;
        }
        .card-header h1 { font-size: 1.1rem; font-weight: 700; letter-spacing: -.01em; }
        .card-header small { font-size: .72rem; opacity: .8; }

        /* ── Body ── */
        .card-body { padding: 1.5rem; }

        .donor-name {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text);
            letter-spacing: -.02em;
            margin-bottom: .25rem;
        }

        /* Blood Group Chip */
        .blood-chip {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            background: #fef2f2;
            border: 1.5px solid #fca5a5;
            color: var(--red);
            font-size: .95rem;
            font-weight: 700;
            padding: .3rem .85rem;
            border-radius: 999px;
            margin: .75rem 0;
        }

        /* Location */
        .location-line {
            font-size: .82rem;
            color: var(--muted);
            margin-bottom: 1.25rem;
            display: flex;
            align-items: center;
            gap: .3rem;
        }

        /* ── Divider ── */
        hr { border: none; border-top: 1px solid var(--border); margin: 1rem 0; }

        /* ── Availability Pill ── */
        .availability-pill {
            display: inline-flex;
            align-items: center;
            gap: .5rem;
            padding: .55rem 1.1rem;
            border-radius: 999px;
            font-size: .88rem;
            font-weight: 600;
            margin-bottom: 1.25rem;
        }
        .availability-pill.available {
            background: #dcfce7;
            color: var(--green);
            border: 1.5px solid #86efac;
        }
        .availability-pill.cooldown {
            background: #fef9c3;
            color: var(--amber);
            border: 1.5px solid #fde68a;
        }
        .availability-pill .dot {
            width: .55rem; height: .55rem;
            border-radius: 50%;
            background: currentColor;
            animation: pulse-dot 1.6s ease-in-out infinite;
        }
        @keyframes pulse-dot {
            0%, 100% { opacity: 1; transform: scale(1); }
            50%       { opacity: .4; transform: scale(.7); }
        }

        /* ── Badges ── */
        .badges-label {
            font-size: .75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .06em;
            color: var(--muted);
            margin-bottom: .6rem;
        }
        .badges-grid {
            display: flex;
            flex-wrap: wrap;
            gap: .5rem;
        }
        .badge-pill {
            display: inline-flex;
            align-items: center;
            gap: .3rem;
            font-size: .78rem;
            font-weight: 600;
            padding: .28rem .7rem;
            border-radius: 999px;
            border: 1.5px solid transparent;
        }

        /* ── Footer ── */
        .card-footer {
            background: #f9fafb;
            border-top: 1px solid var(--border);
            padding: 1rem 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: .72rem;
            color: var(--muted);
        }
        .card-footer .verified-seal {
            display: flex;
            align-items: center;
            gap: .3rem;
            color: var(--green);
            font-weight: 600;
        }
    </style>
</head>
<body>

<div class="card" role="main" aria-label="ডোনার স্মার্ট কার্ড">

    {{-- ── Header ── --}}
    <div class="card-header">
        <div class="logo-circle" role="img" aria-label="রক্তদূত লোগো">🩸</div>
        <div>
            <h1>রক্তদূত ডোনার কার্ড</h1>
            <small>RoktoDut Smart Verification</small>
        </div>
    </div>

    {{-- ── Body ── --}}
    <div class="card-body">

        {{-- নাম --}}
        <p class="donor-name">{{ $name }}</p>

        {{-- রক্তের গ্রুপ --}}
        <span class="blood-chip" aria-label="রক্তের গ্রুপ">
            🩸 {{ $blood_group }}
        </span>

        {{-- জেলা --}}
        @if ($district)
            <p class="location-line">
                <span aria-hidden="true">📍</span>
                {{ $district }}
            </p>
        @endif

        <hr>

        {{-- প্রাপ্যতা --}}
        <div
            class="availability-pill {{ $availability }}"
            role="status"
            aria-live="polite"
            aria-label="ডোনেশনের জন্য প্রাপ্যতার অবস্থা"
        >
            <span class="dot" aria-hidden="true"></span>
            @if ($availability === 'available')
                রক্তদানে প্রস্তুত (Available)
            @else
                বিরতিতে আছেন (In Cooldown)
            @endif
        </div>

        {{-- ব্যাজ --}}
        @if ($badges->isNotEmpty())
            <p class="badges-label">অর্জিত ব্যাজ</p>
            <div class="badges-grid" role="list" aria-label="অর্জিত ব্যাজসমূহ">
                @foreach ($badges as $badge)
                    <span
                        class="badge-pill {{ $badge['color'] }}"
                        role="listitem"
                        title="{{ $badge['bn'] }}"
                    >
                        {{ $badge['emoji'] }} {{ $badge['label'] }}
                    </span>
                @endforeach
            </div>
        @endif
    </div>

    {{-- ── Footer ── --}}
    <div class="card-footer">
        <span class="verified-seal" aria-label="NID দ্বারা যাচাইকৃত">
            ✅ NID Verified
        </span>
        <span>roktodut.com</span>
    </div>

</div>

</body>
</html>
