<?php
// Saves a completed-service review and refreshes provider rating stats.
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
        $booking_id = (int)($data['booking_id'] ?? 0);
        $provider_id = (int)($data['provider_id'] ?? 0);
        $rating = (int)($data['rating'] ?? 0);
        $comment = trim($data['comment'] ?? '');

        if ($booking_id <= 0 || $provider_id <= 0 || $rating < 1 || $rating > 5) {
            echo json_encode(['success' => false, 'error' => 'Invalid review payload.']);
            exit;
        }
        $booking_stmt = $pdo->prepare("SELECT id FROM bookings WHERE id = ? AND customer_id = ? AND provider_id = ? AND status = 'confirmed'");
        $booking_stmt->execute([$booking_id, $customer_id, $provider_id]);
        if (!$booking_stmt->fetch()) {
            echo json_encode(['success' => false, 'error' => 'Review is only allowed for your confirmed bookings.']);
            exit;
        }
        $dup_stmt = $pdo->prepare("SELECT id FROM reviews WHERE booking_id = ? AND customer_id = ? LIMIT 1");
        $dup_stmt->execute([$booking_id, $customer_id]);
        if ($dup_stmt->fetch()) {
            echo json_encode(['success' => false, 'error' => 'You have already reviewed this booking.']);
            exit;
        }
        $stmt = $pdo->prepare("INSERT INTO reviews (booking_id, provider_id, customer_id, rating, comment) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$booking_id, $provider_id, $customer_id, $rating, $comment]);
        $stmt2 = $pdo->prepare("UPDATE bookings SET status = 'completed' WHERE id = ? AND customer_id = ? AND provider_id = ?");
        $stmt2->execute([$booking_id, $customer_id, $provider_id]);
        $stmt3 = $pdo->prepare("SELECT AVG(rating) as avg_rating, COUNT(id) as total_reviews FROM reviews WHERE provider_id = ?");
        $stmt3->execute([$provider_id]);
        $stats = $stmt3->fetch();

        $new_avg = round($stats['avg_rating'], 1);
        $new_count = $stats['total_reviews'];
        // Keep provider profile aggregate columns in sync after each review.
        $stmt4 = $pdo->prepare("UPDATE provider_profiles SET rating = ?, reviews_count = ? WHERE user_id = ?");
        $stmt4->execute([$new_avg, $new_count, $provider_id]);

        echo json_encode(['success' => true]);

    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => 'Database error.']);
    }
}
?>