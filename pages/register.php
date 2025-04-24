<?php
session_start();

require_once __DIR__ . '/../includes/htmlHead.php';
require_once __DIR__ . '/../db_connect.php';
require_once __DIR__ . '/../db_controller.php';

$database_controller = new DatabaseController();   // falls dein
                                                         // Konstruktor
                                                         // die DB-Verbindung braucht

$errorMessage = '';                                      // leerer Default
if ($_SERVER['REQUEST_METHOD'] === 'POST') {             // nur verarbeiten,
    $errorMessage = $database_controller->handleRegister($database_controller);
}
?>
<body>
<header>
    require_once __DIR__ . '/../includes/header.php';
</header>
<main class="register-container">
    <h1 class="register-title">REGISTRIEREN</h1>

    <?php if ($errorMessage): ?>
        <p class="error-message"><?= htmlspecialchars($errorMessage) ?></p>
    <?php endif; ?>

    <!-- action auf dieselbe Datei, XSS-sicher -->
    <form class="register-form" method="post"
          action="<?= htmlspecialchars($_SERVER['PHP_SELF']); ?>">
        <select name="rolle" required>
            <option value="" disabled <?= !isset($_POST['rolle']) ? 'selected' : '' ?>>Ich bin...</option>
            <option value="student" <?= (isset($_POST['rolle']) && $_POST['rolle'] === 'student') ? 'selected' : '' ?>>Student*in</option>
            <option value="dozent"  <?= (isset($_POST['rolle']) && $_POST['rolle'] === 'dozent')  ? 'selected' : '' ?>>Dozent*in</option>
        </select>

        <input type="text"     name="vorname"  placeholder="Vorname"        required value="<?= $_POST['vorname']  ?? '' ?>">
        <input type="text"     name="nachname" placeholder="Nachname"       required value="<?= $_POST['nachname'] ?? '' ?>">
        <input type="email"    name="email"    placeholder="Email Adresse"  required value="<?= $_POST['email']    ?? '' ?>">
        <input type="password" name="password" placeholder="Passwort"       required>

        <p class="login-hint">
            Schon einen Account? <a href="login.php">Jetzt anmelden!</a>
        </p>

        <!-- kein JS-Onclick, reine HTML-Submission -->
        <button type="submit" class="register-btn">Registrieren</button>
    </form>
</main>
</body>

<?php include __DIR__ . '/../includes/footer.php'; ?>
