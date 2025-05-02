<!DOCTYPE html>
<html lang="de">
 
<head>
    <?php include '../includes/htmlHead.php'; ?>
    <title>Fragen erstellen</title>
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
 
// check if user is allowed to edit
if ($_SESSION['user']['role_id'] !== 1) {
    echo '<script>alert("Zugriff auf diese Seite nicht erlaubt."); window.location.href = "../index.php";</script>';
    exit();
}

// Load all available modules for selection
$allModules = $db->getAllModules();

// handle AJAX POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
 
    if ($action === 'create') {
        $moduleId = (int)($_POST['module_id'] ?? 0);
        $text = trim($_POST['frage'] ?? '');
        if ($moduleId > 0 && $text !== '') {
            $success = $db->addQuestion($moduleId, $text);
            echo json_encode(['success' => $success]);
            exit();
        } else {
            echo json_encode(['success' => false, 'error' => 'Bitte wähle ein Modul aus und gib einen Fragetext ein.']);
            exit();
        }
    }
 
    if ($action === 'save') {
        $questionId = (int)($_POST['question_id'] ?? 0);
        $moduleId = (int)($_POST['module_id'] ?? 0);
        $newText = trim($_POST['frage'] ?? '');
        if ($questionId > 0 && $moduleId > 0 && $newText !== '') {
            $success = $db->updateQuestion($questionId, $newText);
            echo json_encode(['success' => $success]);
            exit();
        } else {
            echo json_encode(['success' => false, 'error' => 'Ungültige Daten.']);
            exit();
        }
    }
 
    if ($action === 'delete') {
        $questionId = (int)($_POST['question_id'] ?? 0);
        if ($questionId > 0) {
            $success = $db->deleteQuestion($questionId);
            echo json_encode(['success' => $success]);
            exit();
        }
    }
    
    if ($action === 'loadQuestions') {
        $moduleId = (int)($_POST['module_id'] ?? 0);
        if ($moduleId > 0) {
            $questions = $db->getQuestionsByModule($moduleId);
            echo json_encode(['success' => true, 'questions' => $questions]);
            exit();
        } else {
            echo json_encode(['success' => false, 'error' => 'Kein Modul ausgewählt.']);
            exit();
        }
    }
    
    if ($action === 'getModuleInfo') {
        $moduleId = (int)($_POST['module_id'] ?? 0);
        if ($moduleId > 0) {
            $module = $db->getModuleById($moduleId);
            if ($module && isset($module['created_by'])) {
                $creator = $db->getUserById($module['created_by']);
                $creatorName = $creator ? $creator['first_name'] . ' ' . $creator['last_name'] : 'Unbekannt';
                echo json_encode(['success' => true, 'creatorName' => $creatorName]);
                exit();
            }
        }
        echo json_encode(['success' => false, 'error' => 'Modulinformationen nicht verfügbar.']);
        exit();
    }
 
    echo json_encode(['success' => false, 'error' => 'Ungültige Anfrage.']);
    exit();
}
?>
 
