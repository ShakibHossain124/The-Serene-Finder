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
        $provider_id = (int)($data['provider_id'] ?? 0);
        $issue = trim($data['issue_description'] ?? '');
        $date_input = trim($data['date'] ?? '');
        $time_window = trim($data['time_window'] ?? '');
        $address_line = trim($data['address'] ?? '');
        $city = trim($data['city'] ?? '');
        $zip = trim($data['zip'] ?? '');
        $estimated_time = isset($data['estimated_time']) ? (float)$data['estimated_time'] : 0.0;

        if ($provider_id <= 0 || $provider_id === (int)$customer_id) {
            echo json_encode(['success' => false, 'error' => 'Invalid provider selected.']);
            exit;
        }

        if ($date_input === '' || $time_window === '' || $address_line === '' || $city === '' || $zip === '') {
            echo json_encode(['success' => false, 'error' => 'Please fill all booking fields.']);
            exit;
        }

        if ($estimated_time <= 0) {
            echo json_encode(['success' => false, 'error' => 'Estimated time must be greater than 0.']);
            exit;
        }

        $date_obj = DateTime::createFromFormat('Y-m-d', $date_input);
        if (!$date_obj || $date_obj->format('Y-m-d') !== $date_input) {
            echo json_encode(['success' => false, 'error' => 'Invalid booking date.']);
            exit;
        }

        $time_map = [
            'Morning (8:00 AM - 12:00 PM)' => '08:00:00',
            'Afternoon (12:00 PM - 4:00 PM)' => '12:00:00'
        ];

        if (isset($time_map[$time_window])) {
            $time_part = $time_map[$time_window];
        } elseif (preg_match('/^([01]\\d|2[0-3]):[0-5]\\d(:[0-5]\\d)?$/', $time_window)) {
            $time_part = strlen($time_window) === 5 ? $time_window . ':00' : $time_window;
        } else {
            echo json_encode(['success' => false, 'error' => 'Invalid time window.']);
            exit;
        }

        $scheduled_date = $date_input . ' ' . $time_part;
        $address = $address_line . ', ' . $city . ' ' . $zip;

        // Get provider's hourly rate to calculate total price
        $providerStmt = $pdo->prepare("SELECT hourly_rate FROM provider_profiles WHERE user_id = ?");
        $providerStmt->execute([$provider_id]);
        $provider = $providerStmt->fetch();
        if (!$provider) {
            echo json_encode(['success' => false, 'error' => 'Selected provider profile not found.']);
            exit;
        }
        
        $hourly_rate = (float)$provider['hourly_rate'];
        $travel_fee = 25.0;
        $total_price = round(($estimated_time * $hourly_rate) + $travel_fee, 2);

        $stmt = $pdo->prepare("INSERT INTO bookings (customer_id, provider_id, issue_description, scheduled_date, address, estimated_time, total_price) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$customer_id, $provider_id, $issue, $scheduled_date, $address, $estimated_time, $total_price]);

        echo json_encode(['success' => true, 'message' => 'Booking confirmed!']);
    } catch (Exception $e) {
        // 3. Send the exact database error back so we can see what went wrong
        echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'No data received.']);
}
?>