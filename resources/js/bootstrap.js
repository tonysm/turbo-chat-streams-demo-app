/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

import Echo from 'laravel-echo';

import Pusher from 'pusher-js';
window.Pusher = Pusher;

import { Current } from 'current.js';
window.Current = Current;

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: Current.pusher.key,
    cluster: Current.push.cluster ?? 'mt1',
    wsHost: Current.pusher.host ?? `ws-${Current.push.cluster ?? 'mt1'}.pusher.com`,
    wsPort: Current.pusher.port ?? 80,
    wssPort: Current.pusher.port ?? 443,
    forceTLS: Current.push.scheme === 'https',
    enabledTransports: ['ws', 'wss'],
});
