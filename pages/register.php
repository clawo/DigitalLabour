<?php
require_once '../includes/header.php';
require_once '../includes/db_controller.php';

$db_controller = new DatabaseController();
$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errorMessage = handleRegister();
}

function handleRegister(): ?string {
    global $db_controller;
    echo '<script>console.log("Handling registration...");</script>';

    $roleInput   = $_POST['rolle'] ?? '';
    $roleId      = $roleInput === 'student' ? 2 : ($roleInput === 'dozent' ? 1 : 2);
    $firstName   = trim($_POST['vorname'] ?? '');
    $lastName    = trim($_POST['nachname'] ?? '');
    $email       = trim($_POST['email'] ?? '');
    $password    = $_POST['password'] ?? '';

    if (empty($firstName) || empty($lastName) || empty($email) || empty($password)) {
        return 'Bitte alle Pflichtfelder ausfüllen.';
    }

    $data = [
        'username'   => $firstName . ' ' . $lastName,
        'email'      => $email,
        'first_name' => $firstName,
        'last_name'  => $lastName,
        'role_id'    => $roleId,
        'password'   => $password
    ];

    $result = $db_controller->registerUser($data);

    if ($result['success']) {
        $_SESSION['user'] = [
            'user_id'  => $result['user_id'],
            'username' => $email,
            'role_id'  => $roleId,
            'email'    => $email,
        ];
        header('Location: ../index.php');
        exit;
    }

    return $result['message'] ?? 'Registrierung fehlgeschlagen.';
}
?>
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

<?php include __DIR__ . '/../includes/footer.php'; ?>
