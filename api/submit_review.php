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
        $customer_id = $_SESSION['user_id'];
        $booking_id = $data['booking_id'];
        $provider_id = $data['provider_id'];
        $rating = (int)$data['rating'];
        $comment = $data['comment'];

        // 1. Insert the new review
        $stmt = $pdo->prepare("INSERT INTO reviews (booking_id, provider_id, customer_id, rating, comment) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$booking_id, $provider_id, $customer_id, $rating, $comment]);

        // 2. Change the booking status to 'completed'
        $stmt2 = $pdo->prepare("UPDATE bookings SET status = 'completed' WHERE id = ?");
        $stmt2->execute([$booking_id]);

        // 3. THE MATH: Calculate the new average rating for this provider
        $stmt3 = $pdo->prepare("SELECT AVG(rating) as avg_rating, COUNT(id) as total_reviews FROM reviews WHERE provider_id = ?");
        $stmt3->execute([$provider_id]);
        $stats = $stmt3->fetch();

        $new_avg = round($stats['avg_rating'], 1);
        $new_count = $stats['total_reviews'];

        // 4. Update the provider's public profile with the new math!
        $stmt4 = $pdo->prepare("UPDATE provider_profiles SET rating = ?, reviews_count = ? WHERE user_id = ?");
        $stmt4->execute([$new_avg, $new_count, $provider_id]);

        echo json_encode(['success' => true]);

    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => 'Database error.']);
    }
}
?>