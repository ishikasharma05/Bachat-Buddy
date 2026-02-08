<?php
// Don't start session here - it's already started in the main page files
// Removed session_start() to avoid "session already active" error

// Get user ID from session
$user_id = $_SESSION['user_id'] ?? null;
?>
<div class="header">
    <div class="d-flex align-items-center gap-2">
        <button class="btn d-lg-none border-0 p-0 me-2" onclick="toggleMenu()">
            <i class="bi bi-list fs-2"></i>
        </button>
        <h5 class="mb-0 fw-bold"></h5>
    </div>
    
    <div class="d-flex align-items-center gap-3">
        <!-- Notification Bell -->
        <div style="position: relative; display: inline-block;">
            <button id="notificationBtn" class="notification p-2 rounded-full border-0 notification-bell-btn" style="position: relative; cursor: pointer;">
                <i class="bi bi-bell"></i>
                <span id="notificationBadge" class="notification-badge">0</span>
            </button>
            
            <div id="notificationDropdown" class="notification-dropdown">
                <div class="notification-header">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-bell-fill" style="color: #3b82f6;"></i>
                        <span class="notification-title">Notifications</span>
                    </div>
                    <button onclick="clearNotifications()" class="clear-btn" title="Clear all notifications">
                        <i class="bi bi-trash3"></i>
                    </button>
                </div>
                <div id="notificationList" class="notification-list">
                    <div class="notification-loading">
                        <div class="spinner-border text-primary" role="status" style="width: 2rem; height: 2rem;">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p style="margin-top: 12px; color: #9ca3af;">Loading your alerts...</p>
                    </div>
                </div>
            </div>
        </div>

        <button id="theme-toggle" class="p-2 rounded-full border-0">
            <i class="fas fa-moon"></i>
        </button>

        <button id="logout-btn" class="notification p-2 rounded-full border-0" title="Logout">
            <i class="fas fa-sign-out-alt"></i>
        </button>
    </div>
</div>

<style>
/* ================================
   NOTIFICATION BELL BUTTON
   ================================ */
.notification-bell-btn {
    transition: transform 0.2s ease;
}

.notification-bell-btn:hover {
    transform: scale(1.05);
}

.notification-bell-btn:active {
    transform: scale(0.95);
}

.notification-badge {
    position: absolute;
    top: -4px;
    right: -4px;
    background: #ef4444;
    color: white;
    border-radius: 50%;
    min-width: 20px;
    height: 20px;
    font-size: 11px;
    display: none;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    line-height: 1;
    padding: 0 5px;
    box-shadow: 0 2px 8px rgba(239, 68, 68, 0.4);
}

.notification-badge.show {
    display: flex;
}

/* ================================
   NOTIFICATION DROPDOWN
   ================================ */
.notification-dropdown {
    display: none;
    position: absolute;
    right: 0;
    top: 55px;
    width: 400px;
    max-width: 95vw;
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
    z-index: 9999;
    overflow: hidden;
}

.notification-header {
    padding: 16px 20px;
    background: #f8fafc;
    border-bottom: 1px solid #e5e7eb;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.notification-title {
    font-weight: 700;
    font-size: 16px;
    color: #1f2937;
}

.clear-btn {
    background: none;
    border: none;
    color: #ef4444;
    font-size: 16px;
    cursor: pointer;
    padding: 6px 10px;
    border-radius: 6px;
    transition: all 0.2s;
    font-weight: 500;
}

.clear-btn:hover {
    background: #fee2e2;
}

.notification-list {
    max-height: 450px;
    overflow-y: auto;
    background: white;
}

.notification-loading {
    padding: 40px 20px;
    text-align: center;
}

.notification-empty {
    padding: 50px 30px;
    text-align: center;
}

.notification-empty-icon {
    font-size: 48px;
    margin-bottom: 12px;
    display: block;
    color: #10b981;
}

/* ================================
   NOTIFICATION ITEMS
   ================================ */
.notification-item {
    padding: 16px 20px;
    border-bottom: 1px solid #f3f4f6;
    border-left: 4px solid transparent;
    transition: all 0.2s ease;
}

.notification-item:hover {
    background: #f9fafb;
}

.notification-item:last-child {
    border-bottom: none;
}

.notification-icon {
    font-size: 20px;
    margin-right: 8px;
    display: inline-block;
}

.notification-content {
    margin: 0;
    font-size: 14px;
    color: #374151;
    line-height: 1.5;
}

.notification-title-text {
    font-weight: 700;
    color: #1f2937;
}

.notification-time {
    font-size: 11px;
    color: #9ca3af;
    margin-top: 6px;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}

/* Priority color indicators */
.notification-item.priority-1 {
    border-left-color: #ef4444;
    background: #fef2f2;
}

.notification-item.priority-2 {
    border-left-color: #f59e0b;
    background: #fffbeb;
}

.notification-item.priority-3 {
    border-left-color: #3b82f6;
}

.notification-item.priority-4 {
    border-left-color: #8b5cf6;
}

/* ================================
   SCROLLBAR STYLING
   ================================ */
.notification-list::-webkit-scrollbar {
    width: 8px;
}

.notification-list::-webkit-scrollbar-track {
    background: #f1f5f9;
}

.notification-list::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 10px;
}

.notification-list::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

/* ================================
   DARK MODE SUPPORT
   ================================ */
[data-theme="dark"] .notification-dropdown {
    background-color: #1e293b;
    border-color: #334155;
}

[data-theme="dark"] .notification-header {
    background: #0f172a;
    border-bottom-color: #334155;
}

[data-theme="dark"] .notification-title {
    color: #f8fafc;
}

[data-theme="dark"] .notification-list {
    background-color: #1e293b;
}

