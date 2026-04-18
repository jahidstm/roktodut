function extractContentFromHtml(html) {
    const parser = new DOMParser();
    const doc = parser.parseFromString(html, 'text/html');
    const shell = doc.querySelector('#org-command-shell');

    if (!shell) {
        throw new Error('Org command shell not found in response.');
    }

    return shell.innerHTML;
}

async function loadOrgTab(url, pushState = true) {
    const shell = document.getElementById('org-command-shell');
    if (!shell) return;

    shell.classList.add('opacity-60', 'pointer-events-none');

    try {
        const response = await fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'text/html',
            },
            credentials: 'same-origin',
        });

        if (!response.ok) {
            window.location.href = url;
            return;
        }

        const html = await response.text();
        shell.innerHTML = extractContentFromHtml(html);

        if (pushState) {
            window.history.pushState({ orgTabUrl: url }, '', url);
        }
    } catch (error) {
        console.error('[OrgTabs] Ajax load failed:', error);
        window.location.href = url;
    } finally {
        shell.classList.remove('opacity-60', 'pointer-events-none');
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
}

export function initOrgDashboardTabs() {
    const shell = document.getElementById('org-command-shell');
    if (!shell) return;

    document.addEventListener('click', (event) => {
        const link = event.target.closest('a[data-org-tab], a[data-member-filter]');
        if (!link) return;

        if (link.target && link.target !== '_self') return;

        const href = link.getAttribute('href');
        if (!href || href.startsWith('#')) return;

        event.preventDefault();
        loadOrgTab(href, true);
    });

    window.addEventListener('popstate', (event) => {
        const fallbackUrl = window.location.href;
        const targetUrl = event.state?.orgTabUrl || fallbackUrl;
        loadOrgTab(targetUrl, false);
    });
}
