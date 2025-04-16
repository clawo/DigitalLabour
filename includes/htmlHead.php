<?php
session_start();
require_once 'db_connect.php';
$db = getDB();

// Aktuellen Dateinamen ohne .php holen → z.B. "login"
$currentPage = basename($_SERVER['PHP_SELF'], '.php');

// Absoluter Pfad auf dem Server, für file_exists()
$cssPath = __DIR__ . '/../css/style' . ucfirst($currentPage) . '.css';

// Absoluter Link zur CSS-Datei im Web (funktioniert in allen Unterordnern)
$cssLink = '/digitallabour/css/style' . ucfirst($currentPage) . '.css';
?>

<!-- ========== Head-Bereich ========== -->
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<!-- Fonts -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&display=swap" rel="stylesheet">

<!-- Global CSS -->
<link rel="stylesheet" href="/digitallabour/css/styleGlobal.css">

<!-- Seitenbezogenes CSS automatisch laden -->
<?php if (file_exists($cssPath)): ?>
  <link rel="stylesheet" href="<?= $cssLink ?>">
<?php else: ?>
  <!-- Debug-Hinweis, falls Datei fehlt (nur Entwicklung) -->
  <style>body::before { content: "⚠️ style<?= ucfirst($currentPage) ?>.css nicht gefunden."; color: red; display: block; text-align: center; margin: 20px; }</style>
<?php endif; ?>