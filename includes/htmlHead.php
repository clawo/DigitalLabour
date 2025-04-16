<?php
session_start();

require_once 'db_connect.php';
$db = getDB();

// Testabfrage (optional, nur zum Testen – kann später entfernt werden)
try {
    $stmt = $db->query("SELECT NOW()");
    $row = $stmt->fetch();
    echo "📅 Datenbankzeit: " . $row[0];
} catch (PDOException $e) {
    echo "❌ Fehler bei der Testabfrage: " . $e->getMessage();
}
?>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<!-- Set digitallabour as Base href -->
<base href="/digitallabour/">

<!-- Load Font -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&display=swap" rel="stylesheet">

<!-- Load Stylesheet -->
<link rel="stylesheet" href="css/style.css">