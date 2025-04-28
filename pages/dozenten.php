#this is the dozenten view

<?php
session_start();
// Überprüfen, ob der Benutzer eingeloggt ist und die Rolle Dozent hat
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'dozent') {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Für Dozenten</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php include 'nav.php'; ?>
    <main>
        <h1>Willkommen, <?php echo htmlspecialchars($_SESSION['user']['name']); ?>!</h1>
        <p>Hier finden Sie alle Ressourcen und Tools für Dozenten.</p>
        <ul>
            <li><a href="kursverwaltung.php">Kursverwaltung</a></li>
            <li><a href="material_upload.php">Material hochladen</a></li>
            <li><a href="notenuebersicht.php">Notenübersicht</a></li>
            <li><a href="support.php">Support kontaktieren</a></li>
        </ul>
    </main>
    <?php include 'footer.php'; ?>
</body>
</html>
