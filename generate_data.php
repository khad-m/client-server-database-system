<?php
require_once 'config/init.php';
require_once 'models/Database.php';

try {
    $db = Database::connect();

    // Fetch users using exact casing
    $admin = $db->query("SELECT id FROM users WHERE username = 'admin'")->fetch();
    $lee = $db->query("SELECT id FROM users WHERE username = 'Lee'")->fetch();

    if (!$admin || !$lee) {
        die("Error: Please run seed.php first to create the 'admin' and 'Lee' accounts.");
    }

    $types = ['Dog', 'Cat', 'Bird', 'Other'];
    $statuses = ['missing', 'found'];
    $adjectives = ['Clear', 'Distinctive', 'Large', 'Small', 'Active', 'Calm', 'Young', 'Senior'];
    $colors = ['Black', 'White', 'Brown', 'Grey', 'Golden', 'Mixed'];
    
    // 105 Common Pet Names
    $petNames = [
        "Bella", "Luna", "Charlie", "Max", "Lucy", "Cooper", "Bailey", "Daisy", "Sadie", "Molly", 
        "Buddy", "Lola", "Stella", "Tucker", "Bentley", "Zoey", "Harley", "Maggie", "Riley", "Bear", 
        "Sophie", "Chloe", "Jack", "Penny", "Milo", "Oliver", "Toby", "Zeus", "Nala", "Murphy", 
        "Ruby", "Rosie", "Buster", "Dexter", "Winston", "Mickey", "Oscar", "Louie", "Finn", "Duke", 
        "Bandit", "Rocky", "Simba", "Jax", "Coco", "Leo", "Loki", "Jasper", "Ollie", "Koda", 
        "Diesel", "Gizmo", "Shadow", "Romeo", "Tyson", "Frankie", "Sammy", "Ziggy", "Chester", "Oreo", 
        "Peanut", "Archie", "Sparky", "Chase", "Maya", "Abby", "Winnie", "Hazel", "Lexi", "Pepper", 
        "Princess", "Lily", "Fiona", "Roxy", "Ellie", "Mia", "Athena", "Harper", "Dixie", "Piper", 
        "Cleo", "Pearl", "Willow", "Zara", "Mila", "Kali", "Nova", "Maci", "Delilah", "Gigi", 
        "Ivy", "Josie", "Kiki", "Lulu", "Nola", "Quinn", "Sasha", "Tilly", "Uma", "Veda", 
        "Xena", "Yuki", "Zelda", "Apollo", "Boomer"
    ];

    // Shuffle the names to make the distribution random
    shuffle($petNames);

    echo "<div style='font-family:sans-serif; padding:20px;'>";
    echo "<h2>Generating 105 Professional Records...</h2>";
    
    $db->beginTransaction();

    // Loop exactly 105 times
    for ($i = 0; $i < 105; $i++) {
        $type = $types[array_rand($types)];
        $status = $statuses[array_rand($statuses)];
        $name = $petNames[$i]; // Pulls a unique name from our shuffled list
        
        $desc = "A " . $adjectives[array_rand($adjectives)] . " " . strtolower($colors[array_rand($colors)]) . " " . strtolower($type) . ". No collar visible.";

        $stmt = $db->prepare("INSERT INTO pets (owner_id, name, type, description, status) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$admin['id'], $name, $type, $desc, $status]);
        $petId = $db->lastInsertId();
        
        // Generate random coordinates around Manchester
        $lat = 53.45 + (lcg_value() * 0.07);
        $lon = -2.32 + (lcg_value() * 0.12);
        
        $numSightings = rand(1, 2);

        for ($s = 0; $s < $numSightings; $s++) {
            $stmt = $db->prepare("INSERT INTO sightings (pet_id, user_id, lat, lon, location, note) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $petId, 
                $lee['id'], 
                $lat + (lcg_value() * 0.005), 
                $lon + (lcg_value() * 0.005),
                "Location Area M" . rand(1, 50),
                "Observed moving toward the local perimeter."
            ]);
        }
    }
    
    $db->commit();

    echo "<h3 style='color: green;'>? Success: 105 realistic records injected.</h3>";
    echo "<a href='index.php?controller=pet&action=map' style='text-decoration:none; color:blue;'>View the Live Map</a>";
    echo "</div>";

} catch (Exception $e) {
    if (isset($db)) $db->rollBack();
    echo "Critical Error: " . $e->getMessage();
}
