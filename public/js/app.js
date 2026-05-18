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

    const flash = document.querySelector('.flash');
    if (flash) {
        const dismiss = () => {
            flash.classList.add('flash-hide');
            setTimeout(() => flash.remove(), 300);
        };
        flash.addEventListener('click', dismiss);
        setTimeout(dismiss, 4000);
    }
});
