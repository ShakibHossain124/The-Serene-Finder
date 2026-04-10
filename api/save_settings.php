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
        $userId = $_SESSION['user_id'];
        $fullName = trim($data['full_name'] ?? '');

        if ($fullName === '') {
            echo json_encode(['success' => false, 'error' => 'Full name is required.']);
            exit;
        }

        // Always allow account name updates for both customer and provider
        $userStmt = $pdo->prepare("UPDATE users SET full_name = ? WHERE id = ?");
        $userStmt->execute([$fullName, $userId]);

        // Determine current role from database, not from client payload
        $roleStmt = $pdo->prepare("SELECT role FROM users WHERE id = ?");
        $roleStmt->execute([$userId]);
        $role = $roleStmt->fetchColumn();

        if ($role === 'provider') {
            $specialty = trim($data['specialty'] ?? '');
            $category = trim($data['category'] ?? '');
            $location = trim($data['location'] ?? '');
            $bio = trim($data['bio'] ?? '');
            $hourlyRate = isset($data['hourly_rate']) && $data['hourly_rate'] !== '' ? (float)$data['hourly_rate'] : null;

            if ($specialty === '' || $category === '' || $location === '' || $hourlyRate === null) {
                echo json_encode(['success' => false, 'error' => 'Please fill all provider profile fields.']);
                exit;
            }

            // Insert-or-update provider profile to avoid failing when row does not exist yet
            $providerStmt = $pdo->prepare(" 
                INSERT INTO provider_profiles (user_id, specialty, category, hourly_rate, bio, location)
                VALUES (?, ?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE
                    specialty = VALUES(specialty),
                    category = VALUES(category),
                    hourly_rate = VALUES(hourly_rate),
                    bio = VALUES(bio),
                    location = VALUES(location)
            ");

            $providerStmt->execute([
                $userId,
                $specialty,
                $category,
                $hourlyRate,
                $bio,
                $location
            ]);
        }

        echo json_encode(['success' => true, 'message' => 'Profile updated successfully!']);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => 'Database error.']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'No data received.']);
}
?>