<div id="notification-container" x-data="notificationHandler()" x-init="init()" x-on:notify.window="handleEvent($event)">
    <!-- Notifications will be rendered here by Alpine.js -->
    <!-- This component also listens for runtime notifications via a window 'notify' event -->
    <!-- Expected payload shape: { message: string, type: 'success'|'error'|'warning'|'info' } -->
</div>

<script>
    function notificationHandler() {
        return {
            notifications: @json(session('messages', [])),
            toastrConfigured: false,
            
            init() {
                this.configureToastr();
                this.showNotifications();
                // Also support direct JS dispatch: window.dispatchEvent(new CustomEvent('notify', { detail: { type:'success', message:'...' } }))
                window.addEventListener('notify', (e) => this.handleEvent(e));
            },
            
            configureToastr() {
                if (typeof toastr === 'undefined') {
                    console.warn('Toastr library is not loaded');
                    return;
                }
                if (this.toastrConfigured) return;
                toastr.options = {
                    timeOut: 8000,
                    extendedTimeOut: 2000,
                    progressBar: true,
                    closeButton: true,
                    preventDuplicates: true,
                    positionClass: document.documentElement.dir === "rtl" 
                        ? "toast-top-left" 
                        : "toast-top-right",
                    showMethod: 'slideDown',
                    hideMethod: 'slideUp',
                    showDuration: 300,
                    hideDuration: 300
                };
                this.toastrConfigured = true;
            },

            handleEvent(event) {
                const detail = event?.detail;
                if (!detail) return;
                // Accept single notification or array
                const items = Array.isArray(detail) ? detail : [detail];
                items.forEach((n) => {
                    if (this.isValidNotification(n)) {
                        this.showNotification(n);
                    }
                });
            },
            
            showNotifications() {
                if (typeof toastr === 'undefined') {
                    this.fallbackNotifications();
                    return;
                }

                this.notifications.forEach((notification, index) => {
                    if (this.isValidNotification(notification)) {
                        setTimeout(() => {
                            this.showNotification(notification);
                        }, index * 200);
                    }
                });
            },
            
            isValidNotification(notification) {
                return notification && 
                       notification.message && 
                       (notification['alert-type'] || notification.type);
            },
            
            showNotification(notification) {
                const message = this.escapeHtml(notification.message);
                const type = notification['alert-type'] || notification.type || 'info';

                if (typeof toastr === 'undefined') {
                    this.fallbackLog(type, message);
                    return;
                }

                switch (type) {
                    case 'success':
                        toastr.success(message);
                        break;
                    case 'error':
                    case 'danger':
                        toastr.error(message);
                        break;
                    case 'warning':
                        toastr.warning(message);
                        break;
                    case 'info':
                    default:
                        toastr.info(message);
                        break;
                }
            },
            
            fallbackNotifications() {
                // Fallback for when toastr is not available
                (this.notifications || []).forEach(notification => {
                    if (this.isValidNotification(notification)) {
                        this.fallbackLog(notification['alert-type'] || notification.type, notification.message);
                    }
                });
            },

            fallbackLog(type, message) {
                console.log(`${type || 'info'}: ${message}`);
            },
            
            escapeHtml(text) {
                const map = {
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": '&#039;'
                };
                return String(text).replace(/[&<>"']/g, m => map[m]);
            }
        }
    }
</script>


