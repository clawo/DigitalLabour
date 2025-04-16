<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'includes/db_connect.php';

$db = getDB();

if ($db) {
    echo "✅ Verbindung zur Datenbank erfolgreich!";
} else {
    echo "❌ Verbindung zur Datenbank fehlgeschlagen.";
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <?php include 'includes/htmlHead.php'; ?>
    <title>Titel</title>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <main>
        <p>Testumgebung</p>
    </main>

    <?php include 'includes/footer.php'; ?>
</body>
</html>
