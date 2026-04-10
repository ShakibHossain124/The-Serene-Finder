<?php
session_start();
session_unset();    // Remove all session variables
session_destroy();  // Destroy the session completely
echo json_encode(['success' => true]);
?>