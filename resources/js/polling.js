const HEARTBEAT_ENDPOINT = '/live/heartbeat';
const NOTIFICATIONS_POLL_ENDPOINT = '/notifications/poll';
const DASHBOARD_STATS_ENDPOINT = '/admin/dashboard/stats';
const MAINTENANCE_POLL_ENDPOINT = '/admin/maintenance/latest';
const COMMUNITY_POLL_ENDPOINT = '/admin/community/latest';

// Polling intervals (in milliseconds)
const NOTIFICATIONS_POLL_INTERVAL = 8000;
const DASHBOARD_STATS_POLL_INTERVAL = 15000;
const MAINTENANCE_POLL_INTERVAL = 10000;
const COMMUNITY_POLL_INTERVAL = 20000;
const HEARTBEAT_POLL_INTERVAL = 30000;

const isManagerDashboard = window.location.pathname === '/dashboard';
const LIVE_PANEL_PATHS = new Set([
    '/dashboard',
    '/tickets',
    '/bookings',
    '/announcements',
    '/community',
    '/concerns',
    '/admin/bookings/calendar',
    '/admin/community',
    '/admin/concerns',
    '/admin/users',
]);
const isLivePanel = LIVE_PANEL_PATHS.has(window.location.pathname)
    || window.location.pathname.startsWith('/tickets')
    || window.location.pathname.startsWith('/bookings')
    || window.location.pathname.startsWith('/announcements')
    || window.location.pathname.startsWith('/community')
    || window.location.pathname.startsWith('/concerns')
    || window.location.pathname.startsWith('/admin/')
    || window.location.pathname.startsWith('/staff/');

let lastHeartbeatTimestamp = null;
let lastNotificationTimestamp = null;
let lastDashboardStatsTimestamp = null;
let lastMaintenanceTimestamp = null;
let lastCommunityTimestamp = null;
let statusIndicator = null;
const PAGE_LOAD_TIME = Date.now();

const pageRecentlyLoaded = () => Date.now() - PAGE_LOAD_TIME < 4000;

const hasSubmittingForm = () => {
    return !!document.querySelector('form[data-submitting="true"]');
};

const hasActiveToast = () => {
    return !!document.querySelector('[data-toast]');
};

const updateConnectionStatus = (state) => {
    if (!statusIndicator) {
        return;
    }

    const states = {
        connected:  { label: 'Live',        color: '#5A8A5A', cls: 'is-connected'  },
        connecting: { label: 'Syncing...',   color: '#BE9360', cls: 'is-connecting' },
        fallback:   { label: 'Updating...',  color: '#BE9360', cls: 'is-fallback'   },
    };
    const { label, color, cls } = states[state] ?? states.fallback;

    statusIndicator.textContent = label;
    statusIndicator.style.setProperty('--live-status-color', color);
    statusIndicator.classList.remove('is-connected', 'is-connecting', 'is-fallback');
    statusIndicator.classList.add(cls);
};

// NOTE: The bottom-left "Live" indicator (class: live-update-status) was removed.
// Keep the underlying polling behavior for heartbeat/reload, but do not mount the UI element.
const mountConnectionStatus = () => {
    return;
};


const parseHeartbeatTimestamp = (value) => {
    if (!value) {
        return null;
    }

    const parsed = Date.parse(value);
    return Number.isNaN(parsed) ? null : parsed;
};

const tryReloadIfUpdated = async () => {
    try {
        const response = await window.axios.get(HEARTBEAT_ENDPOINT);
        const updatedAt = response?.data?.updated_at;
        const currentTimestamp = parseHeartbeatTimestamp(updatedAt);

        if (!currentTimestamp) {
            return;
        }

        if (!lastHeartbeatTimestamp) {
            lastHeartbeatTimestamp = currentTimestamp;
            return;
        }

        if (currentTimestamp !== lastHeartbeatTimestamp) {
            lastHeartbeatTimestamp = currentTimestamp;
            if (pageRecentlyLoaded() || hasSubmittingForm() || hasActiveToast()) {
                return;
            }
            window.location.reload();
        }
    } catch (error) {
        // The page-level polling fallbacks keep dashboards fresh when this check misses.
    }
};

const pollNotifications = async () => {
    try {
        const response = await window.axios.get(NOTIFICATIONS_POLL_ENDPOINT);
        const data = response.data;

        // Update notification bell count
        const bellCount = document.querySelector('[data-notification-count]');
        if (bellCount) {
            bellCount.textContent = data.unread_count;
            bellCount.style.display = data.unread_count > 0 ? 'inline' : 'none';
        }

        lastNotificationTimestamp = Date.now();
    } catch (error) {
        console.error('Failed to poll notifications:', error);
    }
};

const pollDashboardStats = async () => {
    try {
        if (!isManagerDashboard) {
            return;
        }

        const response = await window.axios.get(DASHBOARD_STATS_ENDPOINT);
        const data = response.data;

        // Update dashboard metrics using existing function
        if (typeof window.refreshManagerDashboardMetrics === 'function') {
            window.refreshManagerDashboardMetrics();
        }

        lastDashboardStatsTimestamp = Date.now();
    } catch (error) {
        console.error('Failed to poll dashboard stats:', error);
    }
};

const pollMaintenance = async () => {
    try {
        if (!isLivePanel) {
            return;
        }

        const response = await window.axios.get(MAINTENANCE_POLL_ENDPOINT);
        const data = response.data;

        // Maintenance polling keeps heartbeat updated for page reload
        lastMaintenanceTimestamp = Date.now();
    } catch (error) {
        console.error('Failed to poll maintenance:', error);
    }
};

const pollCommunity = async () => {
    try {
        if (!isLivePanel) {
            return;
        }

        const response = await window.axios.get(COMMUNITY_POLL_ENDPOINT);
        const data = response.data;

        // Community polling keeps heartbeat updated for page reload
        lastCommunityTimestamp = Date.now();
    } catch (error) {
        console.error('Failed to poll community:', error);
    }
};

const startPolling = () => {
    if (!isLivePanel) {
        return;
    }

    // Start heartbeat polling
    tryReloadIfUpdated();
    setInterval(tryReloadIfUpdated, HEARTBEAT_POLL_INTERVAL);

    // Start notifications polling
    pollNotifications();
    setInterval(pollNotifications, NOTIFICATIONS_POLL_INTERVAL);

    // Start dashboard stats polling (only on manager dashboard)
    if (isManagerDashboard) {
        pollDashboardStats();
        setInterval(pollDashboardStats, DASHBOARD_STATS_POLL_INTERVAL);
    }

    // Start maintenance polling
    pollMaintenance();
    setInterval(pollMaintenance, MAINTENANCE_POLL_INTERVAL);

    // Start community polling
    pollCommunity();
    setInterval(pollCommunity, COMMUNITY_POLL_INTERVAL);

    updateConnectionStatus('connected');
};

document.addEventListener('DOMContentLoaded', () => {
    mountConnectionStatus();
    startPolling();
});

