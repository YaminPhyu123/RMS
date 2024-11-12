<?php
session_start();

// Check for action
if (isset($_POST['action']) && $_POST['action'] === 'clearPageBackCount') {
    unset($_SESSION['pagebackcount']);
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
}
?>
