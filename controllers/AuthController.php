<?php
require_once 'models/Database.php';

class AuthController {
    public function login() {
        if (isset($_SESSION['user_id'])) {
            header('Location: index.php?controller=pet&action=map');
            exit;
        }

        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';

            if (empty($username) || empty($password)) {
                $error = "Please enter both username and password.";
            } else {
                try {
                    $db = Database::connect();
                    
                    $stmt = $db->prepare("SELECT * FROM users WHERE username = :username");
                    $stmt->execute([':username' => $username]);
                    $user = $stmt->fetch();

                    if ($user && $user['username'] === $username && password_verify($password, $user['password'])) {
                        session_regenerate_id(true);
                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['username'] = $user['username'];
                        $_SESSION['role'] = $user['role'];
                        header('Location: index.php?controller=pet&action=map');
                        exit;
                    } else {
                        // EXACT message requested by user
                        $error = "Invalid username or password.";
                    }
                } catch (Exception $e) {
                    $error = "A system error occurred. Please try again later.";
                }
            }
        }
        require_once 'views/auth/login.phtml';
    }

    public function logout() {
        $_SESSION = [];
        if (session_id() != '' || isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 2592000, '/');
        }
        session_destroy();
        header('Location: index.php?controller=home&action=index');
        exit;
    }
}
