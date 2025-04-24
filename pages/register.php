<?php
require_once '../includes/htmlHead.php';
require_once '../includes/header.php';
require_once '../DatabaseController.php';


?>
<div class="register-container">
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
    <?php
    session_start();
      function handleRegister(): ?string {
          echo '<script>console.log("Handling registration...");</script>';
        /* if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
          if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
              return null;
          }
      */

          $roleInput   = $_POST['rolle'] ?? '';
          $roleId      = $roleInput === 'student' ? 2 : ($roleInput === 'dozent' ? 1 : 2);
          $firstName   = trim($_POST['vorname'] ?? '');
          $lastName    = trim($_POST['nachname'] ?? '');
          $email       = trim($_POST['email'] ?? '');
          $password    = $_POST['password'] ?? '';

          if (empty($firstName) || empty($lastName) || empty($email) || empty($password)) {
              return 'Bitte alle Pflichtfelder ausfüllen.';
          }

          $controller = new DatabaseController();
          $data = [
              'username'   => $email, // Username hier als E-Mail
              'email'      => $email,
              'first_name' => $firstName,
              'last_name'  => $lastName,
              'role_id'    => $roleId,
              'password'   => $password
          ];

          echo '<script>console.log("Data to be inserted: ' . json_encode($data) . '");</script>';
          $result = $controller->registerUser($data);

          if ($result['success']) {
              $_SESSION['user'] = [
                  'user_id'  => $result['user_id'],
                  'username' => $email,
                  'role_id'  => $roleId,
                  'email'    => $email,
              ];
              header('Location: welcome.php');
              exit;
          }

          return $result['message'] ?? 'Registrierung fehlgeschlagen.';
      }

      $errorMessage = handleRegister();?>
    <button onclick="handleRegister()" type="submit" class="register-btn">Registrieren</button>

  </form>
</div>

<?php include '../includes/footer.php'; ?>