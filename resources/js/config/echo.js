import { ENV } from "./bridge.js";
import Echo from 'laravel-echo';

import Pusher from 'pusher-js';
window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: ENV.VITE_REVERB_APP_KEY,
    wsHost: ENV.VITE_REVERB_HOST,
    wsPort: ENV.VITE_REVERB_PORT ?? 80,
    wssPort: ENV.VITE_REVERB_PORT ?? 443,
    forceTLS: (ENV.VITE_REVERB_SCHEME ?? 'https') === 'https',
    enabledTransports: ['ws', 'wss'],
});
