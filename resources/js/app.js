import './bootstrap';
import './theme.js';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

//Theme
document.addEventListener("DOMContentLoaded", () => {
    const theme = localStorage.getItem('theme');
    try { setTheme(theme) } catch {}
    document.getElementById('theme_controller').checked = theme === "dark";
});
