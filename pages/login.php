<?php
include '../includes/htmlHead.php';
include '../includes/header.php';
?>

<!-- ==========================
     Login-Bereich
     ========================== -->
<main class="login-container">
  <h1 class="login-title">LOGIN</h1>

  <!-- Login-Formular -->
  <form class="login-form" method="post" action="dein-login-endpunkt.php">
    <!-- E-Mail-Feld -->
    <input type="email" name="email" placeholder="Email Adresse" required>

    <!-- Passwort-Feld -->
    <input type="password" name="password" placeholder="Passwort" required>

    <!-- Hinweis zur Registrierung -->
    <p class="register-hint">
      Noch keinen Account? <a href="register.php">Jetzt registrieren!</a>
    </p>

    <!-- Login-Button -->
    <button type="submit" class="login-btn">Login</button>
  </form>
</main>

<?php include '../includes/footer.php'; 

// login.php
// Datei für Benutzeranmeldung
session_start();
require_once 'db_connect.php';
require_once 'DatabaseController.php';

/**
 * handleLogin
 *
 * Holt Anmeldedaten aus POST, validiert über DatabaseController->loginUser(),
 * speichert bei Erfolg User-Daten in Session und leitet weiter.
 *
 * @return string|null Fehlermeldung oder null
 */
function handleLogin(): ?string {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return null;
    }

    $usernameOrEmail = trim($_POST['username'] ?? '');
    $password        = $_POST['password'] ?? '';

    $controller = new DatabaseController();
    $result     = $controller->loginUser($usernameOrEmail, $password);

    if ($result['success']) {
        // Session nur mit nötigen Daten füllen
        $_SESSION['user'] = [
            'user_id'    => $result['user']['user_id'],
            'username'   => $result['user']['username'],
            'role_id'    => $result['user']['role_id'],
            'first_name' => $result['user']['first_name'],
            'last_name'  => $result['user']['last_name'],
            'email'      => $result['user']['email'],
        ];
        header('Location: dashboard.php');
        exit;
    }

    return $result['message'] ?? 'Login fehlgeschlagen.';
}

$errorMessage = handleLogin();

// In login_form.php anzeigen:
// <?= htmlspecialchars($errorMessage) ?>
?>