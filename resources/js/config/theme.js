function setTheme(theme) {
    if(typeof theme === "boolean") theme = theme ? 'dark' : 'light';

    const controller = document.querySelector('input.theme-controller');
    const btnLight   = document.getElementById('btn_theme_light');
    const btnDark    = document.getElementById('btn_theme_dark');

    const activeClasses   = ['border-primary', 'bg-primary/10'];
    const inactiveClasses = ['border-base-200', 'bg-base-100'];
    const checkSVG = `<svg width="12" height="12" viewBox="0 0 24 24" fill="none"><path d="M5 13l4 4L19 7" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>`;

    if (theme === 'light') {
        try {
            btnLight.classList.add(...activeClasses);
            btnLight.classList.remove(...inactiveClasses);
            btnLight.querySelector('.rounded-full').innerHTML = checkSVG;
            btnLight.querySelector('.rounded-full').className = 'w-5 h-5 rounded-full bg-primary flex items-center justify-center';

            btnDark.classList.add(...inactiveClasses);
            btnDark.classList.remove(...activeClasses);
            btnDark.querySelector('.rounded-full').innerHTML = '';
            btnDark.querySelector('.rounded-full').className = 'w-5 h-5 rounded-full border-2 border-base-300 flex items-center justify-center';

            if (controller) {
                controller.checked = false;
                controller.dispatchEvent(new Event('change'));
            }
        } catch {}
        localStorage.setItem('Nahida_theme', 'light');
    } else {
        try {
            btnDark.classList.add(...activeClasses);
            btnDark.classList.remove(...inactiveClasses);
            btnDark.querySelector('.rounded-full').innerHTML = checkSVG;
            btnDark.querySelector('.rounded-full').className = 'w-5 h-5 rounded-full bg-primary flex items-center justify-center';

            btnLight.classList.add(...inactiveClasses);
            btnLight.classList.remove(...activeClasses);
            btnLight.querySelector('.rounded-full').innerHTML = '';
            btnLight.querySelector('.rounded-full').className = 'w-5 h-5 rounded-full border-2 border-base-300 flex items-center justify-center';

            if (controller) {
                controller.checked = true;
                controller.dispatchEvent(new Event('change'));
            }
        } catch {}
        localStorage.setItem('Nahida_theme', 'dark');
    }
}

window.setTheme = setTheme;
