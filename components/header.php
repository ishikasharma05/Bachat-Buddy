<div class="header">
    <div class="d-flex align-items-center gap-2">
        <button class="btn d-lg-none border-0 p-0 me-2" onclick="toggleMenu()">
            <i class="bi bi-list fs-2"></i>
        </button>
        <h5 class="mb-0 fw-bold">Dashboard</h5>
    </div>
    
    <div class="d-flex align-items-center gap-3">
        <div class="relative inline-block text-left">
            <div id="notificationBtn" class="notification p-2 rounded-full cursor-pointer hover:bg-gray-200 transition-colors relative">
                <i class="bi bi-bell"></i>
                <span id="notificationBadge" class="absolute top-0 right-0 inline-flex items-center justify-center px-1.5 py-0.5 text-[10px] font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-500 rounded-full">
                    3
                </span>
            </div>
            <div id="notificationDropdown" class="hidden absolute right-0 mt-3 w-80 bg-white border border-gray-200 rounded-xl shadow-xl z-[1000] overflow-hidden">
                <div class="p-3 border-b border-gray-100 flex justify-between items-center bg-white">
                    <span class="font-semibold text-gray-700">Notifications</span>
                    <button onclick="clearNotifications()" class="text-xs text-blue-600 hover:underline">Clear all</button>
                </div>
                <div class="max-h-64 overflow-y-auto bg-white" id="notificationList">
                    <div class="p-3 border-b border-gray-50 hover:bg-gray-50 cursor-pointer">
                        <p class="text-sm text-gray-800 mb-0"><strong>Budget Alert:</strong> You've spent 80% of your Food budget.</p>
                        <span class="text-[11px] text-gray-400">5 mins ago</span>
                    </div>
                </div>
            </div>
        </div>

        <button id="theme-toggle" class="p-2 rounded-full border-0">
            <i class="fas fa-moon"></i>
        </button>

        <button id="logout-btn" class="notification p-2 rounded-full border-0" title="Close session">
            <i class="fas fa-sign-out-alt"></i>
        </button>
    </div>
</div>