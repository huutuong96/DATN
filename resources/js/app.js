import Echo from 'laravel-echo';
window.Pusher = require('pusher-js');

const echo = new Echo({
    broadcaster: 'pusher',
    key: env('PUSHER_APP_KEY'),
    cluster: 'your-cluster',
    encrypted: false,
    wsHost: window.location.hostname,
    wsPort: 6001,
    forceTLS: false,
    disableStats: true,
});

echo.channel('your-channel')
    .listen('YourEvent', (event) => {
        console.log(event);
    });
