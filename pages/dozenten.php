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
        /* Make sure the question button matches other links */
        .question-button {
            display: block;
            margin-top: 20px;
            background-color: #1e2a38;
            color: white;
            padding: 10px 15px;
            text-decoration: none;
            border-radius: 5px;
            width: fit-content;
            font-weight: bold;
        }
        
        .question-button:hover {
            background-color: #2e3e50;
        }
        
        /* Add a divider to separate it from the main menu */
        .divider {
            height: 1px;
            background-color: #ddd;
            margin: 25px 0;
        }
        
        /* Make it look like a standard menu but special */
        .special-menu {
            margin-top: 20px;
        }
        
        .special-menu h3 {
            color: #1e2a38;
            margin-bottom: 15px;
        }
        
        /* Make the button stand out */
        .highlight {
            border-left: 4px solid #1e2a38;
            padding-left: 10px;
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
        
        <div class="divider"></div>
        
        <div class="special-menu highlight">
            <h3>Prüfungsverwaltung</h3>
            <ul>
                <li><a href="create_questions.php">Prüfungsfragen erstellen</a></li>
            </ul>
        </div>
        
        <!-- Alternative button style if you prefer a button over a list item -->
        <a href="create_questions.php" class="question-button">Prüfungsfragen erstellen</a>
    </main>
    <?php include 'footer.php'; ?>
</body>
</html>