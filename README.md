# PetWatch: Full-Stack MVC Web Application

## Project Overview
PetWatch is a full-stack web application developed to log, track, and map missing pet sightings within a local area. Built as part of a client-server systems module at the University of Salford, the system uses an MVC (Model-View-Controller) architecture with an object-oriented PHP backend and an interactive JavaScript frontend, using an SQLite database for data persistence. 

The application is fully self-contained and configured for straightforward deployment on local Apache environments (like XAMPP) or remote Linux web servers.

## Architecture & System Design
The project avoids procedural scripting in favour of a structured, scalable application design:

* **MVC Separation:** Core logic is divided into distinct layers. Controllers handle request routing, Models manage data states and validation, and Views handle the presentation layout (`.phtml`).
* **Database Connection Handling:** The database management layer uses a static instance pattern within the `Database` class to keep database connections efficient and prevent connection leaks.
* **Relational Integrity:** Because SQLite does not enforce foreign keys by default, the database setup explicitly runs `PRAGMA foreign_keys = ON;` at runtime to ensure relational integrity across the user, pet, and sighting schemas.
* **Dynamic Table Initialisation:** The application automatically runs basic migrations (`initTables()`) upon connecting, verifying table structures without requiring external database configuration scripts.

## Technical Features & Implementation

### Live Search Throttling (`js/PetMapApp.js`)
To optimise performance and reduce server load, the live search bar uses a debouncing approach to delay the data fetch until the user stops typing:

```javascript
this.searchInput?.addEventListener('input', (e) => {
    clearTimeout(this.searchTimeout);
    this.searchTimeout = setTimeout(() => this.loadPets(e.target.value), 300);
});
```

### Session Integrity (controllers/AuthController.php)
The authentication workflow actively updates session variables during login states to verify access levels safely:

```php
if ($user && $user['username'] === $username && password_verify($password, $user['password'])) {
    session_regenerate_id(true);
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['role'] = $user['role'];
    header('Location: index.php?controller=pet&action=map');
    exit;
}
```

## Security & Data Handling Practices

* **MVC Separation:** Core logic is divided into distinct layers. Controllers handle request routing, Models manage data states and validation, and Views handle the presentation layout (`.phtml`).
* **Password Hashing:** Passwords are never handled or stored as plain text. The application uses native PHP hashing validation routines via password_verify to protect user credentials.
* **Session Rotation:** On a successful login, the application runs session_regenerate_id(true) to replace the current session token with a new one, protecting users against session fixation exploits.
* **Information Disclosure Controls:** Authentication scripts use uniform error responses ("Invalid username or password.") to prevent malicious actors from guessing valid usernames through unique feedback messages.

## Technology Stack
* **Backend Programming:** Object-Oriented PHP 8.x
* **Database Engine:** SQLite 3 via PHP Data Objects (PDO)
* **Frontend Architecture:** Vanilla ES6 JavaScript (Fetch API, DOM Manipulation)
* **Third-Party Libraries:** LeafletJS (Mapping Engine), Bootstrap 5 (Layout Framework)
