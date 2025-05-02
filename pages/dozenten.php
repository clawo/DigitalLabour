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
    <style>
        .action-buttons {
            margin-top: 30px;
            padding: 20px;
            background-color: #f5f5f5;
            border-radius: 5px;
        }
        
        .secondary-btn {
            display: inline-block;
            background-color: white;
            color: #1e2a38;
            padding: 8px 16px;
            border: 1px solid #1e2a38;
            border-radius: 4px;
            text-decoration: none;
            font-size: 14px;
            margin-right: 10px;
            transition: all 0.3s ease;
        }
        
        .secondary-btn:hover {
            background-color: #eef1f6;
        }
        
        .btn-icon {
            margin-right: 5px;
        }
        
        .section-title {
            font-size: 18px;
            margin-bottom: 15px;
            color: #555;
        }
    </style>
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
        
        <div class="action-buttons">
            <h3 class="section-title">Prüfungsverwaltung</h3>
            <a href="create_questions.php" class="secondary-btn">
                <span class="btn-icon">+</span> Prüfungsfragen erstellen
            </a>
        </div>
    </main>
    <?php include 'footer.php'; ?>
</body>
</html>