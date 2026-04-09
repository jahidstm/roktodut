<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>রক্তদূত — {{ __('verification.verified_donor_profile') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;500;600;700&family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --red:        #dc2626;
            --red-700:    #b91c1c;
            --red-800:    #991b1b;
            --red-900:    #7f1d1d;
            --green:      #22c55e;
            --amber:      #f59e0b;
            --bg:         #070d1a;
            --card:       #111827;
            --card-inner: #1a2336;
            --border:     rgba(255,255,255,.07);
            --muted:      #64748b;
            --text-light: #f1f5f9;
        }

        html, body {
            min-height: 100%;
            background: var(--bg);
        }

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

        /* ── Background ambient glow ── */
        body::before, body::after {
            content: '';
            position: fixed;
            border-radius: 50%;
            pointer-events: none;
        }
        body::before {
            top: -15vw; left: -10vw;
            width: 55vw; height: 55vw;
            background: radial-gradient(circle, rgba(220,38,38,.11) 0%, transparent 65%);
        }
        body::after {
            bottom: -15vw; right: -10vw;
            width: 45vw; height: 45vw;
            background: radial-gradient(circle, rgba(127,29,29,.09) 0%, transparent 65%);
        }

        /* ── Scanning line animation (top of page) ── */
        .scan-bar {
            position: fixed;
            top: 0; left: 0; right: 0;
            height: 2px;
            background: linear-gradient(to right, transparent 0%, #dc2626 50%, transparent 100%);
            animation: scan-sweep 3s ease-in-out infinite;
            z-index: 100;
        }
        @keyframes scan-sweep {
            0%   { transform: translateX(-100%); opacity: 0; }
            20%  { opacity: 1; }
            80%  { opacity: 1; }
            100% { transform: translateX(100%); opacity: 0; }
        }

        /* ── Card ── */
        .card {
            position: relative;
            width: 100%;
            max-width: 26rem;
            border-radius: 1.75rem;
            overflow: hidden;
            background: var(--card);
            border: 1px solid var(--border);
            box-shadow:
                0 0 0 1px rgba(220,38,38,.12),
                0 32px 64px rgba(0,0,0,.7),
                0 0 80px rgba(220,38,38,.07);
            animation: card-rise .55s cubic-bezier(.16,1,.3,1) both;
        }
        @keyframes card-rise {
            from { opacity:0; transform: translateY(28px) scale(.97); }
            to   { opacity:1; transform: translateY(0) scale(1); }
        }

        /* ── Header ── */
        .card-header {
            position: relative;
            background: linear-gradient(150deg, var(--red-900) 0%, var(--red-800) 40%, var(--red) 100%);
            padding: 1.75rem 1.5rem 3rem;
            text-align: center;
            overflow: hidden;
        }
        /* Grid pattern overlay */
        .card-header::before {
            content: '';
            position: absolute; inset: 0;
            background-image:
                linear-gradient(rgba(255,255,255,.04) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,.04) 1px, transparent 1px);
            background-size: 20px 20px;
            pointer-events: none;
        }
        /* Curved bottom overlap */
        .card-header::after {
            content: '';
            position: absolute;
            bottom: -1px; left: 0; right: 0;
            height: 2.25rem;
            background: var(--card);
            border-radius: 1.75rem 1.75rem 0 0;
        }

        .brand-row {
            position: relative; z-index: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: .5rem;
            margin-bottom: 1.25rem;
        }
        .brand-dot {
            width: 2rem; height: 2rem;
            background: rgba(255,255,255,.18);
            border: 1.5px solid rgba(255,255,255,.3);
            border-radius: .6rem;
            display: grid; place-items: center;
            font-size: 1rem;
        }
        .brand-name {
            font-size: .75rem;
            font-weight: 700;
            color: rgba(255,255,255,.88);
            letter-spacing: .07em;
            text-transform: uppercase;
        }

        /* Shield icon with pop animation */
        .shield-wrap {
            position: relative; z-index: 1;
            width: 4rem; height: 4rem;
            margin: 0 auto .8rem;
            background: rgba(255,255,255,.12);
            border: 2px solid rgba(255,255,255,.25);
            border-radius: 50%;
            display: grid; place-items: center;
            animation: pop-in .6s .3s cubic-bezier(.34,1.56,.64,1) both;
        }
        @keyframes pop-in {
            from { opacity:0; transform: scale(.45) rotate(-10deg); }
            to   { opacity:1; transform: scale(1) rotate(0deg); }
        }
        .shield-wrap svg { width: 2rem; height: 2rem; color: #fff; }

        .header-subtitle {
            position: relative; z-index: 1;
            font-size: .82rem;
            font-weight: 600;
            color: rgba(255,255,255,.75);
            letter-spacing: .02em;
        }

        /* ── Body ── */
        .card-body {
            padding: .5rem 1.5rem 1.5rem;
        }

        /* Donor Name */
        .donor-name {
            font-size: 1.65rem;
            font-weight: 800;
            color: var(--text-light);
            letter-spacing: -.035em;
            text-align: center;
            line-height: 1.2;
            margin-bottom: 1.25rem;
        }

        /* ── Blood Group Hero Block ── */
        .blood-hero {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1rem;
            background: linear-gradient(135deg, rgba(220,38,38,.15) 0%, rgba(127,29,29,.18) 100%);
            border: 1.5px solid rgba(220,38,38,.32);
            border-radius: 1.1rem;
            padding: 1.1rem 1.5rem;
            margin-bottom: 1.25rem;
        }
        .blood-left .blood-label {
            font-size: .65rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .1em;
            color: rgba(248,113,113,.65);
            margin-bottom: .2rem;
        }
        .blood-left .blood-value {
            font-size: 3rem;
            font-weight: 900;
            color: #fca5a5;
            letter-spacing: -.05em;
            line-height: 1;
        }
        .blood-drop {
            font-size: 2.5rem;
            animation: float 3s ease-in-out infinite;
        }
        @keyframes float {
            0%,100% { transform: translateY(0); }
            50%      { transform: translateY(-6px); }
        }

        /* ── Status Row ── */
        .status-row {
            display: flex;
            justify-content: center;
            margin-bottom: 1.35rem;
        }
        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: .55rem;
            padding: .6rem 1.35rem;
            border-radius: 999px;
            font-size: .88rem;
            font-weight: 700;
            letter-spacing: .01em;
        }
        .status-pill.available {
            background: rgba(34,197,94,.1);
            border: 1.5px solid rgba(34,197,94,.28);
            color: #4ade80;
        }
        .status-pill.cooldown {
            background: rgba(245,158,11,.09);
            border: 1.5px solid rgba(245,158,11,.25);
            color: #fbbf24;
        }
        .pulse-dot {
            width: .55rem; height: .55rem;
            border-radius: 50%;
            background: currentColor;
        }
        .pulse-dot.live { animation: dot-pulse 1.6s ease-in-out infinite; }
        @keyframes dot-pulse {
            0%,100% { opacity:1; transform:scale(1); }
            50%      { opacity:.3; transform:scale(.6); }
        }

        /* ── Divider ── */
        .divider {
            border: none;
            border-top: 1px solid rgba(255,255,255,.07);
            margin: 1.25rem 0;
        }

        /* ── Badge Section ── */
        .section-label {
            font-size: .66rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .1em;
            color: var(--muted);
            margin-bottom: .65rem;
        }
        .badges-wrap {
            display: flex;
            flex-wrap: wrap;
            gap: .45rem;
        }

        /* NID Verified chip (always shown on this page) */
        .chip-nid {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            font-size: .74rem;
            font-weight: 700;
            padding: .35rem .85rem;
            border-radius: 999px;
            background: rgba(34,197,94,.1);
            border: 1px solid rgba(34,197,94,.25);
            color: #4ade80;
        }
        .chip-nid svg { width: .9rem; height: .9rem; }

        /* Generic badge pill */
        .chip-badge {
            display: inline-flex;
            align-items: center;
            gap: .3rem;
            font-size: .73rem;
            font-weight: 600;
            padding: .3rem .75rem;
            border-radius: 999px;
            background: rgba(255,255,255,.05);
            border: 1px solid rgba(255,255,255,.09);
            color: #cbd5e1;
        }

        /* ── Footer ── */
        .card-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: .75rem;
            padding: 1rem 1.5rem;
            border-top: 1px solid rgba(255,255,255,.06);
            background: rgba(0,0,0,.22);
        }
        .footer-seal {
            display: flex;
            align-items: center;
            gap: .4rem;
            font-size: .72rem;
            font-weight: 700;
            color: #4ade80;
        }
        .footer-seal svg { width: .95rem; height: .95rem; }
        .footer-domain {
            font-size: .68rem;
            font-weight: 600;
            color: #374151;
            letter-spacing: .04em;
        }

        /* ── Privacy Note (below card) ── */
        .privacy-note {
            margin-top: 1.35rem;
            width: 100%;
            max-width: 26rem;
            text-align: center;
            font-size: .68rem;
            color: #1e293b;
            font-weight: 500;
            line-height: 1.6;
        }

        /* ── Responsive tweak ── */
        @media (max-width: 360px) {
            .blood-left .blood-value { font-size: 2.5rem; }
            .donor-name { font-size: 1.4rem; }
        }
    </style>
