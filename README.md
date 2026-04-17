🩸 RoktoDut (রক্তদূত)
> **A smart, privacy-first, and automation-driven platform for finding trustworthy blood donors—fast.**
![Laravel](https://img.shields.io/badge/Laravel-11.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-3.x-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
---
📝 Project Overview
RoktoDut is not a conventional blood bank website. It is a data-driven ecosystem designed to reduce response time during emergencies while protecting donor privacy and maintaining trust throughout the donation lifecycle.
Our core goals are:
Enable donor discovery without login/registration during emergencies.
Protect donors from data scraping and harassment with privacy-by-design features.
Ensure the end-to-end donation flow remains accurate, verifiable, and automated.
---
🌟 Key Features
1) User Roles & Access Control (User Ecosystem)
The system supports four primary user roles:
Donor: Manages profile, receives real-time requests, and earns points/badges for verified donations.
Recipient: Searches for donors without login and can create requests to reach donors quickly.
Org Admin: Represents a blood club/hospital and verifies/manages organization members.
System Admin: Oversees NID verification, reports, and overall system governance.
---
2) Smart Search Engine & Emergency Response 🚑
Open Access (No Login Required): Users can search donors by location and blood group without signing in.
Priority-Based Smart Sorting: Search results are ranked by trust and readiness (not random):
Ready Now: Donors who are currently available and ready to donate.
Org Verified: Members verified by a registered blood club/hospital.
NID Verified: Donors with completed NID verification.
Regular: Standard donors without additional verification tiers.
---
3) Privacy Shield & Secure Contact Reveal 🛡️
Phone Privacy: Donor phone numbers are masked by default (e.g., `017******89`).
Math Challenge: Users must solve a simple math challenge to reveal contact information—reducing spam and bot scraping.
Rate Limiting: Contact reveal requests are limited (e.g., max 5 reveals per 15 minutes) to prevent abuse.
---
4) Automation & Freshness Logic (The Brain) 🧠
Welcome Back Check: If a donor logs in after 30+ days, the system prompts them to confirm their current availability.
Auto Cooldown: After a successful donation, donors are automatically marked Unavailable for 4 months.
Silent Approval: If a donation claim is not disputed by the recipient within 24 hours, it is automatically approved as a successful donation.
---
5) Organization Management Panel 🏥
Member Approval: Organization admins can review and verify members.
Verified Tag: Verified organization members receive a visible trust tag in search results.
---
6) Gamification: Leaderboard & Badges 🏆
Dynamic Leaderboards: National and district-based leaderboards ranked by verified donations and/or points.
Points & Badges: Donors earn points and milestone badges for verified donations and platform engagement.
---
7) Trust & Smart Verification ✅
NID Verification: Donors can upload NID; admins verify and grant a verified badge.
QR Smart Card: Donors receive a digital identity card with a QR code.
---
8) Community & Success Stories ❤️
Success Stories: Donors and recipients can share donation experiences and photos.
Health Blog: A dedicated blog for awareness and health education related to blood donation.
---
🛠️ Tech Stack
Backend Framework: Laravel 11 (PHP 8.x)
Frontend: Blade Templates, Tailwind CSS 3, Alpine.js 3
Bundler: Vite 5
Database: MySQL 8.0
---
👥 Team Members
Name	Role / Contribution	GitHub
Jahid Hasan	Lead Backend, Database Architecture, Security	@jahidstm
Md. Alif Khan	Frontend Refactoring, API Integration, UI Components	@3alif
Nohzat Tabassum	UI/UX, OAuth Integration, System Documentation	@NohzatTabassum
Mst. Moumita Rahman Meem	Database Seeders, Localization, Demo Data	@Meem-1137
