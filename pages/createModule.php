<!DOCTYPE html>
<html lang="de">
 
<head>
    <?php include '../includes/htmlHead.php'; ?>
    <title>Module erstellen</title>
</head>
 
<?php include '../includes/header.php'; ?>
 
<?php
require_once '../includes/db_controller.php';
 
$db = new DatabaseController();
 
// check if user is logged in
if (!isset($_SESSION['user'])) {
    echo '<script>window.location.href = "../index.php";</script>';
    exit();
}
 
// check if user is allowed to create modules (only role_id 1 - teachers/dozenten)
if ($_SESSION['user']['role_id'] !== 1) {
    echo '<script>alert("Zugriff auf diese Seite nicht erlaubt."); window.location.href = "../index.php";</script>';
    exit();
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $moduleName = trim($_POST['module_name'] ?? '');
    $moduleLabel = trim($_POST['module_label'] ?? '');
    
    // Validation
    $errors = [];
    
    if (empty($moduleName)) {
        $errors[] = "Der technische Name des Moduls ist erforderlich.";
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $moduleName)) {
        $errors[] = "Der technische Name darf nur Buchstaben, Zahlen und Unterstriche enthalten.";
    }
    
    if (empty($moduleLabel)) {
        $errors[] = "Der Anzeigename des Moduls ist erforderlich.";
    }
    
    // Check if module name already exists
    if (empty($errors) && $db->moduleNameExists($moduleName)) {
        $errors[] = "Ein Modul mit diesem technischen Namen existiert bereits.";
    }
    
    // Create module if no errors
    if (empty($errors)) {
        $moduleId = $db->createModule($moduleName, $moduleLabel, $_SESSION['user']['user_id']);
        
        if ($moduleId) {
            $successMessage = "Modul wurde erfolgreich erstellt!";
            // Optional: Redirect to module management page
            // header("Location: manage_module.php?module_id=" . $moduleId);
            // exit();
        } else {
            $errors[] = "Fehler beim Erstellen des Moduls. Bitte versuchen Sie es später erneut.";
        }
    }
}

// Load all modules for display
$allModules = $db->getAllModules();
?>
 
<body>
<div class="containerbody">
    <div>
        <h1>Module erstellen</h1>
        <p>Hier können Sie neue Module erstellen und vorhandene Module ansehen.</p>
    </div>
    
    <?php if (isset($successMessage)): ?>
        <div class="success-message">
            <?php echo htmlspecialchars($successMessage); ?>
        </div>
    <?php endif; ?>
    
    <?php if (!empty($errors)): ?>
        <div class="error-message">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    
    <div class="container">
        <!-- Modul-Liste -->
        <div class="panel" id="modules-panel">
            <h2>VORHANDENE MODULE</h2>
            <div id="modules-container">
                <?php if (empty($allModules)): ?>
                    <p>Keine Module vorhanden.</p>
                <?php else: ?>
                    <?php foreach ($allModules as $module): ?>
                        <div class="module-item">
                            <h3><?= htmlspecialchars($module['module_label']) ?></h3>
                            <p>ID: <?= htmlspecialchars($module['module_id']) ?></p>
                            <p>Name: <?= htmlspecialchars($module['module_name']) ?></p>
                            <?php if (isset($module['created_at'])): ?>
                                <p>Erstellt am: <?= htmlspecialchars($module['created_at']) ?></p>
                            <?php endif; ?>
                            
                            <?php
                                // Get question count
                                $questionCount = $db->countQuestionsInModule($module['module_id']);
                            ?>
                            <p>Fragen: <?= $questionCount ?></p>
                            
                            <div class="module-actions">
                                <a href="manage_module.php?module_id=<?= (int)$module['module_id'] ?>" class="edit-button">Bearbeiten</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
 
        <!-- Editor -->
        <div class="panel editor">
            <h2>NEUES MODUL ERSTELLEN</h2>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                <div class="form-group">
                    <label for="module_name">MODULE NAME (Technischer Name)</label>
                    <input type="text" id="module_name" name="module_name" placeholder="z.B. math_101" required
                           value="<?php echo htmlspecialchars($moduleName ?? ''); ?>">
                    <p class="help-text">Technischer Name des Moduls (ohne Leerzeichen, nur Buchstaben, Zahlen und Unterstriche)</p>
                </div>
                
                <div class="form-group">
                    <label for="module_label">MODULE TITEL (Anzeigename)</label>
                    <input type="text" id="module_label" name="module_label" placeholder="z.B. Mathematik Grundlagen" required
                           value="<?php echo htmlspecialchars($moduleLabel ?? ''); ?>">
                    <p class="help-text">Titel des Moduls, wie er den Benutzern angezeigt wird</p>
                </div>
                
                <div class="form-group">
                    <label>AUTOR</label>
                    <input type="text" value="<?= htmlspecialchars($_SESSION['user']['first_name'] . ' ' . $_SESSION['user']['last_name']) ?>" disabled>
                    <p class="help-text">Dieses Modul wird unter Ihrem Namen erstellt</p>
                </div>
                
                <div class="editor-buttons">
                    <button type="submit" class="save">MODUL ERSTELLEN</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.module-item {
    border: 1px solid #ddd;
    padding: 15px;
    margin-bottom: 15px;
    border-radius: 5px;
    background-color: #f9f9f9;
}

.module-item h3 {
    margin-top: 0;
    color: #333;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
}

.form-group input {
    width: 100%;
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.help-text {
    color: #666;
    font-size: 0.9em;
    margin-top: 5px;
}

.edit-button {
    display: inline-block;
    background-color: #4CAF50;
    color: white;
    padding: 8px 12px;
    text-decoration: none;
    border-radius: 4px;
    font-size: 14px;
}

.edit-button:hover {
    background-color: #45a049;
}

.success-message {
    background-color: #dff0d8;
    color: #3c763d;
    padding: 15px;
    margin-bottom: 20px;
    border: 1px solid #d6e9c6;
    border-radius: 4px;
}

.error-message {
    background-color: #f2dede;
    color: #a94442;
    padding: 15px;
    margin-bottom: 20px;
    border: 1px solid #ebccd1;
    border-radius: 4px;
}

.module-actions {
    margin-top: 10px;
}
</style>
 
</body>
 
<?php include '../includes/footer.php'; ?>
</html>