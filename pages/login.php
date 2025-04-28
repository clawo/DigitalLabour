<?php
require_once '../includes/header.php';
?>
<!-- ==========================
     Login-Bereich
     ========================== -->
<main class="login-container">
  <h1 class="login-title">LOGIN</h1>

  <?php if (!empty($errorMessage)): ?>
    <p class="error-message"><?= htmlspecialchars($errorMessage) ?></p>
  <?php endif; ?>

  <form class="login-form" method="post" action="">
    <input type="email" name="email" placeholder="Email Adresse" required>
    <input type="password" name="password" placeholder="Passwort" required>

    <p class="register-hint">
      Noch keinen Account? <a href="register.php">Jetzt registrieren!</a>
    </p>

    <button type="submit" class="login-btn">Login</button>
  </form>
</main>

<?php include '../includes/footer.php'; ?>
<?php
require_once '../includes/db_controller.php';

function handleLogin(): ?string {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return null;
    }

    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $controller = new DatabaseController();
    $result     = $controller->loginUser($email, $password);

    if ($result['success']) {
        $_SESSION['user'] = [
            'user_id'    => $result['user']['user_id'],
            'username'   => $result['user']['username'],
            'role_id'    => $result['user']['role_id'],
            'first_name' => $result['user']['first_name'],
            'last_name'  => $result['user']['last_name'],
            'email'      => $result['user']['email'],
        ];

        header('Location: ../index.php');
        echo '<script>window.location.href = "../index.php";</script>';
        exit;
    }

    return $result['message'] ?? 'Login fehlgeschlagen.';
}

$errorMessage = handleLogin();
?>
