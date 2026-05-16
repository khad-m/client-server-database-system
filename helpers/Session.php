<?php
class Session {
    // Make sure the session is actually running before we try to use it
    public static function init() {
        if (session_status() == PHP_SESSION_NONE) session_start();
    }

    // Save a piece of data into the user's session
    public static function set($key, $value) { $_SESSION[$key] = $value; }

    // Grab a specific piece of data from the session
    public static function get($key) { return $_SESSION[$key] ?? null; }

    // Quick check to see if anyone is logged in right now
    public static function isLoggedIn() { return isset($_SESSION['user_id']); }

    // Wipe the session data when a user wants to log out
    public static function logout() { session_unset(); session_destroy(); }

    // Check if the logged-in user is an 'admin'
    public static function isAdmin() {
        return (isset($_SESSION['role']) && $_SESSION['role'] === 'admin');
    }
}
