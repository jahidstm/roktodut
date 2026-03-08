import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

import { initSearchPage } from './pages/search';document.addEventListener('DOMContentLoaded', () => {
    const isSearchPage = !!document.getElementById('division') && !!document.getElementById('district');
    if (isSearchPage) {
        initSearchPage();
    }
});