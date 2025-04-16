<?php
// ============================
//   Initialisierung & DB-Connect
// ============================

session_start();

// Datenbankverbindung herstellen
require_once 'db_connect.php';
$db = getDB();

// Aktuellen Dateinamen ohne ".php"-Endung holen
// Beispiel: "login.php" → "login"
$currentPage = basename($_SERVER['PHP_SELF'], '.php');

// Dynamischer CSS-Dateiname, z. B. "styleLogin.css"
$cssFile = "css/style" . ucfirst($currentPage) . ".css";
?>

<!-- ========== Basis-HTML-Kopf ========== -->
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<base href="/digitallabour/"> <!-- Basis-URL für relative Pfade -->

<!-- ========== Fonts einbinden ========== -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&display=swap" rel="stylesheet">

<!-- ========== Globales CSS (für alle Seiten gültig) ========== -->


<!-- ========== CSS für spezifische Seite automatisch laden ========== -->
<?php if (file_exists($cssFile)): ?>
  <link rel="stylesheet" href="<?= $cssFile ?>">
<?php endif; ?>