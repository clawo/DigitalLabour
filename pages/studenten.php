#this is for studenten

<?php
session_start();
// Überprüfen, ob der Benutzer eingeloggt ist und die Rolle Student hat
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'student') {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <?php include __DIR__ . '/../includes/htmlHead.php'; ?>
    <title>Für Studierende</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <?php include __DIR__ . '/../includes/header.php'; ?>

    <main>
        <h1>Willkommen, <?php echo htmlspecialchars($_SESSION['user']['name']); ?>!</h1>
        <p>Hier finden Sie alle wichtigen Funktionen für Studierende.</p>

        <ul>
            <li><a href="courses.php">Meine Kurse</a></li>
            <li><a href="exams.php">Prüfungen anzeigen</a></li>
            <li><a href="results.php">Ergebnisse & Noten</a></li>
            <li><a href="materials.php">Lernmaterialien herunterladen</a></li>
            <li><a href="profile.php">Profil bearbeiten</a></li>
            <li><a href="support.php">Support kontaktieren</a></li>
        </ul>
    </main>

    <?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>