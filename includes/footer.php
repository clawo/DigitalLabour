<!-- 
  FOOTER-BEREICH
  Enthält Logo, Links, Social Media Icons und rechtliche Hinweise.
  Eingebunden per PHP include() auf jeder Seite.
-->

<!-- Schriftart & FontAwesome Icons (für Socials) -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
  /* ========== Allgemeiner Footer-Container ========== */
  .footer {
    background-color: #1e2a38;
    color: white;
    padding: 0px 5% 20px;
    font-family: 'Bebas Neue', sans-serif;
  }

  /* ========== Oberer Footer-Bereich (Logo + Spalten) ========== */
  .footer-top {
    display: flex;
    justify-content: center;
    gap: 175px;
    flex-wrap: wrap;
    margin-bottom: 20px;
  }

  .footer-logo img {
    height: 200px;
    width: auto;
  }

  .footer-column {
    display: flex;
    flex-direction: column;
    gap: 10px;
    margin: 10px 0;
  }

  .footer-column h3 {
    font-size: 1.4rem;
    margin-bottom: 5px;
    text-decoration: underline;
  }

  .footer-column a {
    color: white;
    text-decoration: none;
    font-size: 1.2rem;
  }

  .footer-column a:hover {
    text-decoration: underline;
  }

  /* ========== Trennlinie ========== */
  .footer-divider {
    height: 1.5px;
    width: 35%;
    background-color: white;
    margin: 20px auto;
  }

  /* ========== Social Media Icons ========== */
  .social-icons {
    display: flex;
    justify-content: center;
    gap: 15px;
    margin-bottom: 20px;
  }

  .social-icons i {
    font-size: 1.5rem;
    color: white;
    transition: color 0.3s;
  }

  .social-icons i:hover {
    color: #ccc;
  }

  /* ========== Unterer Footer-Bereich ========== */
  .footer-bottom {
    background-color: #161E26;
    color: white;
    padding: 20px 5%;
    font-family: 'Bebas Neue', sans-serif;
    display: flex;
    justify-content: center;
    align-items: center;
    position: relative;
    min-height: 60px;
  }

  /* Copyright mittig */
  .footer-bottom .copyright {
    position: absolute;
    left: 50%;
    transform: translateX(-50%);
    font-size: 1.2rem;
    font-weight: bold;
    white-space: nowrap;
  }

  /* Rechtsliegende Links (AGB, Impressum, Datenschutz) */
  .footer-bottom .links {
    margin-left: auto;
    display: flex;
    gap: 30px;
  }

  .footer-bottom .links a {
    color: white;
    text-decoration: none;
    font-size: 1rem;
    font-weight: normal;
  }

  .footer-bottom .links a:hover {
    text-decoration: underline;
  }

  /* ========== Responsive Anpassungen für Mobile ========== */
  @media (max-width: 768px) {
    .footer-top {
      flex-direction: column;
      gap: 30px;
      align-items: center;
      text-align: center;
    }

    .footer-logo img {
      height: 60px;
    }

    .footer-bottom {
      flex-direction: column;
      padding-top: 40px;
    }

    .footer-bottom .copyright {
      position: static;
      transform: none;
      margin-bottom: 10px;
    }

    .footer-bottom .links {
      justify-content: center;
      margin-left: 0;
    }
  }
</style>

<!-- ========== HTML-Struktur des Footers ========== -->
<div class="footer">
  <div class="footer-top">
    <!-- Logo -->
    <div class="footer-logo">
      <img src="../images/logo.png" alt="Examwise Logo">
    </div>

    <!-- Spalte 1: Dozenten -->
    <div class="footer-column">
        <h3>Für Dozenten</h3>
        <a href="/pages/createModule.php">Modul erstellen</a>
        <a href="/pages/create_questions.php">Fragen erstellen</a>
    </div>

    <!-- Spalte 2: Studenten -->
    <div class="footer-column">
      <h3>Für Studenten</h3>
      <a href="#">Mock beantworten</a>
    </div>

    <!-- Spalte 3: Allgemeines -->
    <div class="footer-column">
      <h3>Für Alle</h3>
      <a href="../pages/contact.php">Kontakt</a>
      <a href="../pages/login.php">Login / Registrieren</a>
    </div>
  </div>

  <!-- Trennlinie -->
  <div class="footer-divider"></div>

  <!-- Social Media Icons -->
  <div class="social-icons">
    <a href="#"><i class="fab fa-facebook-f"></i></a>
    <a href="#"><i class="fab fa-twitter"></i></a>
    <a href="#"><i class="fab fa-instagram"></i></a>
    <a href="#"><i class="fab fa-linkedin-in"></i></a>
    <a href="#"><i class="fab fa-google"></i></a>
  </div>
</div>

<!-- Unterer Bereich: Copyright + Links -->
<div class="footer-bottom">
  <div class="copyright">© Copyright 2025</div>
  <div class="links">
    <a href="../pages/agb.php">AGB</a>
    <a href="../pages/impressum.php">Impressum</a>
    <a href="../pages/datenschutz.php">Datenschutz</a>
  </div>
</div>