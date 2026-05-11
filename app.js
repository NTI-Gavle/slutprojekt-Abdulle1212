document.addEventListener('DOMContentLoaded', function () {
    const navbar = document.querySelector('.navbar');
    const scrollTop = document.getElementById('scrollTop');
    const themeInputs = document.querySelectorAll('input[name="theme"]');

    function syncScrollUi() {
        if (navbar) {
            if (window.scrollY > 24) {
                navbar.classList.add('navbar-scrolled');
            } else {
                navbar.classList.remove('navbar-scrolled');
            }
        }

        if (scrollTop) {
            if (window.scrollY > 280) {
                scrollTop.classList.add('visible');
            } else {
                scrollTop.classList.remove('visible');
            }
        }
    }

    function applyTheme(theme) {
        document.documentElement.setAttribute('data-theme', theme);
        localStorage.setItem('kvitter-theme', theme);
    }

    window.addEventListener('scroll', syncScrollUi);
    syncScrollUi();

    if (scrollTop) {
        scrollTop.addEventListener('click', function () {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }

    if (themeInputs.length > 0) {
        const activeTheme = localStorage.getItem('kvitter-theme') || 'light';

        themeInputs.forEach(function (input) {
            input.checked = input.value === activeTheme;

            input.addEventListener('change', function () {
                if (input.checked) {
                    applyTheme(input.value);
                }
            });
        });
    }
});
