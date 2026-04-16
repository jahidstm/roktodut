import './bootstrap';

import Alpine from 'alpinejs';
import { initSearchPage } from './pages/search';

window.Alpine = Alpine;

Alpine.start();

document.addEventListener('DOMContentLoaded', () => {
    const hasLegacyIds = !!document.getElementById('division') && !!document.getElementById('district');
    const hasNewIds = !!document.getElementById('filter_division') && !!document.getElementById('filter_district');
    const isSearchPage = hasLegacyIds || hasNewIds;

    if (isSearchPage) {
        initSearchPage();
    }
});
