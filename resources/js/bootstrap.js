import 'bootstrap';

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

import axios from 'axios';
import router from './router';
window.axios = axios;


axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.withCredentials = true

axios.interceptors.response.use(response=>{
        return response;
    }, 
    err=> {
    if (err.response) {
        if(err.response.status === 401 || err.response.status === 419){
            const token = localStorage.getItem('x_xsrf_token');
            if(token){
                document.cookie = 'XSRF-TOKEN' + '=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/';
                document.cookie = 'laravel_session' + '=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/';
                localStorage.removeItem('x_xsrf_token');
            }
            router.push({name: 'user.login'});
        }
    }
    if (err.message == "Request aborted"){
    } else {
        return Promise.reject(err);  
    }

});

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

import Echo from 'laravel-echo';

import Pusher from 'pusher-js';
window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    wsHost: import.meta.env.VITE_PUSHER_HOST ?? `ws-${import.meta.env.VITE_PUSHER_APP_CLUSTER}.pusher.com`,
    // wsHost: '127.0.0.1',   // for docker container
    wsPort: import.meta.env.VITE_PUSHER_PORT ?? 80,
    wssPort: import.meta.env.VITE_PUSHER_PORT ?? 443,
    enabledTransports: ['ws', 'wss'],
    cluster:import.meta.env.VITE_PUSHER_APP_CLUSTER,
    forceTLS: false,
    disableStats: true,
});

axios.defaults.withCredentials = true;



