<?php
// ==============================
// INDEX.PHP – Startseite / Homepage
// Module mit Suchfunktion und Sortierdropdown
// ==============================

include 'includes/header.php';
require_once 'includes/db_controller.php';

$modules = [];
$sortierung = $_GET['sortierung'] ?? 'name';
$suche = $_GET['suche'] ?? '';

if (isset($_SESSION['loggedIn']) && $_SESSION['loggedIn'] === true) {
    $db_controller = new DatabaseController();

    // load modules from database
    $modules = $db_controller->getModulesFiltered($sortierung, $suche);

    echo '<script>console.log("Module geladen: ' . count($modules) . ' Module gefunden.");</script>';
}
?>

<main class="home-container">
    <!-- ==========================
         Filter & Suche
         ========================== -->
    <section class="home-filter-bar">
        <div class="filter-group">
            <form method="get" action="">
                <label for="sort-select">Sortiere nach:</label>
                <select id="sort-select" name="sortierung" onchange="this.form.submit()">
                    <option value="anzahl_fragen" <?= ($sortierung == 'anzahl_fragen') ? 'selected' : '' ?>>Anzahl der Fragen</option>
                    <option value="semester" <?= ($sortierung == 'semester') ? 'selected' : '' ?>>Semester</option>
                    <option value="name" <?= ($sortierung == 'name') ? 'selected' : '' ?>>Alphabetisch</option>
                </select>
            </form>
        </div>

        <form class="search-bar" method="get" action="">
            <input type="text" placeholder="Suche nach Modul..." name="suche" value="<?= htmlspecialchars($suche) ?>">
            <input type="hidden" name="sortierung" value="<?= htmlspecialchars($sortierung) ?>">
            <button type="submit"><i class="fas fa-search"></i></button>
        </form>
    </section>

    <!-- ==========================
         Modul-Grid (echte Module aus DB)
         ========================== -->
    <section class="modul-grid">
        <?php if (!empty($modules)): ?>
            <?php foreach ($modules as $module): ?>
                <div class="modul-card">
                    <div class="image-placeholder">
                        <img src="images/placeholder.png" alt="Modulbild">
                    </div>
                    <div class="modul-info">
                        <h3><?= htmlspecialchars($module['module_name']) ?></h3>
                        <p><strong>Label:</strong> <?= htmlspecialchars($module['module_label']) ?></p>
                        <!-- Optional weitere Infos wie Semester etc. -->
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Keine Module gefunden oder nicht eingeloggt.</p>
        <?php endif; ?>
    </section>

</main>

<?php include 'includes/footer.php'; ?>