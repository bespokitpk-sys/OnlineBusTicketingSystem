<?php
require_once APP_ROOT . '/config/db.php';

class User {
    public static function findByEmail(string $email) {
        global $conn;
        $email = $conn->real_escape_string($email);
        $result = $conn->query("SELECT * FROM users WHERE email = '$email' LIMIT 1");
        return $result ? $result->fetch_assoc() : null;
    }

    public static function findById(int $id) {
        global $conn;
        $result = $conn->query("SELECT * FROM users WHERE id = $id LIMIT 1");
        return $result ? $result->fetch_assoc() : null;
    }

    public static function findByResetToken(string $token) {
        global $conn;
        $token = $conn->real_escape_string($token);
        $result = $conn->query("SELECT * FROM users WHERE reset_token = '$token' AND reset_expiry > NOW() LIMIT 1");
        return $result ? $result->fetch_assoc() : null;
    }

    public static function create(string $name, string $email, string $phone, string $password, string $role = 'passenger', bool $verified = false, string $cnic = null, string $profilePicture = null) {
        global $conn;
        $name = $conn->real_escape_string($name);
        $email = $conn->real_escape_string($email);
        $phone = $conn->real_escape_string($phone);
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $role = $conn->real_escape_string($role);
        $isVerified = $verified ? 1 : 0;
        
        // Handle optional CNIC and profile picture
        $cnicValue = $cnic ? "'" . $conn->real_escape_string($cnic) . "'" : 'NULL';
        $profilePicValue = $profilePicture ? "'" . $conn->real_escape_string($profilePicture) . "'" : 'NULL';
        
        $conn->query("INSERT INTO users (name, email, phone, cnic, profile_picture, password_hash, role, is_verified) VALUES ('$name', '$email', '$phone', $cnicValue, $profilePicValue, '$hash', '$role', $isVerified)");
        return $conn->insert_id;
    }

    public static function authenticate(string $email, string $password) {
        $user = self::findByEmail($email);
        if ($user && password_verify($password, $user['password_hash']) && $user['is_verified']) {
            return $user;
        }
        return null;
    }

    public static function generateOTP(int $userId) {
        global $conn;
        $otp = rand(100000, 999999);
        $expiry = date('Y-m-d H:i:s', strtotime('+30 minutes'));
        $conn->query("UPDATE users SET otp_code = '$otp', otp_expiry = '$expiry' WHERE id = $userId");
        return $otp;
    }

    public static function verifyOTP(int $userId, string $otp) {
        global $conn;
        $otp = $conn->real_escape_string($otp);
        
        // First, check if OTP exists and matches
        $result = $conn->query("SELECT otp_code, otp_expiry FROM users WHERE id = $userId LIMIT 1");
        
        if (!$result) {
            return false;
        }
        
        $user = $result->fetch_assoc();
        
        if (!$user || !$user['otp_code']) {
            return false;
        }
        
        // Check if OTP matches
        if ($user['otp_code'] != $otp) {
            return false;
        }
        
        // Check if OTP is expired
        if (strtotime($user['otp_expiry']) < time()) {
            return false;
        }
        
        // OTP is valid - mark user as verified
        $conn->query("UPDATE users SET is_verified = 1, otp_code = NULL, otp_expiry = NULL WHERE id = $userId");
        return true;
    }

    public static function generateResetToken(string $email) {
        global $conn;
        $user = self::findByEmail($email);
        if (!$user) return false;
        $token = bin2hex(random_bytes(32));
        $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
        $conn->query("UPDATE users SET reset_token = '$token', reset_expiry = '$expiry' WHERE id = {$user['id']}");
        return $token;
    }

    public static function resetPassword(string $token, string $newPassword) {
        global $conn;
        $user = self::findByResetToken($token);
        if (!$user) return false;
        $hash = password_hash($newPassword, PASSWORD_DEFAULT);
        $conn->query("UPDATE users SET password_hash = '$hash', reset_token = NULL, reset_expiry = NULL WHERE id = {$user['id']}");
        return true;
    }
}
