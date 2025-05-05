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
        
        <?php if (isset($_SESSION['user']) && $_SESSION['user']['role_id'] === 1): ?>
        <div class="action-buttons">
            <a href="pages/create_questions.php" class="button create-questions-btn">
                Prüfungsfragen erstellen
            </a>
        </div>
        <?php endif; ?>
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
                        <a href="pages/create_questions.php?module_id=<?= urlencode($module['module_id']) ?>" class="button">
                            Fragen hinzufügen
                        </a>
                        <div class="divider"></div>
                        <a href="pages/editQuestions.php?module_id=<?= urlencode($module['module_id']) ?>" class="button">
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
    <!-- ?new=1 signalisiert „Erstellen" -->
    <a href="pages/createModule.php?new=1"
       class="modul-card modul-card--add"
       title="Neues Modul anlegen">
        <span class="plus-icon">+</span>
    </a>
    
    <!-- Button für Prüfungsfragen, im selben Stil wie die Plus-Karte -->
    <a href="pages/create_questions.php"
       class="modul-card modul-card--questions"
       title="Prüfungsfragen erstellen">
        <div class="questions-icon">
            <span class="icon-text">Q</span>
        </div>
        <div class="modul-info text-center">
            <h3>Prüfungsfragen erstellen</h3>
        </div>
    </a>
<?php endif; ?>

    <?php if (empty($modules)): ?>
        <p>Keine Module gefunden oder nicht eingeloggt.</p>
    <?php endif; ?>
</section>

</main>

<style>
    /* Styling für den zusätzlichen Prüfungsfragen-Button in der Filter-Bar */
    .action-buttons {
        margin-left: 20px;
    }
    
    .create-questions-btn {
        background-color: #1e2a38;
        color: white;
        padding: 8px 16px;
        border-radius: 4px;
        text-decoration: none;
    }
    
    .create-questions-btn:hover {
        background-color: #2e3e50;
    }
    
    /* Styling für die Prüfungsfragen-Karte */
    .modul-card--questions {
        background-color: #f0f4f8;
        text-decoration: none;
        color: inherit;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        transition: transform 0.3s, box-shadow 0.3s;
    }
    
    .modul-card--questions:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
    
    .questions-icon {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background-color: #1e2a38;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        margin-bottom: 15px;
    }
    
    .text-center {
        text-align: center;
    }
</style>

<?php include 'includes/footer.php'; ?>