<?php
session_start();
require_once '../db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['loggedIn' => false]);
    exit;
}

$user_id = $_SESSION['user_id'];

try {
    $stmt = $pdo->prepare("SELECT full_name, role FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();

    // 1. Fetch OUTBOUND bookings (Services this user hired someone else to do)
    $stmt_out = $pdo->prepare("
        SELECT b.id, b.provider_id, b.issue_description, b.scheduled_date, b.status, b.estimated_time, b.total_price, p.full_name as other_party 
        FROM bookings b JOIN users p ON b.provider_id = p.id 
        WHERE b.customer_id = ? ORDER BY b.scheduled_date DESC
    ");
    $stmt_out->execute([$user_id]);
    $outbound_bookings = $stmt_out->fetchAll();

    // 2. Fetch INBOUND jobs (Services this user was hired to do)
    $stmt_in = $pdo->prepare("
        SELECT b.id, b.issue_description, b.scheduled_date, b.status, b.address, b.estimated_time, b.total_price, c.full_name as other_party 
        FROM bookings b JOIN users c ON b.customer_id = c.id 
        WHERE b.provider_id = ? ORDER BY b.scheduled_date DESC
    ");
    $stmt_in->execute([$user_id]);
    $inbound_jobs = $stmt_in->fetchAll();

    // Send both arrays to the frontend!
    echo json_encode([
        'loggedIn' => true, 
        'user' => $user, 
        'my_bookings' => $outbound_bookings, 
        'job_offers' => $inbound_jobs
    ]);
} catch (Exception $e) {
    echo json_encode(['loggedIn' => false, 'error' => 'Database error.']);
}
?>