[data-theme="dark"] .notification-item {
    border-bottom-color: #334155;
}

[data-theme="dark"] .notification-item:hover {
    background: #334155;
}

[data-theme="dark"] .notification-item.priority-1 {
    background: #450a0a;
}

[data-theme="dark"] .notification-item.priority-2 {
    background: #451a03;
}

[data-theme="dark"] .notification-content {
    color: #e2e8f0;
}

[data-theme="dark"] .notification-title-text {
    color: #f8fafc;
}

[data-theme="dark"] .notification-time {
    color: #94a3b8;
}

[data-theme="dark"] .notification-list::-webkit-scrollbar-track {
    background: #0f172a;
}

[data-theme="dark"] .notification-list::-webkit-scrollbar-thumb {
    background: #475569;
}

[data-theme="dark"] .clear-btn:hover {
    background: #450a0a;
}

/* ================================
   RESPONSIVE
   ================================ */
@media (max-width: 576px) {
    .notification-dropdown {
        width: calc(100vw - 20px);
        right: -10px;
    }
    
    .notification-item {
        padding: 14px 16px;
    }
}
</style>

<script>
// Notification System - Enhanced Version
(function() {
    let notificationBtn = null;
    let notificationDropdown = null;
    let notificationList = null;
    let notificationBadge = null;
    
    // Initialize when DOM is ready
    function initNotifications() {
        notificationBtn = document.getElementById('notificationBtn');
        notificationDropdown = document.getElementById('notificationDropdown');
        notificationList = document.getElementById('notificationList');
        notificationBadge = document.getElementById('notificationBadge');
        
        if (!notificationBtn || !notificationDropdown) {
            console.error('Notification elements not found');
            return;
        }
        
        // Toggle dropdown on button click
        notificationBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const isVisible = notificationDropdown.style.display === 'block';
            
            if (isVisible) {
                notificationDropdown.style.display = 'none';
            } else {
                notificationDropdown.style.display = 'block';
            }
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (notificationDropdown && notificationDropdown.style.display === 'block') {
                if (!notificationDropdown.contains(e.target) && e.target !== notificationBtn && !notificationBtn.contains(e.target)) {
                    notificationDropdown.style.display = 'none';
                }
            }
        });
        
        // Prevent dropdown from closing when clicking inside it
        notificationDropdown.addEventListener('click', function(e) {
            e.stopPropagation();
        });
        
        // Load notifications immediately
        loadNotifications();
        
        // Refresh every 30 seconds
        setInterval(loadNotifications, 30000);
    }
    
    // Load notifications from backend
    function loadNotifications() {
        fetch('backend/notifications/get_notifications.php')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    updateNotificationUI(data.notifications, data.count);
                    
                    // Log debug info
                    if (data.debug) {
                        console.log('📊 Balance Debug:', data.debug);
                    }
                } else {
                    console.error('Failed to load notifications:', data.message);
                }
            })
            .catch(error => {
                console.error('Error loading notifications:', error);
                
                // Show error in UI
                if (notificationList) {
                    notificationList.innerHTML = `
                        <div class="notification-empty">
                            <i class="bi bi-exclamation-triangle" style="font-size: 48px; color: #ef4444; display: block; margin-bottom: 12px;"></i>
                            <p style="color: #ef4444; margin: 0;">Failed to load notifications</p>
                            <button onclick="loadNotifications()" style="margin-top: 12px; padding: 8px 16px; background: #3b82f6; color: white; border: none; border-radius: 8px; cursor: pointer;">
                                Retry
                            </button>
                        </div>
                    `;
                }
            });
    }
    
    // Update notification UI
    function updateNotificationUI(notifications, count) {
        if (!notificationList || !notificationBadge) return;
        
        // Update badge dynamically based on priority-1 and priority-2 notifications
        const criticalNotifications = notifications.filter(n => n.priority === 1 || n.priority === 2);
        const criticalCount = criticalNotifications.length;
        
        if (criticalCount > 0) {
            notificationBadge.textContent = criticalCount;
            notificationBadge.classList.add('show');
        } else {
            notificationBadge.classList.remove('show');
        }
        
        // Update notification list
        if (notifications.length === 0) {
            notificationList.innerHTML = `
                <div class="notification-empty">
                    <span class="notification-empty-icon">✨</span>
                    <p style="color: #10b981; font-weight: 600; margin: 0; font-size: 15px;">All caught up!</p>
                    <p style="color: #9ca3af; margin: 8px 0 0 0; font-size: 13px;">No new alerts to show</p>
                </div>
            `;
            return;
        }
        
        notificationList.innerHTML = '';
        
        notifications.forEach(function(notif) {
            const notifDiv = document.createElement('div');
            notifDiv.className = 'notification-item priority-' + (notif.priority || 3);
            
            notifDiv.innerHTML = `
                <p class="notification-content">
                    <span class="notification-icon">${notif.icon}</span>
                    <span class="notification-title-text">${notif.title}:</span>
                    ${notif.message}
                </p>
                <div class="notification-time">
                    <i class="bi bi-clock" style="font-size: 10px;"></i>
                    ${notif.time_ago}
                </div>
            `;
            
            notificationList.appendChild(notifDiv);
        });
    }
    
    // Clear notifications
    window.clearNotifications = function() {
        fetch('backend/notifications/clear_notifications.php', {
            method: 'POST'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                loadNotifications();
            }
        })
        .catch(error => {
            console.error('Error clearing notifications:', error);
        });
    };
    
    // Make loadNotifications available globally
    window.loadNotifications = loadNotifications;
    
    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initNotifications);
    } else {
        initNotifications();
    }
})();
</script>