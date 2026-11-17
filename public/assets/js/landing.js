/**
 * assets/js/landing.js
 * Comportamentos simples da landing page: sombra da navbar ao rolar
 * e fechamento do menu mobile ao clicar em um link.
 */
document.addEventListener('DOMContentLoaded', () => {
    const nav = document.querySelector('.landing-nav');

    if (nav) {
        window.addEventListener('scroll', () => {
            nav.style.boxShadow = window.scrollY > 10
                ? '0 4px 18px rgba(30,41,59,0.10)'
                : '0 2px 12px rgba(30,41,59,0.05)';
        });
    }

    const navLinks = document.querySelectorAll('#landingNav .nav-link');
    const collapseEl = document.getElementById('landingNav');
    navLinks.forEach((link) => {
        link.addEventListener('click', () => {
            if (collapseEl.classList.contains('show')) {
                bootstrap.Collapse.getOrCreateInstance(collapseEl).hide();
            }
        });
    });
});
