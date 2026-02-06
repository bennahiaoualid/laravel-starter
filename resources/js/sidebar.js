document.addEventListener('DOMContentLoaded', function () {
    // Check if sidebar elements exist before proceeding
    const sidebar = document.getElementById('sidebar');
    const sideBarToggleBtn = document.getElementById('toggleSidebarBtn');
    const sideBarToggleDesktopBtn = document.getElementById('toggleSidebarDesktopBtn');
    
    // Only initialize if sidebar exists (user is authenticated)
    if (!sidebar || !sideBarToggleBtn || !sideBarToggleDesktopBtn) {
        return; 
    }
    
    // Check localStorage for saved state
    const savedState = localStorage.getItem('sidebarCollapsed');
    if (savedState === 'true') {
        sidebar.classList.add('collapsed');
    }

    // Mobile toggle (existing functionality)
    sideBarToggleBtn.addEventListener('click', function() {
        sidebar.classList.remove('-translate-x-full');
    });

    // Desktop toggle (new functionality)
    sideBarToggleDesktopBtn.addEventListener('click', function() {
        sidebar.classList.toggle('collapsed');
        
        // Save state to localStorage
        const isCollapsed = sidebar.classList.contains('collapsed');
        localStorage.setItem('sidebarCollapsed', isCollapsed);
    });

    // Close sidebar when clicking outside (mobile only)
    document.addEventListener('click', function(e) {
        if (!sidebar.contains(e.target) && !sideBarToggleBtn.contains(e.target)) {
            sidebar.classList.add('-translate-x-full');
        }
    });

    // Restore sidebar when clicking on dropdown toggle if collapsed
    sidebar.addEventListener('click', function(e) {
        // Check if the clicked element is a dropdown toggle button
        const dropdownToggle = e.target.closest('[data-dropdown-target]');
        
        if (dropdownToggle && sidebar.classList.contains('collapsed')) {
            // Expand the sidebar first
            sidebar.classList.remove('collapsed');
            localStorage.setItem('sidebarCollapsed', 'false');
        }
    });
});
