<?php
session_start();
require_once '../db.php';
header('Content-Type: application/json');

// 1. Make sure they are actually logged in!
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'You must be logged in to book an appointment.']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if ($data) {
    try {
        // 2. Use the REAL ID from the current session
        $customer_id = $_SESSION['user_id']; 
        $provider_id = $data['provider_id'];
        $issue = $data['issue_description'];
        $date = $data['date'] . ' ' . $data['time_window'];
        $address = $data['address'] . ', ' . $data['city'] . ' ' . $data['zip'];

        $stmt = $pdo->prepare("INSERT INTO bookings (customer_id, provider_id, issue_description, scheduled_date, address) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$customer_id, $provider_id, $issue, $date, $address]);

        echo json_encode(['success' => true, 'message' => 'Booking confirmed!']);
    } catch (Exception $e) {
        // 3. Send the exact database error back so we can see what went wrong
        echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'No data received.']);
}
?>