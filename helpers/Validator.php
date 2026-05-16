<?php
class Validator {
    // Clean up user input to stop people from entering malicious code into the database
    public static function sanitize($data) {
        return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
    }

    // Make sure map coordinates are actually numbers so the map markers don't break
    public static function isValidCoord($coord) { return is_numeric($coord); }

    // Simple check to make sure a required form field isn't empty
    public static function required($data) { return !empty(trim($data)); }
}
