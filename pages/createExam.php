<!DOCTYPE html>
<html lang="de">

<head>
    <?php include '../includes/htmlHead.php'; ?>
    <title>Mock Exam</title>
</head>
<?php include '../includes/header.php'; ?>

<?php
require_once '../includes/db_controller.php';

// check if the user is logged in
if (!isset($_SESSION['user'])) {
    echo '<script>window.location.href = "../index.php";</script>';
    exit;
}

$db_controller = new DatabaseController();

$moduleId = $_GET['module_id'] ?? null;
if (!$moduleId) {
    echo '<script>alert("Modul-ID nicht angegeben."); window.location.href = "../index.php";</script>';
    exit;
}

$module = $db_controller->getModuleById($moduleId);
if (!$module) {
    echo '<script>alert("Modul nicht gefunden."); window.location.href = "../index.php";</script>';
    exit;
}

$questionCountAvailable = $db_controller->getQuestionCountByModule($moduleId);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $questionCount = (int) ($_POST['questionCount'] ?? 1);
    if ($questionCount < 1) {
        $questionCount = 1;
    }

    $questions = $db_controller->getRandomQuestionsByModule($moduleId, $questionCount);
    $userId = $_SESSION['user']['user_id'];

    if (!empty($questions)) {
        $examId = $db_controller->createMockExam($userId, $moduleId, 0); // Grade=0
        if ($examId) {
            header('Location: answerExam.php?exam_id=' . urlencode($examId));
            exit;
        } else {
            echo '<script>alert("Fehler beim Erstellen des Mock Exams.");</script>';
        }
    } else {
        echo '<script>alert("Keine Fragen gefunden.");</script>';
    }
}
?>

<body>
    <div class="body-wrapper">
        <div class="container">
            <div class="left">
                <div class="tag">MODUL: <?= htmlspecialchars($module['module_name']) ?></div>
                <div class="tag">LABEL: <?= htmlspecialchars($module['module_label']) ?></div>
            </div>

            <div class="right">
                <form method="post" action="">
                    <div class="section-title">3. ANZAHL DER FRAGEN:</div>
                    <input type="number" class="input-field" id="questionCount" name="questionCount"
                           value="1" min="1" max="<?= (int)$questionCountAvailable ?>" required>

                    <button type="submit" class="button">Erstellen</button>
                </form>
            </div>
        </div>
    </div>
</body>

<?php include '../includes/footer.php'; ?>

</html>
