<?php
session_start();
header('Content-Type: application/json');

// Check if the server's session memory has a user ID
if (isset($_SESSION['user_id'])) {
    // They are logged in! Send back a thumbs up.
    echo json_encode(['loggedIn' => true]);
} else {
    // Nobody is logged in.
    echo json_encode(['loggedIn' => false]);
}
?>