function csrfToken(fallback = '') {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? fallback;
}

async function request(url, options = {}, tokenFallback = '') {
    const res = await fetch(url, {
        credentials: 'same-origin',
        ...options,
        headers: {
            'X-CSRF-TOKEN': csrfToken(tokenFallback),
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            ...(options.headers || {}),
        },
    });

    const contentType = res.headers.get('content-type') || '';
    let payload = null;

    try {
        payload = contentType.includes('application/json') ? await res.json() : await res.text();
    } catch {
        payload = null;
    }

    return { res, payload };
}

async function post(url, body, tokenFallback = '') {
    const isForm = body instanceof FormData;
    if (isForm && !body.has('_token')) {
        body.append('_token', csrfToken(tokenFallback));
    }
    return request(url, {
        method: 'POST',
        body: isForm ? body : (body ? JSON.stringify(body) : null),
        headers: isForm ? {} : { 'Content-Type': 'application/json' },
    }, tokenFallback);
}

function getErrorMessage(payload, fallback) {
    if (!payload) return fallback;
    if (typeof payload === 'string') return payload || fallback;
    return payload.message || fallback;
}

function toggleButtonLoading(btn, isLoading, loadingText = 'লোড হচ্ছে...', defaultText = 'নম্বর দেখুন') {
    if (!btn) return;
    const spinner = btn.querySelector('.reveal-spinner');
    const text = btn.querySelector('.reveal-btn-text');
    btn.disabled = isLoading;
    if (spinner) spinner.classList.toggle('hidden', !isLoading);
    if (text && isLoading) text.textContent = loadingText;
    if (text && !isLoading) text.textContent = defaultText;
}

function hideError(card) {
    const err = card.querySelector('.js-inline-error');
    if (!err) return;
    err.textContent = '';
    err.classList.add('hidden');
}

function showError(card, message) {
    const err = card.querySelector('.js-inline-error');
    if (!err) return;
    err.textContent = message || 'সার্ভারে সমস্যা হচ্ছে—আবার চেষ্টা করুন';
    err.classList.remove('hidden');
}

function renderChallenge(card, question) {
    const container = card.querySelector('.js-reveal-container');
    if (!container) return;
    const verifyUrl = card.getAttribute('data-reveal-verify-url');

    container.innerHTML = `
        <form class="space-y-2 js-reveal-verify-form" data-url="${verifyUrl}">
            <label class="block rounded-lg border border-amber-200 bg-amber-50 px-3 py-2 text-xs font-bold text-amber-700">
                OTP নিরাপত্তা প্রশ্ন: ${question}
            </label>
            <div class="flex gap-2">
                <input type="number" name="answer" required class="min-w-0 flex-1 rounded-lg border-slate-300 text-sm focus:border-red-500 focus:ring-red-500" placeholder="উত্তর লিখুন">
                <button type="submit" class="inline-flex h-10 items-center justify-center rounded-lg bg-slate-800 px-4 text-sm font-bold text-white transition hover:bg-slate-900 focus:outline-none focus:ring-2 focus:ring-slate-300">
                    Verify
                </button>
            </div>
        </form>
    `;
}

function renderSuccess(card, phone) {
    const phoneText = card.querySelector('.js-phone-text');
    if (phoneText) phoneText.textContent = phone;

    const container = card.querySelector('.js-reveal-container');
    if (!container) return;

    const canRequest = card.getAttribute('data-can-request') === '1';
    const requestUrl = card.getAttribute('data-request-url');
    const requestActionHtml = canRequest && requestUrl
        ? `<a href="${requestUrl}" class="inline-flex h-10 items-center justify-center rounded-lg border border-red-200 bg-red-50 px-4 text-sm font-bold text-red-700 transition hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-red-200">রিকোয়েস্ট করুন</a>`
        : '';

    container.innerHTML = `
        <div class="grid grid-cols-1 gap-2 sm:grid-cols-2 js-success-actions">
            <a href="tel:${phone}" class="inline-flex h-10 items-center justify-center rounded-lg bg-emerald-600 px-4 text-sm font-black text-white transition hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-300">
                কল করুন
            </a>
            ${requestActionHtml}
        </div>
    `;
}

