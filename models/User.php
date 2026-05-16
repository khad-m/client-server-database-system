<?php
require_once 'models/Database.php';

class User {
    // Authenticate a user by verifying their hashed password
    public static function authenticate($username, $password) {
        $db = Database::connect();
        $stmt = $db->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->execute([':username' => $username]);
        $user = $stmt->fetch();

        // Verify the password against the stored hash
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    }
}
