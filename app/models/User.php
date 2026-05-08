<?php
require_once APP_ROOT . '/config/db.php';

class User {
    public static function findByEmail(string $email) {
        global $conn;
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result ? $result->fetch_assoc() : null;
        $stmt->close();
        return $user;
    }

    public static function findById(int $id) {
        global $conn;
        $stmt = $conn->prepare("SELECT * FROM users WHERE id = ? LIMIT 1");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result ? $result->fetch_assoc() : null;
        $stmt->close();
        return $user;
    }

    public static function findByResetToken(string $token) {
        global $conn;
        $stmt = $conn->prepare("SELECT * FROM users WHERE reset_token = ? AND reset_expiry > NOW() LIMIT 1");
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result ? $result->fetch_assoc() : null;
        $stmt->close();
        return $user;
    }

    public static function create(string $name, string $email, string $phone, string $password, string $role = 'passenger', bool $verified = false, string $cnic = null, string $profilePicture = null) {
        global $conn;
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $isVerified = $verified ? 1 : 0;
        
        $stmt = $conn->prepare("INSERT INTO users (name, email, phone, cnic, profile_picture, password_hash, role, is_verified) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssssi", $name, $email, $phone, $cnic, $profilePicture, $hash, $role, $isVerified);
        $stmt->execute();
        $insertId = $conn->insert_id;
        $stmt->close();
        return $insertId;
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
        
        $stmt = $conn->prepare("UPDATE users SET otp_code = ?, otp_expiry = ? WHERE id = ?");
        $stmt->bind_param("ssi", $otp, $expiry, $userId);
        $stmt->execute();
        $stmt->close();
        return $otp;
    }

    public static function verifyOTP(int $userId, string $otp) {
        global $conn;
        
        // First, check if OTP exists and matches
        $stmt = $conn->prepare("SELECT otp_code, otp_expiry FROM users WHERE id = ? LIMIT 1");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if (!$result) {
            $stmt->close();
            return false;
        }
        
        $user = $result->fetch_assoc();
        $stmt->close();
        
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
        $stmt = $conn->prepare("UPDATE users SET is_verified = 1, otp_code = NULL, otp_expiry = NULL WHERE id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $stmt->close();
        return true;
    }

    public static function generateResetToken(string $email) {
        global $conn;
        $user = self::findByEmail($email);
        if (!$user) return false;
        
        $token = bin2hex(random_bytes(32));
        $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
        
        $stmt = $conn->prepare("UPDATE users SET reset_token = ?, reset_expiry = ? WHERE id = ?");
        $stmt->bind_param("ssi", $token, $expiry, $user['id']);
        $stmt->execute();
        $stmt->close();
        return $token;
    }

    public static function resetPassword(string $token, string $newPassword) {
        global $conn;
        $user = self::findByResetToken($token);
        if (!$user) return false;
        
        $hash = password_hash($newPassword, PASSWORD_DEFAULT);
        
        $stmt = $conn->prepare("UPDATE users SET password_hash = ?, reset_token = NULL, reset_expiry = NULL WHERE id = ?");
        $stmt->bind_param("si", $hash, $user['id']);
        $stmt->execute();
        $stmt->close();
        return true;
    }
}
