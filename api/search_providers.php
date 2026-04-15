<?php
// Performs filtered provider search for the directory page.
require_once '../db.php';
header('Content-Type: application/json');
$search = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';
$location = $_GET['location'] ?? '';
$max_price = isset($_GET['max_price']) && $_GET['max_price'] !== '' ? (float)$_GET['max_price'] : 1000;
$query = "
    SELECT u.id, u.full_name, p.specialty, p.category, p.hourly_rate, p.rating, p.reviews_count, p.location 
    FROM users u 
    JOIN provider_profiles p ON u.id = p.user_id 
    WHERE u.role = 'provider'
";
$params = [];

// Search by provider name or specialty text.
if (!empty($search)) {
    $query .= " AND (u.full_name LIKE ? OR p.specialty LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if (!empty($category)) {
    $query .= " AND p.category LIKE ?";
    $params[] = "%$category%";
}

if (!empty($location)) {
    $query .= " AND p.location LIKE ?";
    $params[] = "%$location%";
}

$query .= " AND p.hourly_rate <= ?";
$params[] = $max_price;
$query .= " ORDER BY p.rating DESC";

try {
    // Execute with bound params to keep filtering safe.
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $providers = $stmt->fetchAll();
    
    echo json_encode(['success' => true, 'providers' => $providers]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Database error.']);
}
?>