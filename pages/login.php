<?php include '../includes/header.php'; ?>

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

<?php include '../includes/footer.php'; ?>
