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