<?php
// Forward both GET and POST requests to auth_router for proper registration handling
require_once __DIR__ . '/../config/db.php';
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Forward POST data to auth_router
    $_GET['action'] = 'register';
    include_once 'auth_router.php';
} else {
    // Forward GET requests to show registration form
    header('Location: auth_router.php?action=register');
    exit;
}
?> 
                required
                value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>">
        </div>

        <div class="form-group">
            <label for="email">Email Address *</label>
            <input 
                type="email" 
                id="email" 
                name="email" 
                placeholder="you@example.com" 
                required
                value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
        </div>

        <div class="form-group">
            <label for="phone">Phone Number *</label>
            <input 
                type="tel" 
                id="phone" 
                name="phone" 
                placeholder="03001234567" 
                required
                value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>">
        </div>

        <div class="form-group">
            <label for="password">Password *</label>
            <input 
                type="password" 
                id="password" 
                name="password" 
                placeholder="Create a strong password" 
                required>
            <small>Minimum 8 characters with uppercase, lowercase, numbers, and special characters.</small>
        </div>

        <div class="form-group">
            <label for="confirm_password">Confirm Password *</label>
            <input 
                type="password" 
                id="confirm_password" 
                name="confirm_password" 
                placeholder="Confirm your password" 
                required>
        </div>

        <div class="form-group">
            <label>
                <input type="checkbox" name="agree" required>
                I agree to the <a href="#" target="_blank">Terms & Conditions</a> and <a href="#" target="_blank">Privacy Policy</a>
            </label>
        </div>

        <div class="form-group">
            <div class="g-recaptcha" data-sitekey="6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI"></div>
        </div>

        <button type="submit" class="btn-primary">Create Account</button>
    </form>

    <p class="small-text">
        Already have an account? <a href="login.php">Sign in here</a>
    </p>
</div>

<script>
    document.querySelector('form').addEventListener('submit', function(e) {
        const password = document.getElementById('password').value;
        const confirm = document.getElementById('confirm_password').value;
        
        if (password !== confirm) {
            e.preventDefault();
            alert('Passwords do not match.');
            return false;
        }

        const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;
        if (!passwordRegex.test(password)) {
            e.preventDefault();
            alert('Password must be at least 8 characters and contain uppercase, lowercase, numbers, and special characters.');
            return false;
        }
    });
</script>

</body>
</html>
</body>
</html>