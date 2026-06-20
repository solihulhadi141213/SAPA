$(function () {
    const bodyEl = document.body;
    const darkModeButton = document.getElementById('toggleDarkMode');
    const backToTopButton = document.getElementById('backToTop');
    const darkModeStorageKey = 'sapa-dark-mode';

    function safeGetStorage(key) {
        try {
            return localStorage.getItem(key);
        } catch (error) {
            return null;
        }
    }

    function safeSetStorage(key, value) {
        try {
            localStorage.setItem(key, value);
        } catch (error) {
            // Ignore storage failures in restricted browsers.
        }
    }

    function updateDarkModeButton(enabled) {
        if (!darkModeButton) {
            return;
        }

        darkModeButton.setAttribute('aria-pressed', enabled ? 'true' : 'false');
        darkModeButton.setAttribute(
            'aria-label',
            enabled ? 'Matikan mode gelap' : 'Aktifkan mode gelap'
        );
        darkModeButton.title = enabled ? 'Matikan mode gelap' : 'Aktifkan mode gelap';
        darkModeButton.innerHTML = enabled
            ? '<i class="bi bi-sun"></i>'
            : '<i class="bi bi-moon-stars"></i>';
    }

    function setDarkMode(enabled) {
        bodyEl.classList.toggle('dark-mode', enabled);
        updateDarkModeButton(enabled);
        safeSetStorage(darkModeStorageKey, enabled ? '1' : '0');
    }

    function toggleDarkMode() {
        setDarkMode(!bodyEl.classList.contains('dark-mode'));
    }

    function scrollToTop() {
        // Use instant scroll to avoid Firefox performance issues on long pages.
        window.scrollTo(0, 0);
    }

    const offcanvasEl = document.getElementById('adminNavbar');
    if (offcanvasEl) {
        offcanvasEl.addEventListener('show.bs.offcanvas', function () {
            document.body.classList.add('nav-open');
        });

        offcanvasEl.addEventListener('hidden.bs.offcanvas', function () {
            document.body.classList.remove('nav-open');
        });
    }

    $('.admin-offcanvas .nav-link').on('click', function () {
        const target = $(this).attr('data-bs-toggle');
        if (target === 'dropdown') {
            return;
        }

        if ($(window).width() < 992 && offcanvasEl) {
            const offcanvas = bootstrap.Offcanvas.getOrCreateInstance(offcanvasEl);
            offcanvas.hide();
        }
    });

    const savedDarkMode = safeGetStorage(darkModeStorageKey);
    const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
    setDarkMode(savedDarkMode ? savedDarkMode === '1' : prefersDark);

    if (darkModeButton) {
        darkModeButton.addEventListener('click', toggleDarkMode);
    }

    if (backToTopButton) {
        backToTopButton.addEventListener('click', scrollToTop);
    }
});
