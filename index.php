<?php
// ==============================
// INDEX.PHP – Startseite / Homepage
// Module mit Suchfunktion und Sortierdropdown
// ==============================

include 'includes/header.php';
?>

<main class="home-container">

  <!-- ==========================
       Filter & Suche
       ========================== -->
  <section class="home-filter-bar">
    <div class="filter-group">
      <label for="sort-select">Wähle dein Modul:</label>
      <select id="sort-select" name="sortierung">
        <option value="anzahl_fragen">Anzahl der Fragen</option>
        <option value="semester">Semester</option>
        <option value="name">Alphabetisch</option>
      </select>
    </div>

    <form class="search-bar" method="get" action="">
      <input type="text" placeholder="Suche..." name="suche">
      <button type="submit"><i class="fas fa-search"></i></button>
    </form>
  </section>

  <!-- ==========================
       Modul-Grid (3 Karten pro Zeile)
       ========================== -->
  <section class="modul-grid">
    <?php for ($i = 1; $i <= 6; $i++): ?>
      <div class="modul-card">
        <div class="image-placeholder">
          <img src="images/placeholder.png" alt="Modulbild">
        </div>
        <div class="modul-info">
          <h3>Modul <?= $i ?></h3>
          <p><strong>Prof:</strong> Max Mustermann</p>
          <p><strong>Semester:</strong> 2</p>
          <p><strong>Mitglieder:</strong> 24</p>
        </div>
      </div>
    <?php endfor; ?>
  </section>

</main>

<?php include 'includes/footer.php'; ?>