class PaymentNotificationSystem {
    constructor() {
        this.intervalId = null;
        this.userIntervalId = null;
        this.dismissedNotifications = new Set();
        this.checkUsers = false; // Flag to enable user checking
    }

    init(options = {}) {
        console.log('🚀 Initializing Payment Notification System...');
        
        // Set options
        this.checkUsers = options.checkUsers || false;
        
        // Load previously dismissed notifications
        this.loadDismissedNotifications();
        
        // Create toast container
        this.createToastContainer();
        
        // Start checking for new payments
        this.startChecking();
        
        // Start checking for new users if enabled
        if (this.checkUsers) {
            this.startUserChecking();
        }
        
        // Handle page visibility change
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                this.stopChecking();
                if (this.checkUsers) {
                    this.stopUserChecking();
                }
            } else {
                this.startChecking();
                if (this.checkUsers) {
                    this.startUserChecking();
                }
            }
        });
        
        // Add notification permission request
        this.requestNotificationPermission();
        
        console.log('✅ Payment Notification System initialized successfully');
        if (this.checkUsers) {
            console.log('👥 User notifications enabled');
        }
    }

    // Load dismissed notifications from localStorage
    loadDismissedNotifications() {
        try {
            const saved = localStorage.getItem('synergy_dismissed_notifications');
            if (saved) {
                const dismissed = JSON.parse(saved);
                this.dismissedNotifications = new Set(dismissed);
                console.log('📂 Loaded dismissed notifications:', this.dismissedNotifications.size, 'items');
            }
        } catch (e) {
            console.warn('⚠️ Failed to load dismissed notifications:', e);
            this.dismissedNotifications = new Set();
        }
    }

    // Save dismissed notifications to localStorage
    saveDismissedNotifications() {
        try {
            const dismissed = Array.from(this.dismissedNotifications);
            localStorage.setItem('synergy_dismissed_notifications', JSON.stringify(dismissed));
            console.log('💾 Saved dismissed notifications:', dismissed.length, 'items');
        } catch (e) {
            console.warn('⚠️ Failed to save dismissed notifications:', e);
        }
    }

    createToastContainer() {
        if (!document.querySelector('.toast-container')) {
            const container = document.createElement('div');
            container.className = 'toast-container';
            document.body.appendChild(container);
            console.log('📦 Toast container created');
        }
    }

    requestNotificationPermission() {
        if ('Notification' in window && Notification.permission === 'default') {
            Notification.requestPermission().then(permission => {
                console.log('🔔 Notification permission:', permission);
            });
        }
    }

    startChecking() {
        // Stop any existing interval
        this.stopChecking();
        
        // Check immediately
        this.checkForNewPayments();
        
        // Then check every 30 seconds
        this.intervalId = setInterval(() => {
            this.checkForNewPayments();
        }, 30000); // 30 seconds
        
        console.log('▶️ Payment checking started (every 30 seconds)');
    }

    stopChecking() {
        if (this.intervalId) {
            clearInterval(this.intervalId);
            this.intervalId = null;
            console.log('⏹️ Payment checking stopped');
        }
    }

    startUserChecking() {
        // Stop any existing interval
        this.stopUserChecking();
        
        // Check immediately
        this.checkForNewUsers();
        
        // Then check every 30 seconds
        this.userIntervalId = setInterval(() => {
            this.checkForNewUsers();
        }, 30000); // 30 seconds
        
        console.log('▶️ User checking started (every 30 seconds)');
    }

    stopUserChecking() {
        if (this.userIntervalId) {
            clearInterval(this.userIntervalId);
            this.userIntervalId = null;
            console.log('⏹️ User checking stopped');
        }
    }

    async checkForNewPayments() {
        console.log('🔍 Checking for new payments...');
        try {
            const response = await fetch('check_new_payments.php', {
                method: 'GET',
                headers: {
                    'Cache-Control': 'no-cache'
                }
            });
            
            console.log('📡 Response status:', response.status);
            
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            
            const data = await response.json();
            console.log('📥 Payment data received:', data);
            
            if (data.success && data.new_payments > 0) {
                console.log('🎉 New payments found:', data.new_payments);
                this.showPaymentNotification(data);
                
                // Play notification sound if available
                this.playNotificationSound();
                
                // Show browser notification if permission granted
                this.showBrowserNotification(data);
            } else if (data.success && data.new_payments === 0) {
                console.log('✅ No new payments');
            } else {
                console.log('⚠️ No success in response:', data);
            }
        } catch (error) {
            console.error('❌ Error checking payments:', error);
            
            // Show error notification occasionally (not every time to avoid spam)
            if (Math.random() < 0.1) { // 10% chance
                this.showToast('⚠️ Connection Issue', 'Unable to check for new payments: ' + error.message, 'error');
            }
        }
    }

    showPaymentNotification(data) {
        const count = data.new_payments;
        const amount = data.total_amount;
        
        // Create unique notification ID based on payment details
        const notificationId = `payment_${count}_${amount}_${data.latest_payment_time}`;
        
        // Skip if this exact notification was already dismissed
        if (this.dismissedNotifications.has(notificationId)) {
            console.log('🚫 Notification already dismissed, skipping:', notificationId);
            return;
        }
        
        let message = count === 1 
            ? `New payment received: ${amount.toFixed(2)} GEL` 
            : `${count} new payments received: ${amount.toFixed(2)} GEL total`;
        
        // Add payment details if available
        if (data.payment_details && data.payment_details.length > 0) {
            message += '\n\nDetails:\n';
            data.payment_details.forEach(payment => {
                message += `• ${payment.amount} GEL from ${payment.client_mobile_number}\n`;
            });
        }
        
        this.showToast('💰 Payment Alert', message, 'success', notificationId);
    }

    async checkForNewUsers() {
        console.log('👥 Checking for new users...');
        try {
            const response = await fetch('check_new_users.php', {
                method: 'GET',
                headers: {
                    'Cache-Control': 'no-cache'
                }
            });
            
            console.log('📡 User response status:', response.status);
            
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            
            const data = await response.json();
            console.log('📥 User data received:', data);
            
            if (data.success && data.new_users > 0) {
                console.log('🎉 New users found:', data.new_users);
                this.showUserNotification(data);
                
                // Play notification sound if available
                this.playNotificationSound();
                
                // Show browser notification if permission granted
                this.showBrowserUserNotification(data);
            } else if (data.success && data.new_users === 0) {
                console.log('✅ No new users');
            } else {
                console.log('⚠️ No success in user response:', data);
            }
        } catch (error) {
            console.error('❌ Error checking users:', error);
            
            // Show error notification occasionally (not every time to avoid spam)
            if (Math.random() < 0.1) { // 10% chance
                this.showToast('⚠️ User Check Issue', 'Unable to check for new users: ' + error.message, 'error');
            }
        }
    }

    showUserNotification(data) {
        const count = data.new_users;
        
        // Create unique notification ID based on user details
        const notificationId = `user_${count}_${data.latest_user_time}`;
        
        // Skip if this exact notification was already dismissed
        if (this.dismissedNotifications.has(notificationId)) {
            console.log('🚫 User notification already dismissed, skipping:', notificationId);
            return;
        }
        
        let message = count === 1 
            ? `New user registered` 
            : `${count} new users registered`;
        
        // Add user details if available
        if (data.user_details && data.user_details.length > 0) {
            message += '\n\nDetails:\n';
            data.user_details.forEach(user => {
                message += `• ${user.full_name} (${user.mobile_number})\n`;
            });
        }
        
        this.showToast('👤 New User Alert', message, 'info', notificationId);
    }

    showToast(title, message, type = 'success', notificationId = null) {
        console.log('🍞 Showing toast:', title, type);
        
        const container = document.querySelector('.toast-container');
        if (!container) {
            console.error('❌ Toast container not found');
            return;
        }

        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        
        // Store notification ID for tracking
        if (notificationId) {
            toast.dataset.notificationId = notificationId;
        }
        
        // Format message for HTML (convert \n to <br>)
        const formattedMessage = message.replace(/\n/g, '<br>');
        
        toast.innerHTML = `
            <div class="toast-header">
                ${title}
                <button class="toast-close" type="button" aria-label="Close">×</button>
            </div>
            <div class="toast-body">${formattedMessage}</div>
            <div class="toast-footer">
                Click anywhere on this notification to dismiss
            </div>
        `;
        
        // Make entire toast clickable to dismiss
        toast.addEventListener('click', () => {
            this.dismissToast(toast);
        });
        
        // Prevent close button click from bubbling
        const closeBtn = toast.querySelector('.toast-close');
        closeBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            this.dismissToast(toast);
        });
        
        container.appendChild(toast);
        
        // Trigger animation
        setTimeout(() => {
            toast.classList.add('show');
        }, 10);
        
        console.log(`✨ Toast notification shown: ${title}`);
    }

    dismissToast(toast) {
        // Mark notification as dismissed if it has an ID
        const notificationId = toast.dataset.notificationId;
        if (notificationId) {
            this.dismissedNotifications.add(notificationId);
            console.log('🗑️ Dismissed notification:', notificationId);
            
            // Store dismissed notifications in localStorage for persistence
            this.saveDismissedNotifications();
        }
        
        // Remove toast with animation
        toast.classList.remove('show');
        setTimeout(() => {
            if (toast.parentNode) {
                toast.parentNode.removeChild(toast);
            }
        }, 300);
    }

    showBrowserNotification(data) {
        if ('Notification' in window && Notification.permission === 'granted') {
            const count = data.new_payments;
            const amount = data.total_amount;
            
            const title = count === 1 
                ? 'New Payment Received' 
                : `${count} New Payments Received`;
            
            const body = count === 1 
                ? `Amount: ${amount.toFixed(2)} GEL` 
                : `Total: ${amount.toFixed(2)} GEL`;
            
            const notification = new Notification(title, {
                body: body,
                icon: '/favicon.ico',
                badge: '/favicon.ico'
            });
            
            // Auto close after 5 seconds
            setTimeout(() => {
                notification.close();
            }, 5000);
            
            console.log('🔔 Browser notification shown');
        }
    }

    showBrowserUserNotification(data) {
        if ('Notification' in window && Notification.permission === 'granted') {
            const count = data.new_users;
            
            const title = count === 1 
                ? 'New User Registered' 
                : `${count} New Users Registered`;
            
            let body = 'Check the admin panel for details';
            if (data.user_details && data.user_details.length > 0) {
                const firstUser = data.user_details[0];
                body = count === 1 
                    ? `${firstUser.full_name} (${firstUser.mobile_number})` 
                    : `Latest: ${firstUser.full_name} and ${count - 1} more`;
            }
            
            const notification = new Notification(title, {
                body: body,
                icon: '/favicon.ico',
                badge: '/favicon.ico'
            });
            
            // Auto close after 5 seconds
            setTimeout(() => {
                notification.close();
            }, 5000);
            
            console.log('🔔 Browser user notification shown');
        }
    }

    playNotificationSound() {
        try {
            // Create a simple notification sound
            const audio = new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+HyvmwhCi2QzdKNOQxGm+H1vGAhCzWWzsiNOQ5IrePtu2Ah');
            audio.volume = 0.3;
            audio.play().then(() => {
                console.log('🔊 Notification sound played');
            }).catch(e => {
                console.log('🔇 Could not play notification sound:', e);
            });
        } catch (e) {
            console.log('🔇 Audio not supported');
        }
    }
}

// Export for use in other scripts
if (typeof module !== 'undefined' && module.exports) {
    module.exports = PaymentNotificationSystem;
}

console.log('📜 PaymentNotificationSystem class loaded');