function wireAjaxReveal() {
    document.addEventListener('click', async (e) => {
        const btn = e.target.closest('[data-reveal-start]');
        if (!btn) return;

        e.preventDefault();
        const card = btn.closest('[data-donor-card]');
        if (!card) return;

        hideError(card);
        toggleButtonLoading(btn, true);

        const url = card.getAttribute('data-reveal-start-url');
        const tokenFallback = card.getAttribute('data-csrf-token') || '';
        try {
            const payload = new FormData();
            payload.append('_token', csrfToken(tokenFallback));
            const { res, payload: responsePayload } = await post(url, payload, tokenFallback);
            if (!res.ok || !responsePayload?.ok) {
                throw new Error(getErrorMessage(responsePayload, `Reveal start failed (${res.status})`));
            }
            renderChallenge(card, responsePayload.question || '2 + 2 = ?');
        } catch (err) {
            showError(card, err.message);
            toggleButtonLoading(btn, false);
        }
    });

    document.addEventListener('submit', async (e) => {
        const form = e.target.closest('.js-reveal-verify-form');
        if (!form) return;

        e.preventDefault();
        const card = form.closest('[data-donor-card]');
        if (!card) return;

        hideError(card);
        const submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn) submitBtn.disabled = true;

        try {
            const tokenFallback = card.getAttribute('data-csrf-token') || '';
            const { res, payload } = await post(form.getAttribute('data-url'), new FormData(form), tokenFallback);
            if (!res.ok || !payload?.ok || !payload?.phone) {
                throw new Error(getErrorMessage(payload, `Verify failed (${res.status})`));
            }
            renderSuccess(card, payload.phone);
        } catch (err) {
            showError(card, err.message);
            if (submitBtn) submitBtn.disabled = false;
        }
    });
}

async function wireLocationsDropdowns() {
    const divisionEl = document.getElementById('division') || document.getElementById('filter_division');
    const districtEl = document.getElementById('district') || document.getElementById('filter_district');
    const upazilaEl = document.getElementById('upazila') || document.getElementById('filter_upazila');

    if (!divisionEl || !districtEl || !upazilaEl) return;

    const selectedDivision = document.getElementById('selectedDivision')?.value || '';
    const selectedDistrict = document.getElementById('selectedDistrict')?.value || '';
    const selectedUpazila = document.getElementById('selectedUpazila')?.value || '';

    let data = {};
    try {
        const res = await fetch('/data/bd_locations.json', { cache: 'no-store' });
        if (res.ok) data = await res.json();
    } catch (e) {
        console.error('Failed to load locations JSON', e);
    }

    const divisionsMap = (data && data.divisions && typeof data.divisions === 'object') ? data.divisions : {};
    const hasJsonDivisions = Object.keys(divisionsMap).length > 0;

    function setOptions(el, placeholder, values, selectedValue = '') {
        el.innerHTML = '';
        const ph = document.createElement('option');
        ph.value = '';
        ph.textContent = placeholder;
        el.appendChild(ph);

        values.forEach(v => {
            const opt = document.createElement('option');
            opt.value = v;
            opt.textContent = v;
            if (v === selectedValue) opt.selected = true;
            el.appendChild(opt);
        });
    }

    function populateDivisions() {
        if (!hasJsonDivisions) {
            return;
        }
        setOptions(divisionEl, 'সিলেক্ট করুন', Object.keys(divisionsMap), selectedDivision);
    }

    function populateDistricts() {
        if (!hasJsonDivisions) {
            districtEl.disabled = !divisionEl.value;
            upazilaEl.disabled = true;
            return;
        }
        const districtsMap = divisionsMap?.[divisionEl.value] ?? {};
        const districtPlaceholder = divisionEl.value ? 'জেলা নির্বাচন' : 'প্রথমে বিভাগ নির্বাচন করুন';
        setOptions(districtEl, districtPlaceholder, Object.keys(districtsMap), selectedDistrict);
        districtEl.disabled = !divisionEl.value;
        setOptions(upazilaEl, 'প্রথমে জেলা নির্বাচন করুন', [], selectedUpazila);
        upazilaEl.disabled = !divisionEl.value;
    }

    function populateUpazilas() {
        if (!hasJsonDivisions) return;
        const districtsMap = divisionsMap?.[divisionEl.value] ?? {};
        const upazilas = Array.isArray(districtsMap?.[districtEl.value]) ? districtsMap[districtEl.value] : [];
        setOptions(upazilaEl, 'উপজেলা/থানা নির্বাচন', upazilas, selectedUpazila);
        upazilaEl.disabled = !(divisionEl.value && districtEl.value);
    }

    divisionEl.addEventListener('change', () => {
        const sd = document.getElementById('selectedDistrict');
        const su = document.getElementById('selectedUpazila');
        if (sd) sd.value = '';
        if (su) su.value = '';
        populateDistricts();
        upazilaEl.disabled = true;
    });

    districtEl.addEventListener('change', () => {
        const su = document.getElementById('selectedUpazila');
        if (su) su.value = '';
        populateUpazilas();
    });

    populateDivisions();
    populateDistricts();
    populateUpazilas();
}

function wireSearchFormLoading() {
    const form = document.querySelector('form[action*="/search"]');
    if (!form) return;
    const skeletonTemplate = document.getElementById('donor-loading-skeleton-template');

    form.addEventListener('submit', () => {
        if (!skeletonTemplate) return;
        const parent = form.closest('.max-w-7xl');
        if (!parent) return;
        const wrap = document.createElement('div');
        wrap.id = 'search-loading-skeleton';
        wrap.innerHTML = skeletonTemplate.innerHTML;
        parent.appendChild(wrap);
    });
}

export async function initSearchPage() {
    wireAjaxReveal();
    wireSearchFormLoading();
    await wireLocationsDropdowns();
}
