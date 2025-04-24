<?php
include '../includes/htmlHead.php';
include '../includes/header.php';
?>

<main class="register-container">
  <h1 class="register-title">REGISTRIEREN</h1>

  <!-- ==============================
       Registrierungsformular
       Action: Noch zu definierender Backend-Endpunkt
       Method: POST
       ============================== -->
  <form class="register-form" method="post" action="dein-register-endpunkt.php">

    <!-- Dropdown zur Auswahl der Rolle -->
    <select name="rolle" required>
      <option value="" disabled selected>Ich bin...</option>
      <option value="student">Student*in</option>
      <option value="dozent">Dozent*in</option>
    </select>

    <!-- Benutzerinformationen -->
    <input type="text" name="vorname" placeholder="Vorname" required>
    <input type="text" name="nachname" placeholder="Nachname" required>
    <input type="email" name="email" placeholder="Email Adresse" required>
    <input type="password" name="password" placeholder="Passwort" required>

    <!-- Hinweis zur Anmeldung für bestehende Benutzer -->
    <p class="login-hint">
      Schon einen Account? <a href="login.php">Jetzt anmelden!</a>
    </p>

    <!-- Registrierungs-Button -->
    <button type="submit" class="register-btn">Registrieren</button>
  </form>
</main>

<?php include '../includes/footer.php'; ?>
<?php
function handleRegister(): ?string {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return null;
    }

    $username    = trim($_POST['username'] ?? '');
    $email       = trim($_POST['email'] ?? '');
    $firstName   = trim($_POST['first_name'] ?? '');
    $lastName    = trim($_POST['last_name'] ?? '');
    $password    = $_POST['password'] ?? '';
    $confirmPass = $_POST['confirm_password'] ?? '';
    $roleId      = intval($_POST['role_id'] ?? 2); // Standardrolle 2

    if (empty($username) || empty($email) || empty($password) || empty($confirmPass)) {
        return 'Bitte alle Pflichtfelder ausfüllen.';
    }
    if ($password !== $confirmPass) {
        return 'Passwörter stimmen nicht überein.';
    }

    $controller = new DatabaseController();
    $data = [
        'username'   => $username,
        'email'      => $email,
        'first_name' => $firstName,
        'last_name'  => $lastName,
        'role_id'    => $roleId,
        'password'   => $password
    ];
    $result = $controller->registerUser($data);

    if ($result['success']) {
        // Direktes Einloggen nach erfolgreicher Registrierung
        $_SESSION['user'] = [
            'user_id'  => $result['user_id'],
            'username' => $username,
            'role_id'  => $roleId,
            'email'    => $email,
        ];
        header('Location: welcome.php');
        exit;
    }

    return $result['message'] ?? 'Registrierung fehlgeschlagen.';
}

$errorMessage = handleRegister();

// In register_form.php anzeigen:
// <?= htmlspecialchars($errorMessage) ?>
?>
