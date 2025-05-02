<!DOCTYPE html>
<html lang="de">

<head>
    <?php include '../includes/htmlHead.php'; ?>
    <title>Module verwalten</title>
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

// check if existing module ID is passed
$moduleId = $_GET['module_id'] ?? null;
$module = null;
if ($moduleId) {
    $moduleId = (int)$moduleId;
    if ($moduleId <= 0) {
        echo '<script>alert("Ungültige Modul-ID."); window.location.href = "../index.php";</script>';
        exit();
    }

    $module = $db->getModuleById($moduleId);
    if (!$module) {
        echo '<script>alert("Modul nicht gefunden."); window.location.href = "../index.php";</script>';
        exit();
    }
} else {
    echo '<script>alert("Keine Modul-ID übergeben."); window.location.href = "../index.php";</script>';
    exit();
}

// check if user is allowed to edit
$moduleCreatedByUser = $db->checkModuleCreatedByUser($moduleId, $_SESSION['user']['user_id']);
if ($_SESSION['user']['role_id'] !== 1 || !$moduleCreatedByUser) {
    echo '<script>alert("Zugriff auf diese Seite nicht erlaubt."); window.location.href = "../index.php";</script>';
    exit();
}

// get module creator
$moduleCreator = $db->getUserById($module['created_by']);

// handle AJAX POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'create') {
        $moduleId = (int)($_POST['module_id'] ?? 0);
        $text = trim($_POST['frage'] ?? '');
        if ($moduleId > 0) {
            $newQuestionId = $db->addQuestion($moduleId, $text);
            if ($newQuestionId) {
                echo json_encode(['success' => true, 'question_id' => $newQuestionId]);
                exit();
            }
        }
        echo json_encode(['success' => false, 'error' => 'Erstellung fehlgeschlagen.']);
        exit();
    }

    if ($action === 'save') {
        $questionId = (int)($_POST['question_id'] ?? 0);
        $newText = trim($_POST['frage'] ?? '');
        if ($questionId > 0 && $newText !== '') {
            $success = $db->updateQuestion($questionId, $newText);

            echo json_encode(['success' => $success]);
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

    echo json_encode(['success' => false, 'error' => 'Ungültige Anfrage.']);
    exit();
}

// load questions
$questions = $db->getQuestionsByModule($moduleId);
?>

<body>
<div class="containerbody">
    <div>
        <h1><?= htmlspecialchars($module['module_label']) ?></h1>
        <p>Angelegt von: <?= htmlspecialchars($moduleCreator['first_name'] . ' ' . $moduleCreator['last_name']) ?></p>
    </div>

    <div class="search-bar">
        <input type="text" placeholder="Fragen durchsuchen">
    </div>

    <div class="container">
        <!-- Aufgaben-Liste -->
        <div class="panel" id="task-panel">
            <h2>WÄHLEN SIE EINE AUFGABE AUS</h2>

            <?php foreach ($questions as $question): ?>
                <div class="task">
                    <h3>Frage #<?= htmlspecialchars($question['question_id']) ?></h3>
                    <p><?= nl2br(htmlspecialchars($question['question'])) ?></p>
                    <span class="edit-icon" onclick='selectQuestion(<?= (int)$question['question_id'] ?>, <?= json_encode($question['question'], JSON_HEX_APOS | JSON_HEX_QUOT) ?>)'>✏️</span>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Editor -->
        <div class="panel editor">
            <h2>EDITOR</h2>
            <label for="titel">TITEL BEARBEITEN</label>
            <input type="text" id="titel" placeholder="Titel der Frage..." readonly>
            <label for="frage">FRAGE BEARBEITEN</label>
            <textarea id="frage" rows="10" placeholder="Fragetext..."></textarea>
            <div class="editor-buttons">
                <button class="delete">AUFGABE LÖSCHEN</button>
                <button id="save-btn" class="save" style="display: none;">SPEICHERN</button>
                <button id="create-btn" class="save">FRAGE ERSTELLEN</button>
            </div>
        </div>
    </div>
</div>

<script>
    let selectedQuestionId = null;

    function selectQuestion(id, frageText) {
        selectedQuestionId = id;
        document.getElementById('titel').value = "Frage #" + id;
        document.getElementById('frage').value = frageText;

        document.getElementById('save-btn').style.display = 'inline-block';
        document.getElementById('create-btn').style.display = 'none';
    }

    document.getElementById('create-btn').addEventListener('click', function () {
        const frage = document.getElementById('frage').value;

        if (!frage.trim()) {
            alert('Bitte gib einen Fragetext ein.');
            return;
        }

        fetch(window.location.href, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({
                action: 'create',
                module_id: <?= (int)$moduleId ?>,
                frage: frage
            })
        })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.question_id) {
                    window.location.reload();
                } else {
                    alert('Erstellung fehlgeschlagen.');
                }
            });
    });

    document.getElementById('save-btn').addEventListener('click', function () {
        if (!selectedQuestionId) {
            alert('Bitte wähle zuerst eine Aufgabe aus.');
            return;
        }
        const frage = document.getElementById('frage').value;

        fetch(window.location.href, {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: new URLSearchParams({
                action: 'save',
                question_id: selectedQuestionId,
                frage: frage
            })
        }).then(function () {
            window.location.reload();
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
        }).then(function () {
            window.location.reload();
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

</html>