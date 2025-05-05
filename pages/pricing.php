<!DOCTYPE html>
<html lang="de">

<head>
    <?php include '../includes/htmlHead.php'; ?>
    <title>Preise</title>
</head>

<?php include '../includes/header.php'; ?>

<main class="pricing-container">

  <!-- Hero -->
  <section class="hero">
    <h1>Erstelle &amp; bewerte dein Examen sofort</h1>
    <p>Wähle dein Paket, erstelle Mock-Examen und erhalte KI-Feedback mit Note.</p>
    <a href="#packages" class="btn btn-primary">Preise ansehen</a>
  </section>

  <!-- Packages -->
  <section id="packages" class="packages">
    <div class="package-card">
      <h2>Basic</h2>
      <p class="price">3 €<span>/Monat</span></p>
      <ul>
        <li>5 Mock-Examen</li>
        <li>KI-Auswertung</li>
      </ul>
      <button class="btn btn-outline">Jetzt wählen</button>
    </div>
    <div class="package-card">
      <h2>Plus</h2>
      <p class="price">6 €<span>/Monat</span></p>
      <ul>
        <li>12 Mock-Examen</li>
        <li>KI-Auswertung</li>
      </ul>
      <button class="btn btn-outline">Jetzt wählen</button>
    </div>
    <div class="package-card">
      <h2>Pro</h2>
      <p class="price">10 €<span>/Monat</span></p>
      <ul>
        <li>30 Mock-Examen</li>
        <li>KI-Auswertung</li>
      </ul>
      <button class="btn btn-outline">Jetzt wählen</button>
    </div>
  </section>

  <!-- Testimonials -->
  <section class="testimonials">
    <div class="testimonial">
      <h3>Anna M.</h3>
      <p class="stars">★★★★★</p>
      <p>Perfekt vorbereitet<br>Dank der Mock-Examen und der KI-Auswertung habe ich mich sicher gefühlt.</p>
    </div>
    <div class="testimonial">
      <h3>Ben S.</h3>
      <p class="stars">★★★★★</p>
      <p>Klausurenstress adé<br>Die schnelle Auswertung hilft mir, gezielt zu üben und meine Note zu verbessern.</p>
    </div>
    <div class="testimonial">
      <h3>Clara L.</h3>
      <p class="stars">★★★★★</p>
      <p>Einfach genial<br>Mit nur wenigen Klicks habe ich mein Examen erstellt und direkt Feedback erhalten.</p>
    </div>
  </section>

  <!-- FAQ -->
  <section class="faq">
    <h2>FAQ</h2>
    <div class="faq-item">
      <h4>Wie kann ich mein Paket kündigen?</h4>
      <p>Du kannst dein Paket jederzeit in den Kontoeinstellungen kündigen. Es läuft zum Ende des aktuellen Monats aus.</p>
    </div>
    <div class="faq-item">
      <h4>Wie schnell erhalte ich das KI-Feedback?</h4>
      <p>In der Regel innerhalb weniger Minuten nach Abgabe deines Mock-Examens.</p>
    </div>
  </section>

</main>

<?php include '../includes/footer.php'; ?>
</html>
