<?php
// Clears all session data to sign the user out.
session_start();
session_unset();
session_destroy();
echo json_encode(['success' => true]);
?>