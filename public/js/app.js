document.addEventListener('DOMContentLoaded', () => {
    const dropdowns = document.querySelectorAll('.dropdown');

    dropdowns.forEach(dd => {
        const toggle = dd.querySelector('.dropdown-toggle');
        toggle.addEventListener('click', (e) => {
            e.stopPropagation();
            dd.classList.toggle('open');
        });
    });

    document.addEventListener('click', () => {
        dropdowns.forEach(dd => dd.classList.remove('open'));
    });
});
