<?php
// Ensure session is started (if not already)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Get user ID from session
$user_id = $_SESSION['user_id'] ?? null;
?>
<div class="header">
    <div class="d-flex align-items-center gap-2">
        <button class="btn d-lg-none border-0 p-0 me-2" onclick="toggleMenu()">
            <i class="bi bi-list fs-2"></i>
        </button>
        <h5 class="mb-0 fw-bold">Dashboard</h5>
    </div>
    
    <div class="d-flex align-items-center gap-3">
        <!-- Notification Bell -->
        <div style="position: relative; display: inline-block;">
            <button id="notificationBtn" class="notification p-2 rounded-full border-0" style="position: relative; cursor: pointer;">
                <i class="bi bi-bell"></i>
                <span id="notificationBadge" style="
                    position: absolute;
                    top: -2px;
                    right: -2px;
                    background: #ef4444;
                    color: white;
                    border-radius: 50%;
                    width: 18px;
                    height: 18px;
                    font-size: 11px;
                    display: none;
                    align-items: center;
                    justify-content: center;
                    font-weight: 600;
                    line-height: 1;
                ">0</span>
            </button>
            
            <div id="notificationDropdown" style="
                display: none;
                position: absolute;
                right: 0;
                top: 50px;
                width: 350px;
                max-width: 90vw;
                background: white;
                border: 1px solid #e5e7eb;
                border-radius: 12px;
                box-shadow: 0 10px 25px rgba(0,0,0,0.15);
                z-index: 9999;
                max-height: 400px;
                overflow: hidden;
            ">
                <div style="padding: 12px 16px; border-bottom: 1px solid #e5e7eb; display: flex; justify-content: space-between; align-items: center; background: white;">
                    <span style="font-weight: 600; color: #374151;">Notifications</span>
                    <button onclick="clearNotifications()" style="background: none; border: none; color: #3b82f6; font-size: 12px; cursor: pointer; text-decoration: underline;">Clear all</button>
                </div>
                <div id="notificationList" style="max-height: 320px; overflow-y: auto; background: white;">
                    <div style="padding: 20px; text-align: center; color: #9ca3af;">
                        <i class="bi bi-bell-slash" style="font-size: 32px; display: block; margin-bottom: 8px; color: #d1d5db;"></i>
                        Loading notifications...
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
/* Dark mode support for notifications */
[data-theme="dark"] #notificationDropdown {
    background-color: #1e293b !important;
    border-color: #334155 !important;
}

[data-theme="dark"] #notificationDropdown > div:first-child {
    background-color: #1e293b !important;
    border-bottom-color: #334155 !important;
}

[data-theme="dark"] #notificationDropdown span {
    color: #f8fafc !important;
}

[data-theme="dark"] #notificationList {
    background-color: #1e293b !important;
}

[data-theme="dark"] .notification-item {
    border-bottom-color: #334155 !important;
    background-color: #1e293b !important;
}

[data-theme="dark"] .notification-item:hover {
    background-color: #334155 !important;
}

[data-theme="dark"] .notification-item p {
    color: #e2e8f0 !important;
}

[data-theme="dark"] .notification-item span {
    color: #94a3b8 !important;
}

/* Scrollbar styling */
#notificationList::-webkit-scrollbar {
    width: 6px;
}

#notificationList::-webkit-scrollbar-track {
    background: #f1f5f9;
}

[data-theme="dark"] #notificationList::-webkit-scrollbar-track {
    background: #0f172a;
}

#notificationList::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 3px;
}

[data-theme="dark"] #notificationList::-webkit-scrollbar-thumb {
    background: #475569;
}
</style>

<script>
// Notification System - Standalone
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
            
            console.log('Notification dropdown toggled:', !isVisible);
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
        console.log('Loading notifications...');
        
        fetch('backend/notifications/get_notifications.php')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                console.log('Notifications loaded:', data);
                
                if (data.success) {
                    updateNotificationUI(data.notifications, data.count);
                } else {
                    console.error('Failed to load notifications:', data.message);
                }
            })
            .catch(error => {
                console.error('Error loading notifications:', error);
                
                // Show error in UI
                if (notificationList) {
                    notificationList.innerHTML = `
                        <div style="padding: 20px; text-align: center; color: #ef4444;">
                            <i class="bi bi-exclamation-triangle" style="font-size: 32px; display: block; margin-bottom: 8px;"></i>
                            Failed to load notifications
                        </div>
                    `;
                }
            });
    }
    
    // Update notification UI
    function updateNotificationUI(notifications, count) {
        if (!notificationList || !notificationBadge) return;
        
        // Update badge
        if (count > 0) {
            notificationBadge.textContent = count;
            notificationBadge.style.display = 'flex';
        } else {
            notificationBadge.style.display = 'none';
        }
        
        // Update notification list
        if (notifications.length === 0) {
            notificationList.innerHTML = `
                <div style="padding: 20px; text-align: center; color: #10b981;">
                    <i class="bi bi-check2-all" style="font-size: 32px; display: block; margin-bottom: 8px; color: #10b981;"></i>
                    All caught up! No new alerts.
                </div>
            `;
            return;
        }
        
        notificationList.innerHTML = '';
        
        notifications.forEach(function(notif) {
            const notifDiv = document.createElement('div');
            notifDiv.className = 'notification-item';
            notifDiv.style.cssText = 'padding: 12px 16px; border-bottom: 1px solid #f3f4f6; cursor: pointer; transition: background-color 0.2s;';
            
            notifDiv.innerHTML = `
                <p style="margin: 0 0 4px 0; font-size: 14px; color: #374151; line-height: 1.5;">
                    ${notif.icon} <strong>${notif.title}:</strong> ${notif.message}
                </p>
                <span style="font-size: 11px; color: #9ca3af;">${notif.time_ago}</span>
            `;
            
            // Hover effect
            notifDiv.addEventListener('mouseenter', function() {
                this.style.backgroundColor = '#f9fafb';
            });
            
            notifDiv.addEventListener('mouseleave', function() {
                this.style.backgroundColor = 'transparent';
            });
            
            notificationList.appendChild(notifDiv);
        });
    }
    
    // Clear notifications
    window.clearNotifications = function() {
        console.log('Clearing notifications...');
        
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
    
    // Make loadNotifications available globally for debugging
    window.loadNotifications = loadNotifications;
    
    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initNotifications);
    } else {
        // DOM is already ready
        initNotifications();
    }
})();
</script>