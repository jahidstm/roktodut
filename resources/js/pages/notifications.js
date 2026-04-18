/**
 * notifications.js — Real-time Echo subscription for Bell UI
 *
 * Dispatches custom DOM events consumed by the Alpine `notificationBell` component.
 * Also renders a Bangla toast for immediate visual feedback.
 */

// ─── Toast ────────────────────────────────────────────────────────────────────

function showToast(message, title = '🔔 নতুন নোটিফিকেশন') {
    const container = document.getElementById('notif-toast-container');
    if (!container) return;

    const toast = document.createElement('div');
    toast.className = [
        'flex items-start gap-3 bg-white border border-red-200 shadow-2xl',
        'rounded-2xl px-4 py-3.5 max-w-sm w-full',
        'transform transition-all duration-300 translate-y-4 opacity-0',
    ].join(' ');

    toast.innerHTML = `
        <div class="shrink-0 w-9 h-9 rounded-full bg-red-100 flex items-center justify-center text-red-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
            </svg>
        </div>
        <div class="flex-1 min-w-0">
            <p class="text-sm font-black text-slate-900">${title}</p>
            <p class="text-xs text-slate-500 mt-0.5 leading-snug line-clamp-2">${message}</p>
        </div>
        <button onclick="this.parentElement.remove()"
                class="shrink-0 text-slate-300 hover:text-slate-500 transition mt-0.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>`;

    container.appendChild(toast);

    // Animate in
    requestAnimationFrame(() => {
        requestAnimationFrame(() => {
            toast.classList.remove('translate-y-4', 'opacity-0');
        });
    });

    // Auto-dismiss after 7s
    setTimeout(() => {
        toast.classList.add('translate-y-4', 'opacity-0');
        setTimeout(() => toast.remove(), 300);
    }, 7000);
}

// ─── Main Initializer ────────────────────────────────────────────────────────

/**
 * Call this once per authenticated page load.
 * Subscribes to the private user channel and dispatches reactive events.
 */
export function initNotifications(userId) {
    if (!window.Echo) {
        console.warn('[Notification] Echo not available — make sure Reverb is running.');
        return;
    }

    window.Echo
        .private(`user.${userId}`)
        .listen('BloodRequestMatched', (event) => {
            window.dispatchEvent(new CustomEvent('notif:new', {
                detail: {
                    id: `${event.request_id}_${Date.now()}`,
                    message: event.message ?? 'নতুন রক্তের অনুরোধ',
                    blood_group: event.blood_group ?? null,
                    urgency: event.urgency ?? 'normal',
                    url: event.url ?? '#',
                    read_at: null,
                    time_ago: 'এইমাত্র',
                },
            }));

            showToast(event.message ?? 'নতুন রক্তের অনুরোধ এসেছে!', '🩸 নতুন রক্তের অনুরোধ');
        })
        .notification((event) => {
            window.dispatchEvent(new CustomEvent('notif:new', {
                detail: {
                    id: event.id ?? `notif_${Date.now()}`,
                    message: event.message ?? 'নতুন নোটিফিকেশন',
                    blood_group: event.blood_group ?? null,
                    urgency: event.urgency ?? 'normal',
                    url: event.url ?? '#',
                    read_at: null,
                    time_ago: 'এইমাত্র',
                },
            }));

            showToast(event.message ?? 'নতুন নোটিফিকেশন এসেছে!', event.title ?? '🔔 নতুন নোটিফিকেশন');
        });
}
