<?php
session_start(); // Start the session

// Unset the PreCart session variable
if (isset($_SESSION['PreCart'])) {
    unset($_SESSION['PreCart']);
}
?>
