<?php
// Start the session so we can keep track of 'admin' or 'Lee' as they use the site
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Create a shortcut to the root folder so the app finds its files correctly on the Poseidon server
define('BASE_PATH', dirname(__DIR__) . '/');
