<?php
session_start();
require_once 'db_connect.php';
$db = getDB();

// Aktuelle Seite
$currentPage = basename($_SERVER['PHP_SELF'], '.php');

// Absoluter Pfad auf dem Server (für file_exists)
$cssPath = __DIR__ . '/../css/style' . ucfirst($currentPage) . '.css';

// Öffentlicher URL-Link (fix: mit /digitallabour/)
$cssLink = '/digitallabour/css/style' . ucfirst($currentPage) . '.css';
?>

<!-- Meta & Base -->
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<base href="/digitallabour/">

<!-- Fonts -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&display=swap" rel="stylesheet">

<!-- Global Styles -->
<link rel="stylesheet" href="/digitallabour/css/styleGlobal.css">

<!-- Dynamisches Seiten-CSS -->
<?php if (file_exists($cssPath)): ?>
  <link rel="stylesheet" href="<?= $cssLink ?>">
<?php endif; ?>
