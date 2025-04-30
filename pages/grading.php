<!DOCTYPE html>
<html lang="de">

<head>
    <?php include '../includes/htmlHead.php'; ?>
    <title>Grading with ChatGPT</title>
</head>

<?php include '../includes/header.php'; ?>

<?php
require_once '../includes/db_controller.php';

if (!isset($_SESSION['user'])) {
    echo '<script>window.location.href = "../index.php";</script>';
    exit;
}

$db_controller = new DatabaseController();

$examId = $_GET['exam_id'] ?? null;
if (!$examId) {
    echo '<script>alert("Keine Prüfungs-ID übergeben."); window.location.href="../index.php";</script>';
    exit;
}

// check if exam belongs to the user
$userId = $_SESSION['user']['user_id'];
$exam = $db_controller->checkUserExamAccess($userId, $examId);
if (!$exam) {
    echo '<script>alert("Zugriff auf diese Prüfung nicht erlaubt."); window.location.href="../index.php";</script>';
    exit;
}

// load mock exam
$mockExam = $db_controller->getMockExam($examId);
$module = $db_controller->getModuleById($mockExam['module_id']);

// get average grade
$averageGrade = $db_controller->getAverageGradeByExam($examId);

// load mock questions for the exam
$mockQuestions = $db_controller->getMockQuestionsByExam($examId);

if (empty($mockQuestions)) {
    echo '<script>alert("Keine Fragen für diese Prüfung gefunden."); window.location.href="../index.php";</script>';
    exit;
}

?>

<body>

<div class="contentBody">
    <h1>Aufgaben: <?= htmlspecialchars($module['module_name']) ?></h1>

    <div class="section">
        <h2>Wissensfragen</h2>
    </div>

    <?php foreach ($mockQuestions as $index => $question): ?>
        <div class="section question">
            <h3><strong>Frage: </strong><?= htmlspecialchars($question['question']) ?></h3>
            <label>Antwort:</label><br>
            <textarea readonly rows="4" cols="60"><?= htmlspecialchars($question['answer'] ?? 'Keine Antwort vorhanden.') ?></textarea>
            <br><br>
            <label>Bewertung von ChatGPT:</label><br>
            <textarea readonly rows="4" cols="60"><?= htmlspecialchars($question['judgement'] ?? 'Keine Antwort vorhanden.') ?></textarea>
            <br><br>
            <label>Note:</label><br>
            <textarea readonly rows="1" cols="10"><?= htmlspecialchars($question['grade'] ?? 'Noch keine Note.') ?></textarea>
        </div>
    <?php endforeach; ?>

    <h3>Note: <?= htmlspecialchars($averageGrade) ?></h3>

</div>

<?php include '../includes/footer.php'; ?>

</body>
</html>