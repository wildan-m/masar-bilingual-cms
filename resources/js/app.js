// Mobile navigation toggle.
document.addEventListener('click', (event) => {
    const toggle = event.target.closest('[data-menu-toggle]');

    if (!toggle) {
        return;
    }

    const menu = document.getElementById(toggle.getAttribute('aria-controls'));
    const expanded = toggle.getAttribute('aria-expanded') === 'true';

    toggle.setAttribute('aria-expanded', String(!expanded));
    menu?.classList.toggle('hidden', expanded);
});

// Show the chosen file name next to the custom attachment button.
document.addEventListener('change', (event) => {
    const input = event.target;

    if (!(input instanceof HTMLInputElement) || input.type !== 'file' || !input.dataset.fileLabel) {
        return;
    }

    const label = document.getElementById(input.dataset.fileLabel);

    if (label) {
        label.textContent = input.files?.[0]?.name ?? label.dataset.empty ?? '';
    }
});
