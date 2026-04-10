<?php
session_start();
require_once '../db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Not logged in']);
    exit;
}

try {
    // Fetch the user's current profile data
    $stmt = $pdo->prepare("
        SELECT u.full_name, p.specialty, p.category, p.hourly_rate, p.bio, p.location 
        FROM users u 
        LEFT JOIN provider_profiles p ON u.id = p.user_id 
        WHERE u.id = ?
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $profile = $stmt->fetch();

    echo json_encode(['success' => true, 'profile' => $profile]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Database error.']);
}
?>