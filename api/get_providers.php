<?php
// Returns all provider cards for simple listing pages.
require_once '../db.php';
header('Content-Type: application/json');

try {
    $stmt = $pdo->query("
        SELECT u.id, u.full_name, p.specialty, p.category , p.hourly_rate, p.rating 
        FROM users u 
        JOIN provider_profiles p ON u.id = p.user_id 
        WHERE u.role = 'provider'
    ");
    $providers = $stmt->fetchAll();
    echo json_encode($providers);
} catch (Exception $e) {
    echo json_encode(['error' => 'Could not fetch providers.']);
}
?>