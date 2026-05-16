<?php
require_once 'models/Database.php';

class Sighting {
    public static function create($pet_id, $user_id, $lat, $lon, $location, $note) {
        $db = Database::connect();
        $stmt = $db->prepare("INSERT INTO sightings (pet_id, user_id, lat, lon, location, note) 
                              VALUES (:pet_id, :user_id, :lat, :lon, :location, :note)");
        return $stmt->execute([
            ':pet_id' => $pet_id,
            ':user_id' => $user_id, // We need to track who left the sighting
            ':lat' => $lat,
            ':lon' => $lon,
            ':location' => $location,
            ':note' => $note
        ]);
    }

    public static function getByPetId($pet_id) {
        $db = Database::connect();
        $stmt = $db->prepare("SELECT s.*, u.username 
                              FROM sightings s 
                              JOIN users u ON s.user_id = u.id 
                              WHERE s.pet_id = :pet_id 
                              ORDER BY s.created_at DESC");
        $stmt->execute([':pet_id' => $pet_id]);
        return $stmt->fetchAll();
    }
}
