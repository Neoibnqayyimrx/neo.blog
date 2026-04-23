/**
 * app.js — small sprinkle of interactivity for the blog.
 *
 * Responsibilities:
 *   - Toggle the public navbar on mobile.
 *   - Toggle the admin sidebar on tablet / mobile.
 *   - Auto-dismiss alerts after a few seconds.
 */

(() => {
    'use strict';

    // Public nav toggle
    const navToggle = document.getElementById('siteNavToggle');
    const nav       = document.getElementById('siteNav');
    if (navToggle && nav) {
        navToggle.addEventListener('click', (e) => {
            e.stopPropagation();
            nav.classList.toggle('is-open');
        });
        document.addEventListener('click', (e) => {
            if (!nav.contains(e.target)) nav.classList.remove('is-open');
        });
    }

    // Admin sidebar toggle
    const sidebarToggle = document.getElementById('sidebarToggle');
    const dashboard     = document.getElementById('dashboardShell');
    if (sidebarToggle && dashboard) {
        sidebarToggle.addEventListener('click', (e) => {
            e.stopPropagation();
            dashboard.classList.toggle('is-open');
        });
        document.addEventListener('click', (e) => {
            if (window.innerWidth > 1024) return;
            const clickedInside = dashboard.querySelector('.sidebar')?.contains(e.target);
            if (!clickedInside && !sidebarToggle.contains(e.target)) {
                dashboard.classList.remove('is-open');
            }
        });
        window.addEventListener('resize', () => {
            if (window.innerWidth > 1024) dashboard.classList.remove('is-open');
        });
    }

    // Auto-dismiss alerts after 6s
    document.querySelectorAll('.alert').forEach((alert) => {
        setTimeout(() => {
            alert.style.transition = 'opacity .4s ease, transform .4s ease, margin .4s ease, max-height .4s ease';
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-4px)';
            setTimeout(() => alert.remove(), 400);
        }, 6000);
    });
})();
