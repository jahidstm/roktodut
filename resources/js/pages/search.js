function csrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';
}async function request(url, options = {}) {
    const res = await fetch(url, {
        credentials: 'same-origin',
        ...options,
        headers: {
            'X-CSRF-TOKEN': csrfToken(),
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
}async function post(url, body) {
    const isForm = body instanceof FormData;
    return request(url, {
        method: 'POST',
        body: isForm ? body : (body ? JSON.stringify(body) : null),
        headers: isForm ? {} : { 'Content-Type': 'application/json' },
    });
}function getErrorMessage(payload, fallback) {
    if (!payload) return fallback;
    if (typeof payload === 'string') return payload || fallback;
    return payload.message || fallback;
}async function revealStart(url) {
    const { res, payload } = await post(url);
    if (!res.ok) throw new Error(getErrorMessage(payload, `Reveal start failed (HTTP ${res.status})`));
}async function revealVerify(form) {
    const fd = new FormData(form);
    const { res, payload } = await post(form.action, fd);
    if (!res.ok) throw new Error(getErrorMessage(payload, `Verify failed (HTTP ${res.status})`));
}function wireRevealStartButtons() {
    document.addEventListener('click', async (e) => {
        const btn = e.target.closest('[data-reveal-start]');
        if (!btn) return;

        e.preventDefault();
        e.stopPropagation();

        const url = btn.getAttribute('data-reveal-start');
        if (!url) return;

        try {
            btn.disabled = true;
            await revealStart(url);
            window.location.reload();
        } catch (err) {
            console.error(err);
            alert(err.message);
        } finally {
            btn.disabled = false;
        }
    }, true);
}function wireRevealVerifyForms() {
    document.addEventListener('submit', async (e) => {
        const form = e.target.closest('form[data-reveal-verify]');
        if (!form) return;

        e.preventDefault();
        e.stopPropagation();

        try {
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn) submitBtn.disabled = true;

            await revealVerify(form);
            window.location.reload();
        } catch (err) {
            console.error(err);
            alert(err.message);
        } finally {
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn) submitBtn.disabled = false;
        }
    }, true);
}async function wireLocationsDropdowns() {
    const divisionEl = document.getElementById('division');
    const districtEl = document.getElementById('district');
    const upazilaEl = document.getElementById('upazila');

    if (!divisionEl || !districtEl || !upazilaEl) return;

    const selectedDivision = document.getElementById('selectedDivision')?.value || '';
    const selectedDistrict = document.getElementById('selectedDistrict')?.value || '';
    const selectedUpazila  = document.getElementById('selectedUpazila')?.value || '';

    let data = {};
    try {
        const res = await fetch('/data/bd_locations.json', { cache: 'no-store' });
        if (res.ok) data = await res.json();
    } catch (e) {
        console.error('Failed to load locations JSON', e);
    }

    const divisionsMap = (data && data.divisions && typeof data.divisions === 'object') ? data.divisions : {};

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
        setOptions(divisionEl, 'সিলেক্ট করুন', Object.keys(divisionsMap), selectedDivision);
    }

    function populateDistricts() {
        const districtsMap = divisionsMap?.[divisionEl.value] ?? {};
        setOptions(districtEl, 'সিলেক্ট করুন', Object.keys(districtsMap), selectedDistrict);
        setOptions(upazilaEl, 'সব এলাকা', [], selectedUpazila);
    }

    function populateUpazilas() {
        const districtsMap = divisionsMap?.[divisionEl.value] ?? {};
        const upazilas = Array.isArray(districtsMap?.[districtEl.value]) ? districtsMap[districtEl.value] : [];
        setOptions(upazilaEl, 'সব এলাকা', upazilas, selectedUpazila);
    }

    divisionEl.addEventListener('change', () => {
        const sd = document.getElementById('selectedDistrict');
        const su = document.getElementById('selectedUpazila');
        if (sd) sd.value = '';
        if (su) su.value = '';
        populateDistricts();
    });

    districtEl.addEventListener('change', () => {
        const su = document.getElementById('selectedUpazila');
        if (su) su.value = '';
        populateUpazilas();
    });

    populateDivisions();
    populateDistricts();
    populateUpazilas();
}export async function initSearchPage() {
    wireRevealStartButtons();
    wireRevealVerifyForms();
    await wireLocationsDropdowns();
}