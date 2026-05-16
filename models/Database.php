<?php
class Database {
    private static $pdo = null;

    public static function connect() {
        if (self::$pdo === null) {
            try {
                // Use the BASE_PATH shortcut to find the file on the Poseidon server
                // We are using 'petwatch.sqlite' as requested
                $dbPath = BASE_PATH . 'db/petwatch.sqlite';
                
                self::$pdo = new PDO("sqlite:" . $dbPath);
                
                // Turn on foreign keys so our table relationships actually work
                self::$pdo->exec('PRAGMA foreign_keys = ON;');
                
                // Set error modes so we can catch database issues while developing
                self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
                
                // Ensure the tables exist in the petwatch file
                self::initTables();
            } catch (PDOException $e) {
                // If the connection fails, stop the app and show the error
                die("Database Connection Error: " . $e->getMessage());
            }
        }
        return self::$pdo;
    }

    private static function initTables() {
        $db = self::$pdo;

        // Create the users table for the mandatory 'admin' and 'Lee' accounts
        $db->exec("CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT, 
            username TEXT UNIQUE NOT NULL, 
            password TEXT NOT NULL, 
            role TEXT NOT NULL)");

        // Create the main pets table
        $db->exec("CREATE TABLE IF NOT EXISTS pets (
            id INTEGER PRIMARY KEY AUTOINCREMENT, 
            owner_id INTEGER NOT NULL, 
            name TEXT NOT NULL, 
            type TEXT NOT NULL, 
            description TEXT, 
            status TEXT DEFAULT 'missing', 
            FOREIGN KEY (owner_id) REFERENCES users(id))");

        // Create the sightings table with coordinates for the Mapping requirement
        $db->exec("CREATE TABLE IF NOT EXISTS sightings (
            id INTEGER PRIMARY KEY AUTOINCREMENT, 
            pet_id INTEGER NOT NULL, 
            user_id INTEGER NOT NULL, 
            lat REAL, 
            lon REAL, 
            location TEXT NOT NULL, 
            note TEXT, 
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP, 
            FOREIGN KEY (pet_id) REFERENCES pets(id), 
            FOREIGN KEY (user_id) REFERENCES users(id))");
    }
}