<body>
<div class="containerbody">
    <div>
        <h1>Fragen erstellen</h1>
    </div>
    
    <div class="module-selection">
        <label for="module-select">Modul auswählen:</label>
        <select id="module-select">
            <option value="">-- Bitte wählen --</option>
            <?php foreach ($allModules as $module): ?>
                <option value="<?= (int)$module['module_id'] ?>">
                    <?= htmlspecialchars($module['module_label']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <div id="module-info" style="display: none;">
            <p>Ausgewähltes Modul: <span id="selected-module-name"></span></p>
            <p>Angelegt von: <span id="module-creator"></span></p>
        </div>
    </div>
 
    <div class="search-bar">
        <input type="text" placeholder="Fragen durchsuchen">
    </div>
 
    <div class="container">
        <!-- Aufgaben-Liste -->
        <div class="panel" id="task-panel">
            <h2>WÄHLEN SIE EINE AUFGABE AUS</h2>
            <div id="questions-container">
                <p id="no-module-selected">Bitte wählen Sie zuerst ein Modul aus.</p>
                <!-- Questions will be loaded dynamically here -->
            </div>
        </div>
 
        <!-- Editor -->
        <div class="panel editor">
            <h2>EDITOR</h2>
            <label for="titel">TITEL BEARBEITEN</label>
            <input type="text" id="titel" placeholder="Titel der Frage..." readonly>
            <label for="frage">FRAGE BEARBEITEN</label>
            <textarea id="frage" rows="10" placeholder="Fragetext..."></textarea>
            <div class="editor-buttons">
                <button class="delete" disabled>AUFGABE LÖSCHEN</button>
                <button id="save-btn" class="save" style="display: none;">SPEICHERN</button>
                <button id="create-btn" class="save" disabled>FRAGE ERSTELLEN</button>
            </div>
        </div>
    </div>
</div>
 
<script>
    let selectedQuestionId = null;
    let selectedModuleId = null;
    
    // Module selection change handler
    document.getElementById('module-select').addEventListener('change', function() {
        selectedModuleId = parseInt(this.value) || null;
        
        // Reset question selection
        selectedQuestionId = null;
        document.getElementById('titel').value = "";
        document.getElementById('frage').value = "";
        document.getElementById('save-btn').style.display = 'none';
        document.getElementById('create-btn').style.display = 'inline-block';
        
        // Enable/disable create button based on module selection
        document.getElementById('create-btn').disabled = !selectedModuleId;
        document.querySelector('.delete').disabled = true;
        
        if (selectedModuleId) {
            // Show module info
            const selectedOption = this.options[this.selectedIndex];
            document.getElementById('selected-module-name').textContent = selectedOption.textContent.trim();
            
            // Load module creator info
            fetch(window.location.href, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({
                    action: 'getModuleInfo',
                    module_id: selectedModuleId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('module-creator').textContent = data.creatorName;
                    document.getElementById('module-info').style.display = 'block';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('module-info').style.display = 'block';
            });
            
            // Load questions for this module
            loadQuestions(selectedModuleId);
        } else {
            document.getElementById('module-info').style.display = 'none';
            document.getElementById('questions-container').innerHTML = 
                '<p id="no-module-selected">Bitte wählen Sie zuerst ein Modul aus.</p>';
        }
    });
    
    function loadQuestions(moduleId) {
        fetch(window.location.href, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({
                action: 'loadQuestions',
                module_id: moduleId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderQuestions(data.questions);
            } else {
                alert('Fehler beim Laden der Fragen: ' + data.error);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Fehler beim Laden der Fragen.');
        });
    }
    
    function renderQuestions(questions) {
        const container = document.getElementById('questions-container');
        
        if (!questions || questions.length === 0) {
            container.innerHTML = '<p>Keine Fragen für dieses Modul vorhanden.</p>';
            return;
        }
        
        container.innerHTML = '';
        
        questions.forEach(question => {
            const taskDiv = document.createElement('div');
            taskDiv.className = 'task';
            taskDiv.innerHTML = `
                <h3>Frage #${question.question_id}</h3>
                <p>${question.question.replace(/\n/g, '<br>')}</p>
                <span class="edit-icon">✏️</span>
            `;
            
            taskDiv.querySelector('.edit-icon').addEventListener('click', function() {
                selectQuestion(question.question_id, question.question);
            });
            
            container.appendChild(taskDiv);
        });
    }
 
    function selectQuestion(id, frageText) {
        selectedQuestionId = id;
        document.getElementById('titel').value = "Frage #" + id;
        document.getElementById('frage').value = frageText;
 
        document.getElementById('save-btn').style.display = 'inline-block';
        document.getElementById('create-btn').style.display = 'none';
        document.querySelector('.delete').disabled = false;
    }
 
    document.getElementById('create-btn').addEventListener('click', function () {
        const frage = document.getElementById('frage').value;
 
        if (!selectedModuleId) {
            alert('Bitte wähle zuerst ein Modul aus.');
            return;
        }
        
        if (!frage.trim()) {
            alert('Bitte gib einen Fragetext ein.');
            return;
        }
 
        fetch(window.location.href, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({
                action: 'create',
                module_id: selectedModuleId,
                frage: frage
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Reload questions for the current module
                loadQuestions(selectedModuleId);
                // Clear the form
                document.getElementById('frage').value = '';
            } else {
                alert('Fehler beim Erstellen der Frage: ' + (data.error || 'Unbekannter Fehler'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Fehler beim Erstellen der Frage.');
        });
    });
 
    document.getElementById('save-btn').addEventListener('click', function () {
        if (!selectedQuestionId) {
            alert('Bitte wähle zuerst eine Aufgabe aus.');
            return;
        }
        
        if (!selectedModuleId) {
            alert('Kein Modul ausgewählt.');
            return;
        }
        
        const frage = document.getElementById('frage').value;
        
        if (!frage.trim()) {
            alert('Bitte gib einen Fragetext ein.');
            return;
        }
 
        fetch(window.location.href, {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: new URLSearchParams({
                action: 'save',
                question_id: selectedQuestionId,
                module_id: selectedModuleId,
                frage: frage
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Reload questions for the current module
                loadQuestions(selectedModuleId);
            } else {
                alert('Fehler beim Speichern der Frage: ' + (data.error || 'Unbekannter Fehler'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Fehler beim Speichern der Frage.');
        });
    });
 
    document.querySelector('.delete').addEventListener('click', function () {
        if (!selectedQuestionId) {
            alert('Bitte wähle zuerst eine Aufgabe aus.');
            return;
        }
        if (!confirm('Willst du diese Aufgabe wirklich löschen?')) {
            return;
        }
 
        fetch(window.location.href, {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: new URLSearchParams({
                action: 'delete',
                question_id: selectedQuestionId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Reload questions for the current module
                loadQuestions(selectedModuleId);
                // Reset the form
                document.getElementById('titel').value = '';
                document.getElementById('frage').value = '';
                document.getElementById('save-btn').style.display = 'none';
                document.getElementById('create-btn').style.display = 'inline-block';
                document.querySelector('.delete').disabled = true;
                selectedQuestionId = null;
            } else {
                alert('Fehler beim Löschen der Frage: ' + (data.error || 'Unbekannter Fehler'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Fehler beim Löschen der Frage.');
        });
    });
 
    document.querySelector('.search-bar input').addEventListener('input', function () {
        const searchTerm = this.value.toLowerCase();
        const tasks = document.querySelectorAll('.task');
 
        tasks.forEach(task => {
            const text = task.textContent.toLowerCase();
            if (text.includes(searchTerm)) {
                task.style.display = 'block';
            } else {
                task.style.display = 'none';
            }
        });
    });
</script>
 
</body>
 
<?php include '../includes/footer.php'; ?>