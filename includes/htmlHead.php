<?php
// ==============================
// HTML HEAD-BEREICH
// Gemeinsamer Head-Inhalt für alle Seiten
// Lädt Fonts, Stylesheets (global & seitenbezogen) und stellt DB-Verbindung her
// ==============================

session_start();

// Datenbankverbindung herstellen
require_once 'db_connect.php';
$db = getDB();

// Dateiname der aktuellen Seite ohne ".php"
$currentPage = basename($_SERVER['PHP_SELF'], '.php');

// Absoluter Pfad zur zugehörigen CSS-Datei auf dem Server
$cssPath = __DIR__ . '/../css/style' . ucfirst($currentPage) . '.css';

// Relativer Link zum Einbinden im HTML (von Seiten wie /pages/*.php aus)
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
<link rel="icon" href="images/logo.png" type="image/png">
<a href="profile.php">My Profile</a>

<!-- ==============================
     Globales Stylesheet (für die gesamte Website)
     ============================== -->
<link rel="stylesheet" href="../css/styleGlobal.css">

<!-- ==============================
     Seitenbezogenes Stylesheet mit Cache-Busting
     (lädt nur, wenn CSS-Datei existiert)
     ============================== -->
<?php if (file_exists($cssPath)): ?>
  <link rel="stylesheet" href="<?= $cssLink ?>?v=<?= filemtime($cssPath) ?>">
<?php else: ?>
  <!-- Hinweis im DEV-Fall, wenn CSS-Datei nicht gefunden wurde -->
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