</head>
<body>

{{-- Scanning sweep line at top of page --}}
<div class="scan-bar" aria-hidden="true"></div>

<div class="card" role="main" aria-label="রক্তদূত ডোনার স্মার্ট কার্ড">

    {{-- ══ HEADER ══ --}}
    <div class="card-header">

        {{-- Brand --}}
        <div class="brand-row">
            <div class="brand-dot" aria-hidden="true">🩸</div>
            <span class="brand-name">রক্তদূত &middot; RoktoDut</span>
        </div>

        {{-- Shield icon --}}
        <div class="shield-wrap" aria-hidden="true">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                      d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0
                         0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02
                         12.02 0 003 9c0 5.591 3.824 10.29 9 11.622
                         5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
            </svg>
        </div>

        <p class="header-subtitle">{{ __('verification.verified_donor_profile') }}</p>
    </div>

    {{-- ══ BODY ══ --}}
    <div class="card-body">

        {{-- Donor Name --}}
        <p class="donor-name" aria-label="ডোনারের নাম">{{ $name }}</p>

        {{-- Blood Group Hero --}}
        <div class="blood-hero" role="img" aria-label="{{ __('verification.blood_group') }}: {{ $blood_group }}">
            <div class="blood-left">
                <p class="blood-label">{{ __('verification.blood_group') }}</p>
                <p class="blood-value">{{ $blood_group }}</p>
            </div>
            <span class="blood-drop" aria-hidden="true">🩸</span>
        </div>

        {{-- Availability Status --}}
        {{-- Label: Current Status --}}
        <p class="section-label" style="text-align:center; margin-bottom:.6rem;">{{ __('verification.current_status') }}</p>
        <div class="status-row" role="status" aria-live="polite">
            @if($availability === 'available')
                <div class="status-pill available">
                    <span class="pulse-dot live" aria-hidden="true"></span>
                    {{ __('verification.available_for_donation') }}
                </div>
            @else
                <div class="status-pill cooldown">
                    <span class="pulse-dot" aria-hidden="true"></span>
                    {{ __('verification.in_cooldown_period') }}
                </div>
            @endif
        </div>

        <hr class="divider">

        {{-- Badges Section --}}
        <div>
            <p class="section-label">{{ __('verification.identity_verified_by_system') }}</p>
            <div class="badges-wrap" role="list">

                {{-- NID Verified — always shown (controller only renders this view for verified users) --}}
                <span class="chip-nid" role="listitem"
                      title="{{ __('verification.identity_verified_by_system') }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                    </svg>
                    {{ __('verification.identity_verified_by_system') }}
                </span>

                {{-- Gamification / milestone badges --}}
                @foreach ($badges as $badge)
                    <span class="chip-badge" role="listitem"
                          title="{{ $badge['bn'] ?? ($badge['label'] ?? '') }}">
                        {{ $badge['emoji'] ?? '' }}
                        {{ $badge['bn'] ?? ($badge['label'] ?? '') }}
                    </span>
                @endforeach

                @if($badges->isEmpty())
                    <span class="chip-badge" role="listitem">🩸 নিবন্ধিত ডোনার</span>
                @endif
            </div>
        </div>

    </div>{{-- /.card-body --}}

    {{-- ══ FOOTER ══ --}}
    <div class="card-footer">
        <div class="footer-seal" aria-label="{{ __('verification.identity_verified_by_system') }}">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                      d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0
                         0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02
                         12.02 0 003 9c0 5.591 3.824 10.29 9 11.622
                         5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
            </svg>
            {{ __('verification.identity_verified_by_system') }}
        </div>
        <span class="footer-domain">roktodut.com</span>
    </div>

</div>{{-- /.card --}}

{{-- Privacy note below card --}}
<p class="privacy-note">
    🔒 {{ __('verification.privacy_protected') }}
</p>

</body>
</html>
