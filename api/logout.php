<?php
session_start();
header('Content-Type: application/json');
session_unset();    // Remove all session variables
session_destroy();  // Destroy the session completely
echo json_encode(['success' => true]);
?>