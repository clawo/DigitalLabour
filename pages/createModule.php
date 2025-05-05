<!DOCTYPE html>
<html lang="de">

<head>
    <?php include '../includes/htmlHead.php'; ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Module erstellen</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; margin: 0; }
        .body-wrapper { padding: 20px; }
        .container { max-width: 1200px; margin: 0 auto; display: flex; }
        .panel { flex: 1; padding: 20px; margin: 10px; border: 1px solid #ddd; border-radius: 5px; }
        .error { color: #ff0000; background-color: #ffeeee; padding: 10px; border-radius: 5px; }
        .success { color: #008000; background-color: #eeffee; padding: 10px; border-radius: 5px; }
        .module-item { border: 1px solid #ddd; padding: 15px; margin-bottom: 15px; border-radius: 5px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="text"] { width: 100%; padding: 8px; margin-bottom: 15px; border: 1px solid #ddd; border-radius: 4px; }
        button { background-color: #4CAF50; color: white; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background-color: #45a049; }
    </style>
</head>

<?php include '../includes/header.php'; ?>
<?php
// This is a simplified version of the createModule.php page
// that uses direct database queries to bypass any issues with the DatabaseController

// Include necessary files
require_once '../includes/db_connect.php';
require_once '../includes/db_controller.php';
session_start();

// Function to check if a user is logged in and authorized
function checkAuth() {
    if (!isset($_SESSION['user'])) {
        return false;
    }
    
    if ($_SESSION['user']['role_id'] !== 1) {
        return false;
    }
    
    return true;
}

// Get table structure to verify columns
function getTableStructure($tableName) {
    $pdo = getDB();
    $stmt = $pdo->query("DESCRIBE $tableName");
    $columns = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $columns[] = $row['Field'];
    }
    return $columns;
}

// Direct module creation function
function createModuleDirect($moduleName, $moduleLabel, $createdBy) {
    try {
        // Get direct DB connection
        $pdo = getDB();
        
        // Check if module name already exists
        $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM modules WHERE module_name = ?");
        $checkStmt->execute([$moduleName]);
        if ($checkStmt->fetchColumn() > 0) {
            return [false, "Ein Modul mit diesem Namen existiert bereits."];
        }
        
        // Check table structure
        $columns = getTableStructure("modules");
        
        // Create SQL based on actual table structure
        $sql = "INSERT INTO modules (module_name, module_label, created_by";
        $values = "(?, ?, ?";
        $params = [$moduleName, $moduleLabel, $createdBy];
        
        // Add created_at if it exists
        if (in_array('created_at', $columns)) {
            $sql .= ", created_at";
            $values .= ", NOW()";
        }
        
        // Close the SQL statement
        $sql .= ") VALUES " . $values . ")";
        
        // Log the SQL for debugging
        error_log("SQL query: " . $sql);
        error_log("Parameters: " . implode(", ", $params));
        
        // Create the module
        $stmt = $pdo->prepare($sql);
        $success = $stmt->execute($params);
        
        if (!$success) {
            $error = implode(", ", $stmt->errorInfo());
            return [false, "Datenbankfehler: " . $error];
        }
        
        return [true, $pdo->lastInsertId()];
    } catch (Exception $e) {
        error_log("Error creating module: " . $e->getMessage());
        return [false, "Fehler: " . $e->getMessage()];
    }
}

// Initialize variables
$error = "";
$success = "";
$moduleName = "";
$moduleLabel = "";

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $moduleName = trim($_POST['module_name'] ?? '');
    $moduleLabel = trim($_POST['module_label'] ?? '');
    
    // Basic validation
    if (empty($moduleName) || empty($moduleLabel)) {
        $error = "Beide Felder müssen ausgefüllt sein.";
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $moduleName)) {
        $error = "Der technische Name darf nur Buchstaben, Zahlen und Unterstriche enthalten.";
    } else {
        // Try to create the module
        list($result, $message) = createModuleDirect(
            $moduleName, 
            $moduleLabel, 
            $_SESSION['user']['user_id']
        );
        
        if ($result) {
            $success = "Modul erfolgreich erstellt! Modul-ID: " . $message;
            $moduleName = "";
            $moduleLabel = "";
        } else {
            $error = $message;
        }
    }
}

// Check if user is authorized
if (!checkAuth()) {
    echo "Nicht autorisiert. <a href='../index.php'>Zurück</a>";
    exit;
}

// Get existing modules
$db = getDB();
$modulesStmt = $db->query("SELECT * FROM modules ORDER BY module_label");
$modules = $modulesStmt->fetchAll(PDO::FETCH_ASSOC);

// Debug table structure for modules table
$moduleColumns = getTableStructure("modules");
error_log("Modules table columns: " . implode(", ", $moduleColumns));
?>
<body>
<div class="body-wrapper">
    <h1>Module erstellen</h1>
    <p>Hier können Sie neue Module erstellen und vorhandene Module ansehen.</p>
    
    <?php if (!empty($error)): ?>
        <div class="error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    
    <?php if (!empty($success)): ?>
        <div class="success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>
    
    <div class="container">
        <div class="panel">
            <h2>VORHANDENE MODULE</h2>
            <?php if (empty($modules)): ?>
                <p>Keine Module vorhanden.</p>
            <?php else: ?>
                <?php foreach ($modules as $module): ?>
                    <div class="module-item">
                        <h3><?php echo htmlspecialchars($module['module_label']); ?></h3>
                        <p>ID: <?php echo htmlspecialchars($module['module_id']); ?></p>
                        <p>Name: <?php echo htmlspecialchars($module['module_name']); ?></p>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <div class="panel">
            <h2>NEUES MODUL ERSTELLEN</h2>
            <form method="post" action="">
                <div>
                    <label for="module_name">MODULE NAME (Technischer Name)</label>
                    <input type="text" id="module_name" name="module_name" 
                           placeholder="z.B. math_101" required
                           value="<?php echo htmlspecialchars($moduleName); ?>">
                    <p>Technischer Name des Moduls (ohne Leerzeichen, nur Buchstaben, Zahlen und Unterstriche)</p>
                </div>
                
                <div>
                    <label for="module_label">MODULE TITEL (Anzeigename)</label>
                    <input type="text" id="module_label" name="module_label" 
                           placeholder="z.B. Mathematik Grundlagen" required
                           value="<?php echo htmlspecialchars($moduleLabel); ?>">
                    <p>Titel des Moduls, wie er den Benutzern angezeigt wird</p>
                </div>
                
                <div>
                    <label>AUTOR</label>
                    <input type="text" 
                           value="<?php echo htmlspecialchars($_SESSION['user']['first_name'] . ' ' . $_SESSION['user']['last_name']); ?>" 
                           disabled>
                    <p>Dieses Modul wird unter Ihrem Namen erstellt</p>
                </div>
                
                <button type="submit">MODUL ERSTELLEN</button>
            </form>
        </div>
    </div>
    
    <!-- Debug info - remove in production -->
<!--    <div style="margin-top: 30px; padding: 15px; background-color: #f8f8f8; border: 1px solid #ddd;">-->
<!--        <h3>Debug Information</h3>-->
<!--        <p>Module table structure: --><?php //echo implode(", ", $moduleColumns); ?><!--</p>-->
<!--    </div>-->
</div>
</body>
<?php include '../includes/footer.php'; ?>
</html>