<?php
// ==============================
// INDEX.PHP – Startseite / Homepage
// Zeigt eine grid-basierte Übersicht aller Module
// Filterbar + Suche + Modul-Kacheln
// ==============================

include 'includes/htmlHead.php';
include 'includes/header.php';
?>

<main class="home-container">

  <!-- ==========================
       Filter- & Suchbereich
       ========================== -->
  <section class="home-filter-bar">
    <label for="sort-select">Wähle dein Modul:</label>
    <select id="sort-select" name="sortierung">
      <option value="anzahl_fragen">Anzahl der Fragen</option>
      <option value="semester">Semester</option>
      <option value="name">Alphabetisch</option>
    </select>

    <!-- Suchfeld -->
    <div class="search-bar">
      <input type="text" placeholder="Suche" name="suche">
      <button type="submit"><i class="fas fa-search"></i></button>
    </div>
  </section>

  <!-- ==========================
       Modul-Übersicht (Grid mit max. 3 Spalten)
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