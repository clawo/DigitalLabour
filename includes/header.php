<?php
// ==============================
// Gemeinsamer Head- und Header-Bereich
// Lädt Fonts, Stylesheets (global & seitenbezogen), DB-Verbindung und Header-HTML
// ==============================

session_start();

// Datenbankverbindung herstellen
require_once 'db_connect.php';
$db = getDB();

// Dateiname der aktuellen Seite ohne ".php"
$currentPage = basename($_SERVER['PHP_SELF'], '.php');

// Pfade für CSS
$cssPath = __DIR__ . '/../css/style' . ucfirst($currentPage) . '.css';
$cssLink = '../css/style' . ucfirst($currentPage) . '.css';
?>

<!-- ==============================
     META & BASIS-DATEN
     ============================== -->
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<!-- ==============================
     Fonts: Google Fonts (Bebas Neue)
     ============================== -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&display=swap" rel="stylesheet">

<!-- Browser Icon -->
<link rel="icon" href="../images/logo.png" type="image/png">

<!-- ==============================
     Globales Stylesheet
     ============================== -->
<link rel="stylesheet" href="../css/styleGlobal.css">

<!-- ==============================
     Seitenbezogenes Stylesheet (wenn vorhanden) mit Cache-Busting
     ============================== -->
<?php if (file_exists($cssPath)): ?>
    <link rel="stylesheet" href="<?= $cssLink ?>?v=<?= filemtime($cssPath) ?>">
<?php else: ?>
    <style>
        body::before {
            content: "⚠️ style<?= ucfirst($currentPage) ?>.css nicht gefunden.";
            color: red;
            display: block;
            text-align: center;
            margin: 20px;
        }
    </style>
<?php endif; ?>

<!-- ==============================
     Header-Bereich
     ============================== -->
<style>
    /* Allgemeines Header-Layout */
    .header {
        width: 100%;
        max-width: 1920px;
        height: 100px;
        background-color: white;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 5%;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        font-family: 'Bebas Neue', sans-serif;
        box-sizing: border-box;
    }

    /* Logo-Styling */
    .logo img {
        height: 150px;
        max-height: 200px;
        width: auto;
    }

    /* Navigation */
    .nav {
        display: flex;
        gap: 100px;
        font-size: 1.8rem;
        flex-wrap: wrap;
        justify-content: center;
    }

    .nav a {
        text-decoration: none;
        color: black;
        transition: color 0.3s;
        white-space: nowrap;
    }

    .nav a:hover {
        color: #555;
    }

    /* Login-Button */
    .login-button {
        background-color: #1e2a38;
        color: white;
        padding: 8px 16px;
        border-radius: 8px;
        text-decoration: none;
        font-size: 1.2rem;
        white-space: nowrap;
    }

    .login-button:hover {
        background-color: #2e3e50;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .header {
            flex-direction: column;
            height: auto;
            padding: 20px;
            gap: 15px;
        }

        .nav {
            flex-direction: column;
            gap: 10px;
        }

        .logo img {
            height: 50px;
        }
    }
</style>

<div class="header">
    <!-- Logo -->
    <div class="logo">
        <a href="../index.php"><img src="../images/logo.png" alt="Examwise Logo"></a>
    </div>

    <!-- Navigation -->
    <div class="nav">
        <a href="../index.php">Suchen</a>
        <a href="../pages/dozenten.php">Für Dozenten</a>
        <a href="../pages/studenten.php">Für Studenten</a>
    </div>

    <!-- Login-Button -->
    <a href="../pages/login.php" class="login-button">Login</a>
</div>
