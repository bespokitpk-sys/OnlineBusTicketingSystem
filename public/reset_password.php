<?php
$token = $_GET['token'] ?? '';
if (empty($token)) {
    header('Location: auth_router.php?action=login');
    exit;
}
header('Location: auth_router.php?action=resetPassword&token=' . urlencode($token));
exit;
?>

        <div class="form-group">
            <label for="password">New Password *</label>
            <input 
                type="password" 
                id="password" 
                name="password" 
                placeholder="Enter new password" 
                required>
            <small>Minimum 8 characters with uppercase, lowercase, numbers, and special characters.</small>
        </div>

        <div class="form-group">
            <label for="confirm_password">Confirm New Password *</label>
            <input 
                type="password" 
                id="confirm_password" 
                name="confirm_password" 
                placeholder="Confirm new password" 
                required>
        </div>

        <button type="submit" class="btn-primary">Reset Password</button>
    </form>

    <p class="small-text">
        <a href="login.php">Back to Login</a>
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