<div align="center">
  <h1>🩸 RoktoDut (রক্তদূত)</h1>
  <p><strong>A smart, privacy-first, and automation-driven platform for finding trustworthy blood donors—fast.</strong></p>
  
  <p>
    <img src="https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel" />
    <img src="https://img.shields.io/badge/Tailwind_CSS-3.x-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white" alt="Tailwind CSS" />
    <img src="https://img.shields.io/badge/Alpine.js-3.x-8BC0D0?style=for-the-badge&logo=alpine.js&logoColor=white" alt="Alpine JS" />
    <img src="https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL" />
    <img src="https://img.shields.io/badge/Leaflet-1.9-199900?style=for-the-badge&logo=leaflet&logoColor=white" alt="Leaflet" />
  </p>
</div>

---

## 🚀 Project Overview

**RoktoDut** is not just another conventional blood bank directory. It is a highly optimized, data-driven ecosystem designed to drastically reduce response times during medical emergencies, whilst strongly protecting donor privacy and maintaining absolute trust throughout the donation lifecycle.

### 🎯 Core Objectives:
- Enable **donor discovery without login/registration** during critical emergencies.
- Protect donors from **data scraping, spam, and harassment** through privacy-by-design architecture.
- Automate the end-to-end donation flow with a **Truth Loop Verification System**.
- Provide real-time data insights via **Spatial Heatmaps**.

---

## ✨ Key Innovations & Features

### 1️⃣ Progressive Web App (PWA) Support 📱
RoktoDut behaves natively on mobile devices. Users can install the app directly to their home screens via our seamless **PWA Install Banner**, complete with caching strategies and an app-like navigation experience for offline-ready access.

### 2️⃣ Live Demand Heatmap (Spatial Analytics) 🗺️
A visually stunning, real-time spatial map of Bangladesh built with **Leaflet.js** and GeoJSON. 
- **Public View:** Shows real-time blood demand across all 64 districts. Users can tap on any district to find active donors instantly.
- **Admin Analytics:** Features complex algorithms calculating **Donor Fatigue Index (DFI)** and **Critical Risk Score (CRS)** per district to monitor supply/demand bottlenecks.

### 3️⃣ Privacy Shield & Secure Contact Reveal 🛡️
- **Phone Privacy:** Donor phone numbers are masked by default (e.g., `017******89`).
- **Anti-Scraping Challenge:** Users must solve a randomized math challenge to reveal contact information—thwarting automated bots.
- **Rate Limiting:** IP-based rate limits (max 5 reveals per 15 minutes) prevent malicious abuse.

### 4️⃣ Smart Search Engine & Priority Ranking 🚑
Our intelligent sorting algorithm ensures patients find the most reliable donors first:
1. **Ready Now:** Donors manually toggling their state to "Available".
2. **Org Verified:** Members authenticated by registered hospitals or blood clubs.
3. **NID Verified:** Donors with national identity verification.
4. **Regular:** Standard registered donors.

### 5️⃣ Automation & "The Truth Loop" (The Brain) 🧠
- **Auto Cooldown:** After a successful donation, donors are automatically marked **Unavailable for 4 months (120 days)**.
- **The Truth Loop:** When a donor claims they donated, the recipient is prompted to confirm or dispute. If not disputed within 24 hours, the system silently approves the donation.
- **Welcome Back Prompt:** If a donor logs in after 30+ days of inactivity, the system smartly prompts them to confirm their current availability status.

### 6️⃣ Gamification: Leaderboard & Badges 🏆
To solve "Donor Retention", we introduced a rich gamified experience:
- **Dynamic Leaderboards:** Real-time national ranking based on verified donations.
- **Points & Milestones:** Donors earn points and unlock beautiful visual badges (emoji-based metadata) as they cross donation milestones.

### 7️⃣ Organization Management Panel 🏥
- **Hospitals & Blood Clubs:** Organizations can register and manage their members.
- **Verified Tags:** Members verified by an organization gain a massive trust boost in the global search algorithm.

---

## 🛠️ Technical Architecture & Stack

### Backend
- **Framework:** Laravel 12 (PHP 8.x)
- **Database:** MySQL 8.0 (Optimized indexing for spatial & relational queries)
- **Queue System:** Laravel Horizon/Redis for background jobs (DFI calculation, Cooldown resets)

### Frontend
- **Styling:** Tailwind CSS 3 (Custom utility classes, animations, and responsive UI)
- **Interactivity:** Alpine.js 3 (Lightweight reactive components, Teleport, Off-canvas menus)
- **Mapping:** Leaflet.js (GeoJSON rendering, Interactive Tooltips)
- **Bundler:** Vite 5

---

## 👥 Meet the Team

| Name | Role / Contribution | GitHub |
|---|---|---|
| **Jahid Hasan** | Lead Backend, Database Architecture, Security | [@jahidstm](https://github.com/jahidstm) |
| **Md. Alif Khan** | Frontend Refactoring, API Integration, UI Components | [@3alif](https://github.com/3alif) |
| **Nohzat Tabassum** | UI/UX, OAuth Integration, System Documentation | [@NohzatTabassum](https://github.com/NohzatTabassum) |
| **Mst. Moumita Rahman Meem** | Database Seeders, Localization, Demo Data | [@Meem-1137](https://github.com/Meem-1137) |

<br>
<div align="center">
  <sub>Built with ❤️ for humanity. Every drop counts.</sub>
</div>
