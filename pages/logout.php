<?php
unset($_SESSION['user']);

session_unset();
session_destroy();

echo '<script>window.location.href = "../index.php";</script>';
exit;
?>
