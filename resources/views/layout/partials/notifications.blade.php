<!-- Notification Container -->
<div id="notification-container" class="position-fixed top-0 end-0 p-3" style="z-index: 9999;"></div>

<script>
function showNotification(type, message) {
    const container = document.getElementById('notification-container');
    
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show`;
    notification.innerHTML = `
        <i class="ph ph-${type === 'success' ? 'check-circle' : 'warning'} me-2"></i>
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    // Add to container
    container.appendChild(notification);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (notification.parentNode) {
            notification.remove();
        }
    }, 5000);
    
    // Remove on close button click
    notification.querySelector('.btn-close').addEventListener('click', () => {
        notification.remove();
    });
}
</script>
