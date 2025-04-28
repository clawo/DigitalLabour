<!DOCTYPE html>
<html lang="de">

<head>
    <?php include '../includes/htmlHead.php'; ?>
    <title>Grading with ChatGPT</title>
</head>

<?php include '../includes/header.php'; ?>

<?php
require_once '../includes/db_controller.php';

$db = new DatabaseController();

// Check if user is logged in
if (!isset($_SESSION['user'])) {
    echo '<script>window.location.href = "../index.php";</script>';
    exit();
}

// Check if existing module ID is passed
$moduleId = $_GET['module_id'] ?? null;
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

// Check if user is allowed to edit
$moduleCreatedByUser = $db->checkModuleCreatedByUser($moduleId, $_SESSION['user']['user_id']);
if ($_SESSION['user']['role_id'] !== 1 || !$moduleCreatedByUser) {
    echo '<script>alert("Zugriff auf diese Seite nicht erlaubt."); window.location.href = "../index.php";</script>';
    exit();
}

// Handle AJAX POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

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

    // Wenn kein gültiger action vorhanden ist:
    echo json_encode(['success' => false, 'error' => 'Ungültige Anfrage.']);
    exit();
}

// load questions
$questions = $db->getQuestionsByModule($moduleId);
?>

<body>
<div class="containerbody">
    <div>
        <h1>GRUNDLAGEN DER INFORMATIK</h1>
        <p>Prof. Dr. Kamyar Sarshar</p>
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
                <button class="save">SPEICHERN</button>
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
    }

    document.querySelector('.save').addEventListener('click', function () {
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