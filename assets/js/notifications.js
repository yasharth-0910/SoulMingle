document.addEventListener('DOMContentLoaded', function() {
    const notificationToggle = document.querySelector('.notification-toggle');
    const notificationDropdown = document.querySelector('.notification-dropdown');
    
    // Toggle notification dropdown
    notificationToggle.addEventListener('click', function() {
        notificationDropdown.style.display = 
            notificationDropdown.style.display === 'block' ? 'none' : 'block';
    });
    
    // Close dropdown when clicking outside
    document.addEventListener('click', function(event) {
        if (!event.target.closest('.notification-bar')) {
            notificationDropdown.style.display = 'none';
        }
    });
    
    // Mark notifications as read
    const notificationItems = document.querySelectorAll('.notification-item');
    notificationItems.forEach(item => {
        item.addEventListener('click', function() {
            const notificationId = this.dataset.id;
            markNotificationAsRead(notificationId);
        });
    });
});

function markNotificationAsRead(notificationId) {
    fetch('mark-notification-read.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'notification_id=' + notificationId
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            // Update UI to reflect read status
            document.querySelector(`[data-id="${notificationId}"]`)
                .classList.add('notification-read');
            
            // Update notification count
            const count = document.querySelector('.notification-count');
            if(count) {
                const currentCount = parseInt(count.textContent);
                if(currentCount <= 1) {
                    count.remove();
                } else {
                    count.textContent = currentCount - 1;
                }
            }
        }
    });
} 