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

$loggedIn = isset($_SESSION['user']) && !empty($_SESSION['user']['user_id']);
$userRole = isset($_SESSION['user']['role_id']) ? $_SESSION['user']['role_id'] : null;

// Rollen-IDs: 1 = Dozent, 2 = Student (aus Ihrem Code abgeleitet)
$isDozent = $userRole == 1;
$isStudent = $userRole == 2;
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

    /* Active link */
    .nav a.active {
        color: #1e2a38;
        font-weight: bold;
    }

    /* Button Styles */
    .button {
        padding: 8px 16px;
        border-radius: 8px;
        text-decoration: none;
        font-size: 1.2rem;
        white-space: nowrap;
        transition: background-color 0.3s, color 0.3s;
        display: inline-block;
    }

    /* Login-Button */
    .login-button {
        background-color: #1e2a38;
        color: white;
    }

    .login-button:hover {
        background-color: #2e3e50;
    }

    /* Profile-Button */
    .profile-button {
        background-color: white;
        color: #1e2a38;
        border: 2px solid #1e2a38;
        margin-right: 10px;
    }

    .profile-button:hover {
        background-color: #f5f5f5;
    }

    /* User actions area */
    .user-actions {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    /* User info display */
    .user-info {
        display: flex;
        align-items: center;
        font-size: 1rem;
        margin-right: 15px;
    }

    .user-name {
        font-weight: bold;
    }

    .user-role {
        color: #666;
        font-size: 0.85rem;
        margin-left: 5px;
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
        
        .user-actions {
            flex-direction: column;
            gap: 5px;
            width: 100%;
        }
        
        .button {
            width: 100%;
            text-align: center;
        }
        
        .user-info {
            flex-direction: column;
            margin-right: 0;
            margin-bottom: 10px;
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
        <a href="../index.php" <?= $currentPage === 'index' ? 'class="active"' : '' ?>>Suchen</a>
        
        <?php if ($loggedIn): ?>
            <?php if ($isDozent): ?>
                <!-- Dozenten-spezifische Navigation -->
                <a href="../pages/dozenten.php" <?= $currentPage === 'dozenten' ? 'class="active"' : '' ?>>Mein Dozentenbereich</a>
            <?php elseif ($isStudent): ?>
                <!-- Studenten-spezifische Navigation -->
                <a href="../pages/studenten.php" <?= $currentPage === 'studenten' ? 'class="active"' : '' ?>>Mein Studentenbereich</a>
            <?php else: ?>
                <!-- Fallback - alle Links anzeigen -->
                <a href="../pages/dozenten.php">Für Dozenten</a>
                <a href="../pages/studenten.php">Für Studenten</a>
            <?php endif; ?>
        <?php else: ?>
            <!-- Nicht eingeloggt - Standard-Navigation -->
            <a href="../pages/dozenten.php">Für Dozenten</a>
            <a href="../pages/studenten.php">Für Studenten</a>
        <?php endif; ?>
    </div>

    <!-- Benutzer-Aktionen (Login/Logout/Profil) -->
    <?php if ($loggedIn): ?>
        <div class="user-actions">
            <div class="user-info">
                <span class="user-name">
                    <?= htmlspecialchars($_SESSION['user']['first_name'] ?? '') ?> 
                    <?= htmlspecialchars($_SESSION['user']['last_name'] ?? '') ?>
                </span>
                <span class="user-role">
                    (<?= $isDozent ? 'Dozent' : ($isStudent ? 'Student' : 'Benutzer') ?>)
                </span>
            </div>
            <a href="../pages/profile.php" class="button profile-button" <?= $currentPage === 'profile' ? 'style="background-color: #eef1f6;"' : '' ?>>Mein Profil</a>
            <a href="../pages/logout.php" class="button login-button">Logout</a>
        </div>
    <?php else: ?>
        <div class="user-actions">
            <a href="../pages/login.php" class="button login-button">Login</a>
        </div>
    <?php endif; ?>
</div>