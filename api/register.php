<?php
require_once '../db.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if ($data) {
    $name = trim($data['full_name'] ?? '');
    $email = trim($data['email'] ?? '');
    $raw_password = $data['password'] ?? '';
    $password = password_hash($raw_password, PASSWORD_DEFAULT); // Secure hashing
    $role = $data['role'] ?? ''; // 'customer' or 'provider'

    if ($name === '' || $email === '' || $raw_password === '' || !in_array($role, ['customer', 'provider'], true)) {
        echo json_encode(['success' => false, 'error' => 'Invalid registration data.']);
        exit;
    }

    try {
        // Check if email exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            echo json_encode(['success' => false, 'error' => 'Email already registered.']);
            exit;
        }

        // Insert new user
        $stmt = $pdo->prepare("INSERT INTO users (full_name, email, password_hash, role) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $email, $password, $role]);
        $new_user_id = $pdo->lastInsertId();

        // If they are a provider, create a placeholder profile aligned with current schema
        if ($role === 'provider') {
            $stmt = $pdo->prepare("INSERT INTO provider_profiles (user_id, specialty, hourly_rate, location) VALUES (?, 'Pending Specialty', 0.00, 'Pending Location')");
            $stmt->execute([$new_user_id]);
        }

        echo json_encode(['success' => true, 'message' => 'Account created successfully!']);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => 'Database error.']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'No data received.']);
}
?>