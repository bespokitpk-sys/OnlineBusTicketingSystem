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

    // ── Mobile hamburger menu ──────────────────────────────────────────────
    const nav = document.querySelector('nav');
    if (nav) {
        // Find the links div (first div inside nav)
        const navLinksDiv = nav.querySelector('div');
        if (navLinksDiv) {
            // Mark it so CSS can target it
            navLinksDiv.classList.add('nav-links');

            // Create hamburger button
            const hamburger = document.createElement('button');
            hamburger.className = 'hamburger-btn';
            hamburger.setAttribute('aria-label', 'Toggle navigation menu');
            hamburger.setAttribute('aria-expanded', 'false');
            hamburger.innerHTML = '&#9776;'; // ☰

            // Insert between brand and links
            nav.insertBefore(hamburger, navLinksDiv);

            // Toggle on hamburger click
            hamburger.addEventListener('click', function(e) {
                e.stopPropagation();
                const isOpen = navLinksDiv.classList.toggle('nav-open');
                hamburger.innerHTML = isOpen ? '&#10005;' : '&#9776;'; // ✕ or ☰
                hamburger.setAttribute('aria-expanded', String(isOpen));
            });

            // Close when clicking a nav link
            navLinksDiv.querySelectorAll('a').forEach(function(link) {
                link.addEventListener('click', function() {
                    navLinksDiv.classList.remove('nav-open');
                    hamburger.innerHTML = '&#9776;';
                    hamburger.setAttribute('aria-expanded', 'false');
                });
            });

            // Close when clicking outside nav
            document.addEventListener('click', function(e) {
                if (!nav.contains(e.target)) {
                    navLinksDiv.classList.remove('nav-open');
                    hamburger.innerHTML = '&#9776;';
                    hamburger.setAttribute('aria-expanded', 'false');
                }
            });
        }
    }

    // ── Wrap tables for horizontal scroll on mobile ────────────────────────
    document.querySelectorAll('.table-container table, table:not(.no-wrap)').forEach(function(table) {
        const parent = table.parentElement;
        if (parent && !parent.classList.contains('table-responsive-wrap')) {
            const wrapper = document.createElement('div');
            wrapper.className = 'table-responsive-wrap';
            parent.insertBefore(wrapper, table);
            wrapper.appendChild(table);
        }
    });
});