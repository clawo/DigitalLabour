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

// load mock questions for the exam
$mockQuestions = $db_controller->getMockQuestionsByExam($examId);

if (empty($mockQuestions)) {
    echo '<script>alert("Keine Fragen für diese Prüfung gefunden."); window.location.href="../index.php";</script>';
    exit;
}
?>

<body>

<div class="contentBody">
    <h1>Aufgaben: "Modul 1"</h1>

    <div class="section">
        <strong><h2>Teil A:</h2></strong> <h2>Wissensfragen</h2>
    </div>

    <?php foreach ($mockQuestions as $index => $question): ?>
        <div class="section question">
            <h3><strong>Beispielfrage:</strong></h3>
            <p><?= htmlspecialchars($question['question']) ?></p>
            <label for="antwort">Antwort:</label><br>
            <textarea readonly rows="4" cols="60"><?= htmlspecialchars($question['answer'] ?? 'Keine Antwort vorhanden.') ?></textarea>
        </div>

        <div class="section question">
            <h3><strong>Note</strong></h3>
            <textarea readonly rows="1" cols="10"><?= htmlspecialchars($question['grade'] ?? 'Noch keine Note.') ?></textarea>
        </div>

        <div class="section question">
            <h3><strong>Begründung der Note durch ChatGPT</strong></h3>
            <label for="antwort">Antwort:</label><br>
            <textarea readonly rows="4" cols="60"><?= htmlspecialchars($question['judgement'] ?? 'Noch keine Bewertung.') ?></textarea>
        </div>
    <?php endforeach; ?>

    <div class="button-container">
        <button> Weiter </button>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

</body>
</html>