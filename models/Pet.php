<?php
require_once 'models/Database.php';

class Pet {
    // Advanced Search Method
    public static function search($keyword = '', $type = '', $status = '', $limit = 6, $offset = 0) {
        $db = Database::connect();
        
        $sql = "SELECT pets.*, users.username as owner_name 
                FROM pets 
                JOIN users ON pets.owner_id = users.id 
                WHERE 1=1";
        
        $params = [];

        if (!empty($keyword)) {
            $sql .= " AND (pets.name LIKE :keyword OR pets.description LIKE :keyword)";
            $params[':keyword'] = '%' . $keyword . '%';
        }
        if (!empty($type)) {
            $sql .= " AND pets.type = :type";
            $params[':type'] = $type;
        }
        if (!empty($status)) {
            $sql .= " AND pets.status = :status";
            $params[':status'] = $status;
        }

        $sql .= " ORDER BY pets.status ASC, pets.id DESC LIMIT :limit OFFSET :offset";

        $stmt = $db->prepare($sql);
        
        // Bind parameters securely
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Get total count for pagination math
    public static function countSearch($keyword = '', $type = '', $status = '') {
        $db = Database::connect();
        $sql = "SELECT COUNT(*) as total FROM pets WHERE 1=1";
        $params = [];

        if (!empty($keyword)) {
            $sql .= " AND (name LIKE :keyword OR description LIKE :keyword)";
            $params[':keyword'] = '%' . $keyword . '%';
        }
        if (!empty($type)) {
            $sql .= " AND type = :type";
            $params[':type'] = $type;
        }
        if (!empty($status)) {
            $sql .= " AND status = :status";
            $params[':status'] = $status;
        }

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $row = $stmt->fetch();
        return $row['total'];
    }

    // Keep the core CRUD methods
    public static function getById($id) {
        $db = Database::connect();
        $stmt = $db->prepare("SELECT * FROM pets WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public static function create($owner_id, $name, $type, $description, $status) {
        $db = Database::connect();
        $stmt = $db->prepare("INSERT INTO pets (owner_id, name, type, description, status) VALUES (:owner_id, :name, :type, :description, :status)");
        return $stmt->execute([':owner_id' => $owner_id, ':name' => $name, ':type' => $type, ':description' => $description, ':status' => $status]);
    }

    public static function update($id, $name, $type, $description, $status) {
        $db = Database::connect();
        $stmt = $db->prepare("UPDATE pets SET name = :name, type = :type, description = :description, status = :status WHERE id = :id");
        return $stmt->execute([':id' => $id, ':name' => $name, ':type' => $type, ':description' => $description, ':status' => $status]);
    }

    public static function delete($id) {
        $db = Database::connect();
        $stmt = $db->prepare("DELETE FROM pets WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
}
