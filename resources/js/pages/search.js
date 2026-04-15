function csrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';
}

function toggleButtonLoading(btn, isLoading) {
    if (!btn) return;
    const spinner = btn.querySelector('.reveal-spinner');
    const text = btn.querySelector('.reveal-btn-text');
    btn.disabled = isLoading;
    if (spinner) spinner.classList.toggle('hidden', !isLoading);
    if (text && isLoading) text.textContent = 'লোড হচ্ছে...';
    if (text && !isLoading) text.textContent = 'নম্বর দেখুন';
}

function wireRevealStartButtons() {
    document.addEventListener('submit', (e) => {
        const form = e.target.closest('form.js-reveal-form');
        if (!form) return;

        const startBtn = form.querySelector('[data-reveal-start]');
        const isStart = !!startBtn;
        if (!isStart) return;

        // Let the normal form POST continue; only show loading UI.
        toggleButtonLoading(startBtn, true);
    });
}

function wireRevealVerifyForms() {
    document.addEventListener('submit', (e) => {
        const form = e.target.closest('form.js-reveal-form');
        if (!form) return;

        const verifyInput = form.querySelector('input[name="answer"]');
        if (!verifyInput) return;

        const submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn) submitBtn.disabled = true;
    });
}

async function wireLocationsDropdowns() {
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
    wireRevealStartButtons();
    wireRevealVerifyForms();
    wireSearchFormLoading();
    await wireLocationsDropdowns();
}
