<?php
require_once 'models/Pet.php';

class PetController {
    public function index() {
        $keyword = htmlspecialchars(trim($_GET['keyword'] ?? ''));
        $type = htmlspecialchars(trim($_GET['type'] ?? ''));
        $status = htmlspecialchars(trim($_GET['status'] ?? ''));

        $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
        $perPage = 6; // Display 6 pets per page
        $offset = ($page - 1) * $perPage;

        $totalPets = Pet::countSearch($keyword, $type, $status);
        $totalPages = ceil($totalPets / $perPage);
        $pets = Pet::search($keyword, $type, $status, $perPage, $offset);

        $queryString = http_build_query(['controller' => 'pet', 'action' => 'index', 'keyword' => $keyword, 'type' => $type, 'status' => $status]);

        require_once 'views/pets/index.phtml';
    }

    // Keep the Create, Edit, and Delete methods exactly the same as before

    // Load the Live Map View
    public function map() {
        require_once 'views/pets/map.phtml';
    }

    public function create() {
        if (!isset($_SESSION['user_id'])) { header('Location: index.php?controller=auth&action=login'); exit; }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Pet::create($_SESSION['user_id'], htmlspecialchars(trim($_POST['name'])), htmlspecialchars(trim($_POST['type'])), htmlspecialchars(trim($_POST['description'])), htmlspecialchars(trim($_POST['status'])));
            header('Location: index.php?controller=pet&action=index'); exit;
        }
        require_once 'views/pets/create.phtml';
    }

    public function edit() {
        if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) { header('Location: index.php?controller=pet&action=index'); exit; }
        $id = $_GET['id']; $pet = Pet::getById($id);
        if ($pet['owner_id'] !== $_SESSION['user_id']) { die("Security Error."); }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Pet::update($id, htmlspecialchars(trim($_POST['name'])), htmlspecialchars(trim($_POST['type'])), htmlspecialchars(trim($_POST['description'])), htmlspecialchars(trim($_POST['status'])));
            header('Location: index.php?controller=pet&action=index'); exit;
        }
        require_once 'views/pets/edit.phtml';
    }

    public function delete() {
        if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) { header('Location: index.php?controller=pet&action=index'); exit; }
        $id = $_GET['id']; $pet = Pet::getById($id);
        if ($pet['owner_id'] === $_SESSION['user_id']) { Pet::delete($id); }
        header('Location: index.php?controller=pet&action=index'); exit;
    }
}

