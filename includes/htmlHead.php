<?php
// ============================
//   Initialisierung & DB-Connect
// ============================

session_start();

require_once 'db_connect.php';
$db = getDB();

// Aktuellen Dateinamen ohne ".php"-Endung holen
$currentPage = basename($_SERVER['PHP_SELF'], '.php');

// Absoluter Pfad zur CSS-Datei
$cssPath = __DIR__ . '/../css/style' . ucfirst($currentPage) . '.css';

// Relativer Link zur CSS-Datei (für HTML <link>)
$cssLink = 'css/style' . ucfirst($currentPage) . '.css';
?>

<!-- ========== Basis-HTML-Kopf ========== -->
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<base href="/digitallabour/"> <!-- Stellt sicher, dass relative Pfade stimmen -->

<!-- ========== Fonts ========== -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&display=swap" rel="stylesheet">

<!-- ========== Globales CSS ========== -->
<link rel="stylesheet" href="css/styleGlobal.css">

<!-- ========== Automatisch korrektes CSS einbinden ========== -->
<?php if (file_exists($cssPath)): ?>
  <link rel="stylesheet" href="<?= $cssLink ?>">
<?php endif; ?>
