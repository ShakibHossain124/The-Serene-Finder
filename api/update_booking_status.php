<?php
// Allows providers to accept or cancel pending booking requests.
session_start();
require_once '../db.php';
header('Content-Type: application/json');
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Not authenticated.']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if ($data) {
    try {
        $provider_id = $_SESSION['user_id']; 
        $booking_id = (int)($data['booking_id'] ?? 0);
        $new_status = $data['status'] ?? '';

        if ($booking_id <= 0 || !in_array($new_status, ['confirmed', 'cancelled'], true)) {
            echo json_encode(['success' => false, 'error' => 'Invalid booking status request.']);
            exit;
        }
        // Only pending bookings can transition via this endpoint.
        $stmt = $pdo->prepare("UPDATE bookings SET status = ? WHERE id = ? AND provider_id = ? AND status = 'pending'");
        $stmt->execute([$new_status, $booking_id, $provider_id]);
        if ($stmt->rowCount() > 0) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Booking not found or already updated.']);
        }

    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => 'Database error.']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'No data received.']);
}
?>