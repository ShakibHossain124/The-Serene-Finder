<?php
// Returns public provider profile details and recent reviews for a given provider ID.
require_once '../db.php';
header('Content-Type: application/json');
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    try {
        $stmt = $pdo->prepare("
            SELECT u.id, u.full_name, p.specialty, p.hourly_rate, p.rating, p.reviews_count, p.bio, p.location 
            FROM users u 
            JOIN provider_profiles p ON u.id = p.user_id 
            WHERE u.id = ? AND u.role = 'provider'
        ");
        $stmt->execute([$id]);
        $profile = $stmt->fetch();
        
        if ($profile) {
            // Attach latest reviews so the profile page can render social proof.
            $stmt_reviews = $pdo->prepare("
                SELECT r.rating, r.comment, r.created_at, u.full_name as reviewer_name 
                FROM reviews r 
                JOIN users u ON r.customer_id = u.id 
                WHERE r.provider_id = ? 
                ORDER BY r.created_at DESC
            ");
            $stmt_reviews->execute([$id]);
            $profile['recent_reviews'] = $stmt_reviews->fetchAll();
            echo json_encode($profile);
        } else {
            echo json_encode(['error' => 'Profile not found.']);
        }
    } catch (Exception $e) {
        echo json_encode(['error' => 'Database error.']);
    }
} else {
    echo json_encode(['error' => 'Invalid ID.']);
}
?>