<?php
require_once 'config/init.php';
require_once 'models/Database.php';

try {
    $db = Database::connect();

    // Clear out the old accounts first
    $db->exec("DELETE FROM users WHERE username IN ('admin', 'Lee', 'Admin', 'lee')");

    // The 2026 passwords you requested
    $passAdmin = password_hash('AdminSecure!2026', PASSWORD_DEFAULT);
    $passLee = password_hash('LeeSecure!2026', PASSWORD_DEFAULT);

    // Insert the accounts
    $stmt = $db->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
    $stmt->execute(['admin', $passAdmin, 'owner']);
    $stmt->execute(['Lee', $passLee, 'user']);

    echo "<div style='font-family: sans-serif; padding: 40px; text-align: center;'>";
    echo "<h1 style='color: green;'>? Database Rebuilt with 2026 Passwords!</h1>";
    echo "<ul style='list-style: none; padding: 0;'>";
    echo "<li><strong>Manager/Owner:</strong> admin / AdminSecure!2026</li>";
    echo "<li><strong>Browsing User:</strong> Lee / LeeSecure!2026</li>";
    echo "</ul>";
    echo "<a href='index.php?controller=auth&action=login' style='padding: 10px 20px; background: #0d6efd; color: white; text-decoration: none; border-radius: 5px; display: inline-block; margin-top: 20px;'>Go to Login</a>";
    echo "</div>";

} catch (Exception $e) {
    echo "Error seeding database: " . $e->getMessage();
}
