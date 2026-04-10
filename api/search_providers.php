<?php
require_once '../db.php';
header('Content-Type: application/json');

// 1. Grab the filter values from the URL (default to empty/high if not provided)
$search = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';
$max_price = isset($_GET['max_price']) && $_GET['max_price'] !== '' ? (float)$_GET['max_price'] : 1000;

// 2. Start the base SQL query
$query = "
    SELECT u.id, u.full_name, p.specialty, p.category, p.hourly_rate, p.rating, p.reviews_count, p.location 
    FROM users u 
    JOIN provider_profiles p ON u.id = p.user_id 
    WHERE u.role = 'provider'
";
$params = [];

// 3. Dynamically add WHERE clauses based on what the user is searching for
if (!empty($search)) {
    // Search both their name AND their specialty
    $query .= " AND (u.full_name LIKE ? OR p.specialty LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if (!empty($category)) {
    $query .= " AND p.category LIKE ?";
    $params[] = "%$category%";
}

$query .= " AND p.hourly_rate <= ?";
$params[] = $max_price;

// 4. Sort by highest rated first!
$query .= " ORDER BY p.rating DESC";

try {
    // Execute the dynamic query safely
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $providers = $stmt->fetchAll();
    
    echo json_encode(['success' => true, 'providers' => $providers]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Database error.']);
}
?>