import './bootstrap';

import Alpine from 'alpinejs';
import { initSearchPage } from './pages/search';
import { initNotifications } from './pages/notifications';
import { initOrgDashboardTabs } from './pages/org-dashboard-tabs';

window.Alpine = Alpine;

// ─── Alpine Component: notificationBell ──────────────────────────────────────
// Registered BEFORE Alpine.start() so Blade x-data="notificationBell()" works.
Alpine.data('notificationBell', () => ({
    open:          false,
    loading:       false,
    notifications: [],
    unreadCount:   Number(window.__unreadCount) || 0,

    init() {
        // Echo pushes a custom DOM event that we catch here
        window.addEventListener('notif:new', (e) => {
            this.unreadCount++;
            this.notifications.unshift(e.detail);
            if (this.notifications.length > 10) {
                this.notifications.pop();
            }
        });
    },

    async toggle() {
        this.open = !this.open;
        // Lazy-load on first open
        if (this.open && this.notifications.length === 0) {
            await this.fetchRecent();
        }
    },

    async fetchRecent() {
        this.loading = true;
        try {
            const res = await window.axios.get('/notifications/recent');
            this.notifications = res.data.notifications;
            this.unreadCount   = res.data.unread_count;
        } catch (err) {
            console.error('[Notification] Failed to fetch recent:', err);
        } finally {
            this.loading = false;
        }
    },

    async markAllRead() {
        try {
            await window.axios.post('/notifications/read-all', {}, {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? '',
                },
            });
            this.unreadCount  = 0;
            this.notifications = this.notifications.map(n => ({
                ...n,
                read_at: new Date().toISOString(),
            }));
        } catch (err) {
            console.error('[Notification] markAllRead failed:', err);
        }
    },

    // ── Helpers for template binding ────────────────────────────────────────

    bloodGroupColor(group) {
        return group && group.includes('-')
            ? 'bg-blue-100 text-blue-700'
            : 'bg-red-100 text-red-700';
    },

    urgencyText(urgency) {
        const map = { emergency: 'অতি জরুরি', urgent: 'জরুরি', normal: 'সাধারণ' };
        return map[urgency] ?? 'সাধারণ';
    },

    urgencyClass(urgency) {
        const map = {
            emergency: 'bg-red-100 text-red-700 animate-pulse',
            urgent:    'bg-yellow-100 text-yellow-700',
            normal:    'bg-slate-100 text-slate-600',
        };
        return map[urgency] ?? map.normal;
    },
}));

Alpine.start();

// ─── Page-level Init (DOMContentLoaded) ──────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    // Smart Donor Search cascading selects
    const hasLegacyIds = !!document.getElementById('division') && !!document.getElementById('district');
    const hasNewIds    = !!document.getElementById('filter_division') && !!document.getElementById('filter_district');
    if (hasLegacyIds || hasNewIds) {
        initSearchPage();
    }

    // Real-time notifications (auth'd users only)
    if (window.__userId) {
        initNotifications(window.__userId);
    }

    initOrgDashboardTabs();
});
