<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>রক্তদূত — {{ __('verification.verified_donor_profile') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <meta property="og:title" content="{{ $name }} - ভেরিফাইড রক্তদাতা | রক্তদূত">
    <meta property="og:description" content="রক্তদূতের একজন ভেরিফাইড রক্তদাতা। রক্তের প্রয়োজনে প্রোফাইলটি দেখতে ক্লিক করুন।">
    <meta property="og:image" content="{{ route('public.verify.og', ['token' => $qr_token]) }}">
    <meta property="og:url" content="{{ route('public.verify', ['token' => $qr_token]) }}">
    <meta property="og:type" content="profile">
    <meta name="twitter:card" content="summary_large_image">

    <style>
        *,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
        :root{
            --bg:#020617;
            --bg-soft:#0b1120;
            --card:#0f172a;
            --line:#1e293b;
            --text:#e2e8f0;
            --muted:#94a3b8;
            --accent:#f43f5e;
            --accent-2:#fb7185;
            --emerald:#4ade80;
            --emerald-bg:rgba(16,185,129,.15);
            --amber:#fbbf24;
            --amber-bg:rgba(245,158,11,.15);
        }
        html,body{
            min-height:100%;
            background:
                radial-gradient(52% 45% at 12% -10%, rgba(244,63,94,.25) 0%, transparent 100%),
                radial-gradient(46% 40% at 100% 100%, rgba(59,130,246,.18) 0%, transparent 100%),
                linear-gradient(180deg,#020617 0%,#030712 100%);
        }
        body{
            font-family:'Inter','Hind Siliguri',system-ui,sans-serif;
            display:flex;
            flex-direction:column;
            align-items:center;
            justify-content:center;
            padding:1.25rem 1rem 2rem;
            color:var(--text);
        }
        .card{
            width:100%;
            max-width:27rem;
            background:linear-gradient(180deg,#0b1224 0%, #070d1e 100%);
            border:1px solid rgba(251,113,133,.22);
            border-radius:1.55rem;
            overflow:hidden;
            box-shadow:0 24px 60px rgba(2,6,23,.65), 0 0 0 1px rgba(148,163,184,.07) inset;
            animation:rise .45s ease-out both, cardGlow 5s ease-in-out infinite;
        }
        @keyframes rise{from{opacity:0;transform:translateY(16px) scale(.98)}to{opacity:1;transform:translateY(0) scale(1)}}
        @keyframes cardGlow{0%,100%{box-shadow:0 24px 60px rgba(2,6,23,.65),0 0 0 1px rgba(148,163,184,.07) inset}50%{box-shadow:0 28px 68px rgba(2,6,23,.7),0 0 0 1px rgba(251,113,133,.15) inset}}

        .header{
            position:relative;
            background:linear-gradient(138deg,#991b1b 0%, #dc2626 48%, #e11d48 100%);
            border-bottom:1px solid rgba(251,113,133,.35);
            padding:1.5rem 1.25rem 2.4rem;
            text-align:center;
        }
        .header::before{
            content:'';
            position:absolute;
            inset:0;
            pointer-events:none;
            background-image:
                linear-gradient(rgba(255,255,255,.10) 1px, transparent 1px),
                linear-gradient(90deg,rgba(255,255,255,.10) 1px, transparent 1px);
            background-size:20px 20px;
            animation:gridFlow 12s linear infinite;
        }
        @keyframes gridFlow{from{background-position:0 0,0 0}to{background-position:0 120px,120px 0}}
        .header > *{position:relative;z-index:1}
        .brand{display:flex;justify-content:center;align-items:center;gap:.5rem;margin-bottom:1.05rem}
        .dot{
            width:2rem;height:2rem;border-radius:.62rem;display:grid;place-items:center;
            background:rgba(255,255,255,.18);border:1px solid rgba(255,255,255,.28);
            box-shadow:0 8px 20px rgba(153,27,27,.2);
        }
        .brand-name{font-size:.75rem;font-weight:800;letter-spacing:.04em;color:#fff;text-transform:uppercase}
        .shield{
            width:4.05rem;height:4.05rem;margin:0 auto .8rem;border-radius:999px;display:grid;place-items:center;
            background:rgba(255,255,255,.13);border:2px solid rgba(255,255,255,.28);
            box-shadow:0 0 0 6px rgba(255,255,255,.08);
            animation:shieldFloat 2.8s ease-in-out infinite;
        }
        @keyframes shieldFloat{0%,100%{transform:translateY(0)}50%{transform:translateY(-4px)}}
        .shield svg{width:1.95rem;height:1.95rem;color:#fff}
        .subtitle{font-size:.84rem;font-weight:800;color:#ffe4e6;text-shadow:0 2px 9px rgba(190,24,93,.35);animation:subtitleFade .8s ease-out both .1s}
        @keyframes subtitleFade{from{opacity:0;transform:translateY(4px)}to{opacity:1;transform:translateY(0)}}

        .body{
            margin-top:-1.05rem;
            padding:1.25rem;
            background:linear-gradient(180deg,#0f172a 0%, #0b142b 100%);
            border-radius:1.35rem 1.35rem 0 0;
            position:relative;
            z-index:2;
        }
        .name{
            text-align:center;
            font-size:2rem;
            font-weight:900;
            line-height:1.12;
            letter-spacing:-.03em;
            color:#f8fafc;
            margin-bottom:1rem;
            text-shadow:0 3px 18px rgba(15,23,42,.55);
        }
        .hero{
            display:flex;
            align-items:center;
            justify-content:center;
            gap:1.2rem;
            background:linear-gradient(130deg,rgba(244,63,94,.16),rgba(30,41,59,.78));
            border:1px solid rgba(251,113,133,.45);
            border-radius:1rem;
            padding:1rem 1.1rem;
            margin-bottom:1rem;
            box-shadow:0 12px 30px rgba(244,63,94,.12) inset;
            position:relative;
            overflow:hidden;
        }
        .hero::before{
            content:'';
            position:absolute;
            inset:-150% auto -150% -35%;
            width:30%;
            transform:rotate(18deg);
            background:linear-gradient(180deg,transparent 0%,rgba(255,255,255,.2) 50%,transparent 100%);
            animation:heroShine 4.2s ease-in-out infinite;
            pointer-events:none;
        }
        @keyframes heroShine{0%,100%{left:-35%;opacity:0}30%{opacity:.55}60%{left:120%;opacity:0}}
        .hero-label{
            font-size:.66rem;
            font-weight:800;
            letter-spacing:.1em;
            text-transform:uppercase;
            color:#fda4af;
            margin-bottom:.2rem;
        }
        .hero-value{
            font-size:3rem;
            font-weight:900;
            line-height:1;
            color:#ffe4e6;
            letter-spacing:-.05em;
            animation:valuePulse 2.5s ease-in-out infinite;
        }
        @keyframes valuePulse{0%,100%{transform:scale(1)}50%{transform:scale(1.02)}}
        .drop{font-size:2.25rem;filter:drop-shadow(0 8px 14px rgba(244,63,94,.35));animation:dropFloat 2.1s ease-in-out infinite}
        @keyframes dropFloat{0%,100%{transform:translateY(0)}50%{transform:translateY(-5px)}}

        .section{
            text-align:center;
            font-size:.69rem;
            font-weight:800;
            letter-spacing:.09em;
            text-transform:uppercase;
            color:var(--muted);
            margin-bottom:.65rem;
        }
        .status-row{display:flex;justify-content:center;margin-bottom:1rem}
        .status{
            display:inline-flex;
            align-items:center;
            gap:.5rem;
            border-radius:999px;
            font-size:.84rem;
            font-weight:800;
            padding:.56rem 1.08rem;
            border:1px solid;
            backdrop-filter:blur(4px);
        }
        .status.available{
            color:var(--emerald);
            background:var(--emerald-bg);
            border-color:rgba(74,222,128,.55);
        }
        .status.cooldown{
            color:var(--amber);
            background:var(--amber-bg);
            border-color:rgba(251,191,36,.5);
        }
        .dot-live{
            width:.55rem;
            height:.55rem;
            border-radius:999px;
            background:currentColor;
            box-shadow:0 0 0 5px color-mix(in srgb, currentColor 22%, transparent);
        }
        .dot-live.live{animation:pulse 1.5s ease-in-out infinite}
        @keyframes pulse{0%,100%{transform:scale(1);opacity:1}50%{transform:scale(.62);opacity:.38}}

        hr{border:0;border-top:1px solid var(--line);margin:1rem 0 .9rem}
        .verify-label{
            font-size:.68rem;
            font-weight:800;
            letter-spacing:.09em;
            text-transform:uppercase;
            color:var(--muted);
            margin-bottom:.62rem;
        }
        .badges{display:flex;flex-wrap:wrap;gap:.45rem}
        .chip{
            display:inline-flex;
            align-items:center;
            gap:.35rem;
            border-radius:999px;
            padding:.36rem .76rem;
            font-size:.74rem;
            font-weight:700;
            background:rgba(30,41,59,.72);
            border:1px solid rgba(148,163,184,.24);
            color:#cbd5e1;
            animation:chipIn .45s ease-out both;
        }
        .badges .chip:nth-child(2){animation-delay:.05s}
        .badges .chip:nth-child(3){animation-delay:.1s}
        .badges .chip:nth-child(4){animation-delay:.15s}
        .badges .chip:nth-child(5){animation-delay:.2s}
        @keyframes chipIn{from{opacity:0;transform:translateY(6px)}to{opacity:1;transform:translateY(0)}}
        .chip.nid{
            background:rgba(16,185,129,.16);
            border-color:rgba(74,222,128,.42);
            color:#86efac;
        }
        .chip.nid svg{width:.88rem;height:.88rem}

        .footer{
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap:.8rem;
            padding:.88rem 1.25rem;
            border-top:1px solid rgba(148,163,184,.2);
            background:rgba(3,7,18,.72);
        }
        .seal{
            display:inline-flex;
            align-items:center;
            gap:.4rem;
            font-size:.73rem;
            font-weight:800;
            color:#86efac;
        }
        .seal svg{width:.9rem;height:.9rem}
        .domain{font-size:.67rem;font-weight:700;letter-spacing:.03em;color:#64748b}

        .flash{margin-top:.85rem;font-size:.75rem;font-weight:800;text-align:center}
        .flash.success{color:var(--emerald)}
        .flash.error{color:#fda4af}

        .report-btn{
            margin-top:1rem;
            border:1px solid rgba(251,113,133,.4);
            background:linear-gradient(180deg,rgba(190,24,93,.28),rgba(127,29,29,.25));
            color:#ffe4e6;
            border-radius:.82rem;
            font-weight:800;
            padding:.58rem 1.02rem;
            font-size:.76rem;
            cursor:pointer;
            transition:all .15s;
            box-shadow:0 10px 20px rgba(244,63,94,.15);
        }
        .report-btn:hover{
            transform:translateY(-1px);
            background:linear-gradient(180deg,rgba(225,29,72,.34),rgba(153,27,27,.32));
        }
        .privacy{
            margin-top:1rem;
            width:100%;
            max-width:27rem;
            text-align:center;
            font-size:.69rem;
            color:#94a3b8;
            font-weight:600;
        }

        .report-modal{
            border:1px solid rgba(148,163,184,.35);
            border-radius:1rem;
            width:min(92vw,30rem);
            padding:0;
            background:#0f172a;
            color:#e2e8f0;
            box-shadow:0 20px 50px rgba(2,6,23,.6);
        }
        .report-modal::backdrop{background:rgba(2,6,23,.62)}
        .report-field{
            width:100%;
            border-radius:.65rem;
            border:1px solid #334155;
            background:#0b1224;
            color:#e2e8f0;
            padding:.55rem .7rem;
            font-size:.82rem;
        }
        .report-field:focus{
            outline:none;
            border-color:#fb7185;
            box-shadow:0 0 0 3px rgba(251,113,133,.24);
        }

        @media (max-width:360px){
            .name{font-size:1.6rem}
            .hero-value{font-size:2.45rem}
        }
        @media (prefers-reduced-motion: reduce){
            .card,.header::before,.shield,.subtitle,.hero::before,.hero-value,.drop,.dot-live.live,.chip{animation:none !important}
            .report-btn{transition:none}
        }
    </style>
</head>
<body>
    <div class="card" role="main" aria-label="রক্তদূত ডোনার স্মার্ট কার্ড">
        <div class="header">
            <div class="brand">
                <div class="dot" aria-hidden="true">🩸</div>
                <span class="brand-name">রক্তদূত &middot; RoktoDut</span>
            </div>
            <div class="shield" aria-hidden="true">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
            </div>
            <p class="subtitle">{{ __('verification.verified_donor_profile') }}</p>
        </div>

        <div class="body">
            <p class="name">{{ $name }}</p>

            <div class="hero" role="img" aria-label="{{ __('verification.blood_group') }}: {{ $blood_group }}">
                <div>
                    <p class="hero-label">{{ __('verification.blood_group') }}</p>
                    <p class="hero-value">{{ $blood_group }}</p>
                </div>
                <span class="drop" aria-hidden="true">🩸</span>
            </div>

            <p class="section">{{ __('verification.current_status') }}</p>
            <div class="status-row" role="status" aria-live="polite">
                @if($availability === 'available')
                    <div class="status available"><span class="dot-live live"></span>{{ __('verification.available_for_donation') }}</div>
                @else
                    <div class="status cooldown"><span class="dot-live"></span>{{ __('verification.in_cooldown_period') }}</div>
                @endif
            </div>

            <hr>

            <p class="verify-label">{{ __('verification.identity_verified_by_system') }}</p>
            <div class="badges" role="list">
                <span class="chip nid" role="listitem" title="{{ __('verification.identity_verified_by_system') }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                    </svg>
                    {{ __('verification.identity_verified_by_system') }}
                </span>

                @foreach ($badges as $badge)
                    <span class="chip" role="listitem" title="{{ $badge['bn'] ?? ($badge['label'] ?? '') }}">
                        {{ $badge['emoji'] ?? '' }}
                        {{ $badge['bn'] ?? ($badge['label'] ?? '') }}
                    </span>
                @endforeach

                @if($badges->isEmpty())
                    <span class="chip" role="listitem">🩸 নিবন্ধিত ডোনার</span>
                @endif
            </div>
        </div>

        <div class="footer">
            <div class="seal">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
                {{ __('verification.identity_verified_by_system') }}
            </div>
            <span class="domain">roktodut.com</span>
        </div>
    </div>

    @if(session('success'))
        <p class="flash success">{{ session('success') }}</p>
    @endif
    @if(session('error'))
        <p class="flash error">{{ session('error') }}</p>
    @endif

    <button id="open-report-modal" type="button" class="report-btn">Report this donor</button>
    <p class="privacy">🔒 {{ __('verification.privacy_protected') }}</p>

    <dialog id="report-modal" class="report-modal">
        <form method="POST" action="{{ route('reports.store') }}" style="padding:1rem 1rem 1.1rem;">
            @csrf
            <input type="hidden" name="reportable_type" value="user">
            <input type="hidden" name="reportable_id" value="{{ $user_id }}">
            <h3 style="font-size:.95rem;font-weight:800;margin-bottom:.65rem;">ডোনার রিপোর্ট করুন</h3>
            <label style="display:block;font-size:.74rem;font-weight:700;margin-bottom:.4rem;">কারণ</label>
            <select name="category" class="report-field" required>
                <option value="">কারণ নির্বাচন করুন</option>
                <option value="fake_info">Fake info</option>
                <option value="harassment">Harassment</option>
                <option value="spam">Spam</option>
                <option value="inappropriate">Inappropriate</option>
                <option value="other">Other</option>
            </select>

            <label style="display:block;font-size:.74rem;font-weight:700;margin:.7rem 0 .4rem;">বিস্তারিত (ঐচ্ছিক)</label>
            <textarea name="message" rows="4" class="report-field"></textarea>

            <div style="display:flex;justify-content:flex-end;gap:.45rem;margin-top:.8rem;">
                <button type="button" id="close-report-modal" class="report-field" style="width:auto;cursor:pointer;font-weight:700;">Cancel</button>
                <button type="submit" class="report-field" style="width:auto;cursor:pointer;font-weight:800;background:#e11d48;color:#fff;border-color:#e11d48;">Submit</button>
            </div>
        </form>
    </dialog>

    <script>
        const reportModal = document.getElementById('report-modal');
        document.getElementById('open-report-modal')?.addEventListener('click', () => reportModal?.showModal());
        document.getElementById('close-report-modal')?.addEventListener('click', () => reportModal?.close());
    </script>
</body>
</html>
