<!DOCTYPE html>
<html lang="de">

<head>
    <?php include '../includes/htmlHead.php'; ?>
    <title>Kontakt</title>
</head>

<?php include '../includes/header.php'; ?>

<body>
<div class="container">
    <h1>Kontakt</h1>
    <p>Wenn Sie Fragen zur Nutzung von ExamWise oder allgemeine Anliegen haben, kontaktieren Sie uns gern:</p>

    <p>
      <strong>HSBA – Hamburg School of Business Administration</strong><br />
      Alter Wall 38<br />
      20457 Hamburg<br />
      Deutschland<br />
      Tel: +49 (0)40 36138-700<br />
      E-Mail: <a href="mailto:examwise@hsba.de">examwise@hsba.de</a>
    </p>

    <h2>Kontaktformular</h2>
    <form action="/kontakt-senden" method="post">
      <label for="name">Ihr Name</label>
      <input type="text" id="name" name="name" required>

      <label for="email">Ihre E-Mail-Adresse</label>
      <input type="email" id="email" name="email" required>

      <label for="nachricht">Nachricht</label>
      <textarea id="nachricht" name="nachricht" rows="6" required></textarea>

      <button type="submit">Nachricht senden</button>
    </form>
  </div>
</body>

<?php include '../includes/footer.php'; ?>

</html>