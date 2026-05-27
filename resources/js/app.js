import './bootstrap';
import './config/theme.js';
import { initSoundUI } from './config/sound-ui.js';

import Alpine from 'alpinejs';

window.Alpine = Alpine;
Alpine.start();

// Theme
window.addEventListener("pageshow", () => {
    const theme = localStorage.getItem('theme');
    try { setTheme(theme) } catch {}
    document.getElementById('theme_controller').checked = theme === "dark";
});

document.addEventListener('DOMContentLoaded', initSoundUI);
