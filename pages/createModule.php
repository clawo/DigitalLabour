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

// For creating modules, we'll need to add a method to the DatabaseController class
// Let's modify our approach to be more compatible with the existing code

// The user needs to implement this function in the DatabaseController class
// This is just a placeholder - we're referencing the function that needs to be added
/*
// Add this method to DatabaseController class:
public function createModule($data) {
    $stmt = $this->pdo->prepare("
        INSERT INTO modules (module_name, module_label, created_by, created_at)
        VALUES (?, ?, ?, NOW())
    ");
    $success = $stmt->execute([
        $data['module_name'],
        $data['module_label'],
        $data['created_by']
    ]);
    
    if ($success) {
        return $this->pdo->lastInsertId();
    }
    
    return false;
}
*/

// handle AJAX POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
 
    if ($action === 'createModule') {
        $moduleName = trim($_POST['module_name'] ?? '');
        $moduleLabel = trim($_POST['module_label'] ?? '');
        $createdBy = $_SESSION['user']['user_id'];
        
        if (empty($moduleName) || empty($moduleLabel)) {
            echo json_encode(['success' => false, 'error' => 'Bitte alle Pflichtfelder ausfüllen.']);
            exit();
        }
        
        $moduleData = [
            'module_name' => $moduleName,
            'module_label' => $moduleLabel,
            'created_by' => $createdBy
        ];
        
        try {
            // Direct database query since we don't have a createModule method yet
            $stmt = getDB()->prepare("
                INSERT INTO modules (module_name, module_label, created_by, created_at)
                VALUES (?, ?, ?, NOW())
            ");
            
            $success = $stmt->execute([
                $moduleData['module_name'],
                $moduleData['module_label'],
                $moduleData['created_by']
            ]);
            
            if ($success) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Fehler beim Erstellen des Moduls.']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => 'Datenbankfehler: ' . $e->getMessage()]);
        }
        exit();
    }
    
    if ($action === 'getAllModules') {
        $modules = $db->getAllModules();
        echo json_encode(['success' => true, 'modules' => $modules]);
        exit();
    }
    
    echo json_encode(['success' => false, 'error' => 'Ungültige Anfrage.']);
    exit();
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
                            <a href="manage_module.php?module_id=<?= (int)$module['module_id'] ?>" class="edit-button">Bearbeiten</a>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
 
        <!-- Editor -->
        <div class="panel editor">
            <h2>NEUES MODUL ERSTELLEN</h2>
            <form id="module-form">
                <div class="form-group">
                    <label for="module_name">MODULE NAME (Technischer Name)</label>
                    <input type="text" id="module_name" name="module_name" placeholder="z.B. math_101" required>
                    <p class="help-text">Technischer Name des Moduls (ohne Leerzeichen, nur Buchstaben, Zahlen und Unterstriche)</p>
                </div>
                
                <div class="form-group">
                    <label for="module_label">MODULE TITEL (Anzeigename)</label>
                    <input type="text" id="module_label" name="module_label" placeholder="z.B. Mathematik Grundlagen" required>
                    <p class="help-text">Titel des Moduls, wie er den Benutzern angezeigt wird</p>
                </div>
                
                <div class="form-group">
                    <label>AUTOR</label>
                    <input type="text" value="<?= htmlspecialchars($_SESSION['user']['first_name'] . ' ' . $_SESSION['user']['last_name']) ?>" disabled>
                    <p class="help-text">Dieses Modul wird unter Ihrem Namen erstellt</p>
                </div>
                
                <div class="editor-buttons">
                    <button type="submit" id="create-module-btn" class="save">MODUL ERSTELLEN</button>
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
</style>
 
<script>
document.getElementById('module-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const moduleName = document.getElementById('module_name').value.trim();
    const moduleLabel = document.getElementById('module_label').value.trim();
    
    // Basic validation
    if (!moduleName || !moduleLabel) {
        alert('Bitte füllen Sie alle Pflichtfelder aus.');
        return;
    }
    
    // Validate module_name format (only letters, numbers, and underscores)
    if (!/^[a-zA-Z0-9_]+$/.test(moduleName)) {
        alert('Der technische Name darf nur Buchstaben, Zahlen und Unterstriche enthalten.');
        return;
    }
    
    // Send AJAX request
    fetch(window.location.href, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({
            action: 'createModule',
            module_name: moduleName,
            module_label: moduleLabel
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Modul wurde erfolgreich erstellt!');
            // Clear form
            document.getElementById('module_name').value = '';
            document.getElementById('module_label').value = '';
            // Reload the page to show the new module
            window.location.reload();
        } else {
            alert('Fehler beim Erstellen des Moduls: ' + (data.error || 'Unbekannter Fehler'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Fehler beim Erstellen des Moduls.');
    });
});

// Function to refresh module list
function refreshModuleList() {
    fetch(window.location.href, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({
            action: 'getAllModules'
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const container = document.getElementById('modules-container');
            
            if (!data.modules || data.modules.length === 0) {
                container.innerHTML = '<p>Keine Module vorhanden.</p>';
                return;
            }
            
            container.innerHTML = '';
            
            data.modules.forEach(module => {
                const moduleDiv = document.createElement('div');
                moduleDiv.className = 'module-item';
                
                let createdAtText = '';
                if (module.created_at) {
                    createdAtText = `<p>Erstellt am: ${module.created_at}</p>`;
                }
                
                moduleDiv.innerHTML = `
                    <h3>${module.module_label}</h3>
                    <p>ID: ${module.module_id}</p>
                    <p>Name: ${module.module_name}</p>
                    ${createdAtText}
                    <a href="manage_module.php?module_id=${module.module_id}" class="edit-button">Bearbeiten</a>
                `;
                
                container.appendChild(moduleDiv);
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}
</script>
 
</body>
 
<?php include '../includes/footer.php'; ?>
</html>