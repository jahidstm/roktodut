<div align="center">
  <h1>🩸 RoktoDut (রক্তদূত)</h1>
  <p><strong>A privacy-first, automation-driven platform that connects patients with verified blood donors in minutes.</strong></p>
  <p>
    <img src="https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel" />
    <img src="https://img.shields.io/badge/Tailwind_CSS-3.x-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white" alt="Tailwind CSS" />
    <img src="https://img.shields.io/badge/Alpine.js-3.x-8BC0D0?style=for-the-badge&logo=alpine.js&logoColor=white" alt="Alpine JS" />
    <img src="https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL" />
    <img src="https://img.shields.io/badge/Leaflet-1.9-199900?style=for-the-badge&logo=leaflet&logoColor=white" alt="Leaflet" />
  </p>
</div>

---

## 🚀 Overview
RoktoDut is a full-stack, production-grade blood donor discovery system built for Bangladesh. It prioritizes **speed in emergencies**, **trust in identities**, and **privacy in contact sharing** through automation, verification, and real-time analytics.

## ✨ Why it stands out
- **No-login emergency search** with smart ranking to surface reliable donors first.
- **Privacy shield** that hides phone numbers until a human verification step passes.
- **Truth Loop verification** to confirm donations without manual follow-up.
- **Live demand heatmap** across all districts for supply-vs-demand visibility.
- **QR Smart Card verification** for NID-verified donors with controlled disclosure.

## 🧩 Core Features
- **Donor discovery & smart ranking**: availability, verification tier, and reliability scoring.
- **Emergency request flow**: public blood requests with response tracking and urgency sorting.
- **Donation verification**: claim → recipient confirmation → automated cooldown.
- **Gamification**: points, badges, and national leaderboards to drive retention.
- **Organization panel**: hospitals/blood clubs can verify and manage members.
- **Content platform**: health blogs and verified success stories.
- **PWA + FCM**: offline fallback and push notifications for critical alerts.
- **Real-time & automation**: queues, scheduled jobs, and alerts via Telegram.

## 🏗️ Architecture
| Layer | Tech |
|---|---|
| Backend | Laravel 12, PHP 8.2, MySQL, Redis, Sanctum, Reverb |
| Frontend | Blade, Tailwind CSS, Alpine.js, Vite |
| Maps & Analytics | Leaflet, GeoJSON, custom spatial analytics |
| Notifications | Firebase (FCM), Telegram Bot |
| ML Service | FastAPI + scikit-learn (donor ranking) + Groq (NLP request parsing) |

## 🔐 Privacy & Safety by Design
- Phone numbers are **masked by default** and only revealed after a challenge + rate limit.
- **QR tokens are opaque** and cannot be enumerated from user IDs.
- **NID data retention** is time-bound and automatically purged.
- **Audit trails and shadow-ban** keep suspicious activity contained.

---

## ⚙️ Local Setup (Laravel App)
**Prerequisites:** PHP 8.2+, Composer, Node.js 18+, MySQL 8+, Redis

1. Install dependencies:
   - `composer install`
   - `npm install`
2. Configure environment:
   - `copy .env.example .env`
   - Update DB, Redis, Firebase, and OAuth settings in `.env`
3. Bootstrap app:
   - `php artisan key:generate`
   - `php artisan migrate`
4. Build assets:
   - `npm run build`

**Run locally:**
- `composer run dev` (Laravel server + queue + logs + Vite)

## 🧠 Local Setup (ML Service)
**Prerequisites:** Python 3.10+

1. Install dependencies:
   - `pip install -r roktodut-ml-service\requirements.txt`
2. Run API:
   - `uvicorn roktodut-ml-service.main:app --host 127.0.0.1 --port 8001`

**Required env:** `ROKTODUT_API_KEY` (or default `ROKTODUT_AI_SECRET`), `GROQ_API_KEY` for NLP parsing.

---

## 🧪 Useful Commands
| Task | Command |
|---|---|
| Run dev stack | `composer run dev` |
| Build assets | `npm run build` |
| Run tests | `composer run test` |
| Ops check | `composer run ops-check` |
| Smoke check | `composer run smoke-check` |

---

## 👥 Team
| Name | Role / Contribution | GitHub |
|---|---|---|
| **Jahid Hasan** | Lead Backend, Database Architecture, Security | [@jahidstm](https://github.com/jahidstm) |
| **Md. Alif Khan** | Frontend Refactoring, API Integration, UI Components | [@3alif](https://github.com/3alif) |
| **Nohzat Tabassum** | UI/UX, OAuth Integration, System Documentation | [@NohzatTabassum](https://github.com/NohzatTabassum) |
| **Mst. Moumita Rahman Meem** | Database Seeders, Localization, Demo Data | [@Meem-1137](https://github.com/Meem-1137) |

<div align="center">
  <sub>Built with ❤️ for humanity. Every drop counts.</sub>
</div>
