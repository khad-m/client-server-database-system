<?php
// 1. Load configuration and session
require_once 'config/init.php';

// 2. Read the URL parameters (Default to 'home' and 'index' if none exist)
$controllerName = $_GET['controller'] ?? 'home';
$actionName = $_GET['action'] ?? 'index';

// 3. Format the controller class name (e.g., 'home' becomes 'HomeController')
$controllerClass = ucfirst($controllerName) . 'Controller';
$controllerFile = 'controllers/' . $controllerClass . '.php';

// 4. Route the request
if (file_exists($controllerFile)) {
    require_once $controllerFile;
    $controller = new $controllerClass();
    
    if (method_exists($controller, $actionName)) {
        // Execute the action (e.g., $controller->index())
        $controller->$actionName();
    } else {
        die("404 Error: Action '$actionName' not found in $controllerClass.");
    }
} else {
    die("404 Error: Controller '$controllerClass' not found.");
}
