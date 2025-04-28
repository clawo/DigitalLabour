<?php
require_once '../includes/header.php';
unset($_SESSION['user']);

echo '<script>console.log("Session beendet.");</script>';

session_unset();
session_destroy();

echo '<script>window.location.href = "../index.php";</script>';
exit;
?>
