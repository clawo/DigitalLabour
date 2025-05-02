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

if (isset($_SESSION['user']) && !empty($_SESSION['user']['user_id'])) {
    $db_controller = new DatabaseController();

    // load modules from database
    $modules = $db_controller->getModulesFiltered($sortierung, $suche);
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
         Modul‑Grid (echte Module aus DB)
         ========================== -->
        <section class="modul-grid">
            <?php if (!empty($modules)): ?>
                <?php foreach ($modules as $module): ?>
                    <?php
                    $isStudent = ($_SESSION['user']['role_id'] === 2);
                    $onclick   = $isStudent
                        ? "onclick=\"window.location.href='pages/createExam.php?module_id=" .
                        urlencode($module['module_id']) . "'\""
                        : "";
                    ?>
                    <div class="modul-card" style="cursor: <?= $isStudent ? 'pointer' : 'default' ?>;" <?= $onclick ?>>
                        <div class="image-placeholder">
                            <img src="images/placeholder.png" alt="Modulbild">
                        </div>
                        <div class="modul-info">
                            <h3><?= htmlspecialchars($module['module_name']) ?></h3>
                            <p><strong>Label:</strong> <?= htmlspecialchars($module['module_label']) ?></p>

                            <!-- Buttons nur für Dozenten -->
                            <?php if ($_SESSION['user']['role_id'] === 1): ?>
                                <a href="pages/createExam.php?module_id=<?= urlencode($module['module_id']) ?>" class="button">
                                    Probeklausur anlegen
                                </a>
                                <div class="divider"></div>
                                <a href="pages/createModule.php?module_id=<?= urlencode($module['module_id']) ?>" class="button">
                                    Fragen bearbeiten
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

            <!-- ++++++++++++++++++++++++++++++++ -->
            <!--  PLUS‑KARTE  (nur für Dozenten)  -->
            <!-- ++++++++++++++++++++++++++++++++ -->

            <?php if ($_SESSION['user']['role_id'] === 1): ?>
                <!-- ?new=1 signalisiert „Erstellen“ -->
                <a href="pages/createModule.php?new=1"
                   class="modul-card modul-card--add"
                   title="Neues Modul anlegen">
                    <span class="plus-icon">+</span>
                </a>
            <?php endif; ?>

            <?php if (empty($modules)): ?>
                <p>Keine Module gefunden oder nicht eingeloggt.</p>
            <?php endif; ?>
        </section>

    </main>

<?php include 'includes/footer.php'; ?>