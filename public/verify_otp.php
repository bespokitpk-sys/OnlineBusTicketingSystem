<?php
header('Location: auth_router.php?action=verifyOTP');
exit;
?>

        <div class="form-group">
            <label for="otp">Verification Code *</label>
            <input 
                type="text" 
                id="otp" 
                name="otp" 
                placeholder="Enter 6-digit code" 
                maxlength="6" 
                required
                inputmode="numeric">
        </div>

        <button type="submit" class="btn-primary">Verify Email</button>
    </form>

    <p class="small-text">
        Didn't receive the code? <a href="auth_router.php?action=register">Back to Register</a>
    </p>
</div>

<script>
    // Only allow numbers
    document.getElementById('otp').addEventListener('input', function(e) {
        e.target.value = e.target.value.replace(/[^0-9]/g, '');
    });
</script>

</body>
</html>