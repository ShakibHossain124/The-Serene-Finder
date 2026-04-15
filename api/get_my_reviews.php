<?php
// Returns reviews based on role: providers get received reviews, customers get submitted reviews.
session_start();
require_once '../db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['loggedIn' => false]);
    exit;
}

try {
    $user_id = $_SESSION['user_id'];
    $stmt = $pdo->prepare("SELECT role FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $role = $stmt->fetchColumn();

    if ($role === 'provider') {
        // Reviews written by customers about this provider.
        $query = "SELECT r.*, b.estimated_time, b.total_price, u.full_name as other_name FROM reviews r 
                  JOIN users u ON r.customer_id = u.id 
                  LEFT JOIN bookings b ON r.booking_id = b.id 
                  WHERE r.provider_id = ? ORDER BY r.created_at DESC";
    } else {
        // Reviews this customer has written for providers.
        $query = "SELECT r.*, b.estimated_time, b.total_price, u.full_name as other_name FROM reviews r 
                  JOIN users u ON r.provider_id = u.id 
                  LEFT JOIN bookings b ON r.booking_id = b.id 
                  WHERE r.customer_id = ? ORDER BY r.created_at DESC";
    }

    $stmt_revs = $pdo->prepare($query);
    $stmt_revs->execute([$user_id]);
    $reviews = $stmt_revs->fetchAll();

    echo json_encode(['loggedIn' => true, 'role' => $role, 'reviews' => $reviews]);
} catch (Exception $e) {
    echo json_encode(['loggedIn' => false, 'error' => 'Database error']);
}
?>