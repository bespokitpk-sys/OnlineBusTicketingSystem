function showAlert() {
    showToast("Booking Successful!", "success");
}

// Toast notification function
function showToast(message, type = 'info', duration = 5000) {
    // Create toast container if it doesn't exist
    let container = document.querySelector('.toast-container');
    if (!container) {
        container = document.createElement('div');
        container.className = 'toast-container';
        document.body.appendChild(container);
    }

    // Create toast element
    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    toast.innerHTML = `
        <span>${message}</span>
        <button class="toast-close">&times;</button>
    `;

    // Add close button listener
    toast.querySelector('.toast-close').addEventListener('click', () => {
        toast.remove();
    });

    // Add to container
    container.appendChild(toast);

    // Auto remove after duration
    setTimeout(() => {
        if (toast.parentElement) {
            toast.remove();
        }
    }, duration);
}

// Scroll to top function
function scrollToTop() {
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

// Display error/success messages from server as toasts when page loads
window.addEventListener('DOMContentLoaded', function() {
    // Check for error messages
    const errorDiv = document.querySelector('.error-message');
    if (errorDiv) {
        const errorText = errorDiv.querySelector('p')?.textContent || 'An error occurred';
        showToast(errorText, 'error');
    }
    
    // Check for success messages
    const successDiv = document.querySelector('.success-message');
    if (successDiv) {
        const successText = successDiv.querySelector('p')?.textContent || 'Success!';
        showToast(successText, 'success');
    }
});