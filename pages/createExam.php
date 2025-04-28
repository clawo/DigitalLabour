<!DOCTYPE html>
<html lang="de">

<head>
    <?php include '../includes/htmlHead.php'; ?>
    <title>Mock Exam</title>
</head>

<?php include '../includes/header.php'; ?>

<?php
require_once '../includes/db_controller.php';

$db_controller = new DatabaseController();

if (!isset($_SESSION['user'])) {
    echo '<script>window.location.href = "../index.php";</script>';
    exit;
}

$moduleId = $_GET['module_id'] ?? null;
if (!$moduleId) {
    echo '<script>alert("Modul-ID nicht angegeben."); window.location.href = "../index.php";</script>';
    exit;
}
echo '<script>console.log("Module ID: ' . htmlspecialchars($moduleId) . '");</script>';

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
    echo '<script>console.log("Anzahl Fragen: ' . count($questions) . '");</script>';
    $userId = $_SESSION['user']['user_id'];

    if (!empty($questions)) {
        echo '<script>console.log("Fragen: ' . htmlspecialchars(print_r($questions, true)) . '");</script>';
        $examId = $db_controller->createMockExam($moduleId, $userId, $questions);
        if ($examId) {
            echo '<script>window.location.href = "answerExam.php?exam_id=' . $examId . '";</script>';
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
