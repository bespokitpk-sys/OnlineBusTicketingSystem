<?php
require_once APP_ROOT . '/config/db.php';
require_once APP_ROOT . '/app/models/User.php';

class AuthController {
    public static function register() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . 'register');
            exit;
        }

        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $cnic = trim($_POST['cnic'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        $agree = $_POST['agree'] ?? '';
        $captcha = $_POST['g-recaptcha-response'] ?? '';

        // Validate CAPTCHA (optional for testing - set CAPTCHA_ENABLED to false in production)
        define('CAPTCHA_ENABLED', false); // Set to false to disable CAPTCHA during testing
        
        if (CAPTCHA_ENABLED) {
            if (empty($captcha)) {
                $_SESSION['error'] = 'Please complete the CAPTCHA.';
                header('Location: ' . BASE_URL . 'register');
                exit;
            }
            // Verify CAPTCHA (assume function exists)
            if (!self::verifyCaptcha($captcha)) {
                $_SESSION['error'] = 'CAPTCHA verification failed.';
                header('Location: ' . BASE_URL . 'register');
                exit;
            }
        }

        // Validate fields
        if (empty($name) || empty($email) || empty($phone) || empty($cnic) || empty($password)) {
            $_SESSION['error'] = 'All required fields must be filled.';
            header('Location: ' . BASE_URL . 'register');
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = 'Invalid email address.';
            header('Location: ' . BASE_URL . 'register');
            exit;
        }

        if (!preg_match('/^[0-9]{10,15}$/', $phone)) {
            $_SESSION['error'] = 'Invalid phone number.';
            header('Location: ' . BASE_URL . 'register');
            exit;
        }

        // Validate CNIC (13 digits)
        if (!preg_match('/^[0-9]{13}$/', $cnic)) {
            $_SESSION['error'] = 'CNIC must be exactly 13 digits.';
            header('Location: ' . BASE_URL . 'register');
            exit;
        }

        if (strlen($password) < 8 || !preg_match('/[A-Za-z]/', $password) || !preg_match('/[0-9]/', $password) || !preg_match('/[^A-Za-z0-9]/', $password)) {
            $_SESSION['error'] = 'Password must be at least 8 characters with letters, numbers, and special characters.';
            header('Location: ' . BASE_URL . 'register');
            exit;
        }

        if ($password !== $confirmPassword) {
            $_SESSION['error'] = 'Passwords do not match.';
            header('Location: ' . BASE_URL . 'register');
            exit;
        }

        if (!$agree) {
            $_SESSION['error'] = 'You must agree to the terms and conditions.';
            header('Location: ' . BASE_URL . 'register');
            exit;
        }

        if (User::findByEmail($email)) {
            $_SESSION['error'] = 'This email is already registered.';
            header('Location: ' . BASE_URL . 'register');
            exit;
        }

        // Handle profile picture upload
        $profilePicture = null;
        if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['profile_picture'];
            $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
            $maxSize = 2 * 1024 * 1024; // 2MB

            // Validate file type
            if (!in_array($file['type'], $allowedTypes)) {
                $_SESSION['error'] = 'Invalid file type. Only JPG, PNG, and GIF are allowed.';
                header('Location: ' . BASE_URL . 'register');
                exit;
            }

            // Validate file size
            if ($file['size'] > $maxSize) {
                $_SESSION['error'] = 'File size exceeds 2MB limit.';
                header('Location: ' . BASE_URL . 'register');
                exit;
            }

            // Generate unique filename
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = 'profile_' . time() . '_' . uniqid() . '.' . $extension;
            $uploadPath = APP_ROOT . '/uploads/profiles/' . $filename;

            // Move uploaded file
            if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
                $profilePicture = 'uploads/profiles/' . $filename;
            } else {
                $_SESSION['error'] = 'Failed to upload profile picture.';
                header('Location: ' . BASE_URL . 'register');
                exit;
            }
        }

        // Create user
        $userId = User::create($name, $email, $phone, $password, 'passenger', false, $cnic, $profilePicture);

        // Generate OTP
        $otp = User::generateOTP($userId);

        $_SESSION['temp_user_id'] = $userId;
        $_SESSION['temp_email'] = $email;
        $_SESSION['test_otp'] = $otp; // temporary testing support

        // Send OTP (simulate email)
        self::sendOTP($email, $otp);

        header('Location: ' . BASE_URL . 'verify-otp');
        exit;
    }

    public static function verifyOTP() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . 'verify-otp');
            exit;
        }

        $otp = trim($_POST['otp'] ?? '');
        $userId = $_SESSION['temp_user_id'] ?? null;

        if (!$userId || empty($otp)) {
            $_SESSION['error'] = 'Invalid request.';
            header('Location: ' . BASE_URL . 'verify-otp');
            exit;
        }

        if (User::verifyOTP($userId, $otp)) {
            $user = User::findById($userId);
            $_SESSION['user_id'] = $userId;
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];
            unset($_SESSION['temp_user_id'], $_SESSION['temp_email']);
            $_SESSION['success'] = 'Registration successful! Welcome to Bus Ticketing System.';
            header('Location: ' . BASE_URL . 'passenger/dashboard');
            exit;
        } else {
            $_SESSION['error'] = 'Invalid or expired OTP.';
            header('Location: ' . BASE_URL . 'verify-otp');
            exit;
        }
    }

    public static function login() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . 'login');
            exit;
        }

        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            $_SESSION['error'] = 'Please enter email and password.';
            header('Location: ' . BASE_URL . 'login');
            exit;
        }

        $user = User::authenticate($email, $password);
        if ($user) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];
            $redirect = match($user['role']) {
                'admin' => 'admin/dashboard',
                'operator' => 'operator/dashboard',
                default => 'passenger/dashboard'
            };
            header('Location: ' . BASE_URL . $redirect);
            exit;
        } else {
            $_SESSION['error'] = 'Invalid email or password, or account not verified.';
            header('Location: ' . BASE_URL . 'login');
            exit;
        }
    }

    public static function forgotPassword() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . 'forgot-password');
            exit;
        }

        $email = trim($_POST['email'] ?? '');

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = 'Please enter a valid email.';
            header('Location: ' . BASE_URL . 'forgot-password');
            exit;
        }

        $token = User::generateResetToken($email);
        if ($token) {
            // Send reset email
            self::sendResetEmail($email, $token);
            $_SESSION['success'] = 'Password reset link sent to your email.';
        } else {
            $_SESSION['error'] = 'Email not found.';
        }
        header('Location: ' . BASE_URL . 'forgot-password');
        exit;
    }

    public static function resetPassword() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . 'reset-password?token=' . ($_GET['token'] ?? ''));
            exit;
        }

        $token = trim($_POST['token'] ?? $_GET['token'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if (empty($token) || empty($password)) {
            $_SESSION['error'] = 'Invalid request.';
            header('Location: ' . BASE_URL . 'reset-password?token=' . $token);
            exit;
        }

        if ($password !== $confirmPassword) {
            $_SESSION['error'] = 'Passwords do not match.';
            header('Location: ' . BASE_URL . 'reset-password?token=' . $token);
            exit;
        }

        if (User::resetPassword($token, $password)) {
            $_SESSION['success'] = 'Password reset successful. Please login.';
            header('Location: ' . BASE_URL . 'login');
            exit;
        } else {
            $_SESSION['error'] = 'Invalid or expired token.';
            header('Location: ' . BASE_URL . 'reset-password?token=' . $token);
            exit;
        }
    }

    private static function verifyCaptcha($response) {
        // Implement reCAPTCHA verification
        $secret = '6LeIxAcTAAAAAGG-vFI1TnRWxMZNFuojJ4WifJWe'; // Test secret key
        $url = 'https://www.google.com/recaptcha/api/siteverify';
        $data = ['secret' => $secret, 'response' => $response];
        $options = ['http' => ['header' => "Content-type: application/x-www-form-urlencoded\r\n", 'method' => 'POST', 'content' => http_build_query($data)]];
        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        $json = json_decode($result);
        return $json->success;
    }

    private static function sendOTP($email, $otp) {
        // Temporarily store OTP for testing and show it on the OTP page.
        // In production, replace this with proper email/SMS delivery.
        $_SESSION['test_otp_message'] = "OTP for $email: $otp";
        return true;
    }

    public static function resendOTP() {
        // Check if user is in OTP verification process
        if (!isset($_SESSION['temp_user_id']) && !isset($_SESSION['temp_email'])) {
            $_SESSION['error'] = 'Please complete registration first.';
            header('Location: ' . BASE_URL . 'register');
            exit;
        }

        $userId = $_SESSION['temp_user_id'] ?? null;
        $email = $_SESSION['temp_email'] ?? null;

        // If no userId but we have email, try to find the user
        if (!$userId && $email) {
            $user = User::findByEmail($email);
            if ($user) {
                $userId = $user['id'];
                $_SESSION['temp_user_id'] = $userId;
            }
        }

        if (!$userId) {
            $_SESSION['error'] = 'Session expired. Please register again.';
            header('Location: ../public/auth_router.php?action=register');
            exit;
        }

        // Generate new OTP
        $otp = User::generateOTP($userId);
        
        if (!$email) {
            $user = User::findById($userId);
            $email = $user['email'];
            $_SESSION['temp_email'] = $email;
        }

        // Send OTP (simulate email)
        self::sendOTP($email, $otp);

        $_SESSION['test_otp'] = $otp; // temporary testing support
        $_SESSION['success'] = 'OTP has been resent to your email. Valid for 30 minutes.';
        header('Location: ' . BASE_URL . 'verify-otp');
        exit;
    }

    private static function sendResetEmail($email, $token) {
        $resetLink = 'http://' . $_SERVER['HTTP_HOST'] . '/BusTicketingSystem/public/auth_router.php?action=resetPassword&token=' . $token;
        $subject = 'Password Reset for Bus Ticketing System';
        $message = "Click here to reset your password: $resetLink";
        mail($email, $subject, $message);
    }
}