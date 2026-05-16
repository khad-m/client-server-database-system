<?php
require_once 'models/Pet.php';
require_once 'models/Sighting.php';

class ApiController {
    
    // Set headers to always return JSON
    private function setJsonHeader() {
        header('Content-Type: application/json');
    }

    // Fetch pets for the map/list
    public function getPets() {
        $this->setJsonHeader();
        
        $keyword = $_GET['keyword'] ?? '';
        $type = $_GET['type'] ?? '';
        $status = $_GET['status'] ?? '';
        
        try {
            $pets = Pet::search($keyword, $type, $status, 500, 0);

            foreach($pets as &$pet) {
                $sightings = Sighting::getByPetId($pet['id']);
                $pet['sightings'] = $sightings;
                if(!empty($sightings)) {
                    $pet['latest_lat'] = $sightings[0]['lat'];
                    $pet['latest_lon'] = $sightings[0]['lon'];
                }
            }
            
            echo json_encode(['status' => 'success', 'data' => $pets]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    // Add a new sighting via AJAX
    public function addSighting() {
        $this->setJsonHeader();
        
        // Must be logged in to leave a sighting
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['status' => 'error', 'message' => 'You must be logged in to add a sighting.']);
            return;
        }

        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        if (!$data || !isset($data['pet_id'], $data['lat'], $data['lon'], $data['location'])) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Invalid data payload.']);
            return;
        }

        try {
            $pet_id = (int)$data['pet_id'];
            $lat = (float)$data['lat'];
            $lon = (float)$data['lon'];
            $location = htmlspecialchars(trim($data['location']));
            $note = htmlspecialchars(trim($data['note'] ?? ''));

            Sighting::create($pet_id, $_SESSION['user_id'], $lat, $lon, $location, $note);
            
            echo json_encode(['status' => 'success', 'message' => 'Sighting added successfully.']);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Database error.']);
        }
    }
}
