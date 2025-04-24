<?php
session_start();
require_once '../includes/htmlHead.php';
require_once '../includes/header.php';
require_once '../db_connect.php';
require_once '../db_controller.php';
$database_controller = new DatabaseController();
$errorMessage = $database_controller->handleRegister($database_controller);
?>

<main class="register-container">
    <h1 class="register-title">REGISTRIEREN</h1>

    <?php if ($errorMessage): ?>
        <p class="error-message"><?= htmlspecialchars($errorMessage) ?></p>
    <?php endif; ?>

    <form class="register-form" method="post" action="">
        <select name="rolle" required>
            <option value="" disabled selected>Ich bin...</option>
            <option value="student">Student*in</option>
            <option value="dozent">Dozent*in</option>
        </select>

        <input type="text" name="vorname" placeholder="Vorname" required>
        <input type="text" name="nachname" placeholder="Nachname" required>
        <input type="email" name="email" placeholder="Email Adresse" required>
        <input type="password" name="password" placeholder="Passwort" required>

        <p class="login-hint">
            Schon einen Account? <a href="login.php">Jetzt anmelden!</a>
        </p>

        <button  onclick="handleRegister()" type="submit" class="register-btn">Registrieren</button>

    </form>
</main>

<?php include '../includes/footer.php'; ?>
