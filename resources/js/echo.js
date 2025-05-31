import Echo from 'laravel-echo';
import Pusher from 'pusher-js';
window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: import.meta.env.VITE_REVERB_PORT ?? 80,
    wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
    forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
    enabledTransports: ['ws', 'wss'],
    // Debug mode - disable in production
    enableLogging: import.meta.env.DEV,
    logToConsole: import.meta.env.DEV,
});

// Connection status handling
window.Echo.connector.pusher.connection.bind('connected', function() {
    console.log('🟢 WebSocket Connected');
    // Update UI status
    const statusIndicator = document.getElementById('websocket-status');
    if (statusIndicator) {
        statusIndicator.className = 'websocket-status connected';
        statusIndicator.textContent = 'Connected';
    }
});

window.Echo.connector.pusher.connection.bind('disconnected', function() {
    console.log('🔴 WebSocket Disconnected');
    // Update UI status
    const statusIndicator = document.getElementById('websocket-status');
    if (statusIndicator) {
        statusIndicator.className = 'websocket-status disconnected';
        statusIndicator.textContent = 'Disconnected';
    }
});

window.Echo.connector.pusher.connection.bind('error', function(err) {
    console.error('❌ WebSocket Error:', err);
});

// Export for use in other modules
export default window.Echo;