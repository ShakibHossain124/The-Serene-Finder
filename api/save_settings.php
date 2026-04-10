<?php
session_start();
require_once '../db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Not logged in']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if ($data) {
    try {
        // Update the provider's profile in the database
        $stmt = $pdo->prepare("
            UPDATE provider_profiles 
            SET specialty = ?, category = ?, hourly_rate = ?, bio = ?, location = ? 
            WHERE user_id = ?
        ");
        
        $stmt->execute([
            $data['specialty'],
            $data['category'],  // <--- ADD THIS
            $data['hourly_rate'],
            $data['bio'],
            $data['location'],
            $_SESSION['user_id']
        ]);

        echo json_encode(['success' => true, 'message' => 'Profile updated successfully!']);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => 'Database error.']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'No data received.']);
}
?>