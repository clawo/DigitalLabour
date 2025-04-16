<?php include 'includes/htmlHead.php'; ?>
<?php include 'includes/header.php'; ?>

<main class="home-container">

  <!-- Filter & Suche -->
  <div class="home-filter-bar">
    <label for="sort-select">Wähle dein Modul:</label>
    <select id="sort-select" name="sortierung">
      <option value="anzahl_fragen">Anzahl der Fragen</option>
      <option value="semester">Semester</option>
      <option value="name">Alphabetisch</option>
    </select>

    <div class="search-bar">
      <input type="text" placeholder="Suche" name="suche">
      <button type="submit"><i class="fas fa-search"></i></button>
    </div>
  </div>

  <!-- Modul-Karten -->
  <div class="modul-grid">
    <?php for ($i = 1; $i <= 6; $i++): ?>
      <div class="modul-card">
        <div class="image-placeholder">
          <img src="images/placeholder.png" alt="Modulbild">
        </div>
        <h3>Modul <?= $i ?></h3>
        <ul>
          <li>Prof. Mustermann</li>
          <li>2. Semester</li>
          <li>24 Mitglieder</li>
        </ul>
      </div>
    <?php endfor; ?>
  </div>

</main>

<?php include 'includes/footer.php'; ?>