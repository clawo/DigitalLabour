<!DOCTYPE html>
<html lang="de">

<head>
    <?php include '../includes/htmlHead.php'; ?>
    <title>Answer Exam</title>
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

// check if exam is assigned to the user
$userId = $_SESSION['user']['user_id'];
$exam = $db_controller->checkUserExamAccess($userId, $examId);
if (!$exam) {
    echo '<script>alert("Zugriff auf diese Prüfung nicht erlaubt."); window.location.href="../index.php";</script>';
    exit;
}

$mockQuestions = $db_controller->getMockQuestionsByExam($examId);

if (empty($mockQuestions)) {
    echo '<script>alert("Keine Fragen für diese Prüfung gefunden."); window.location.href="../index.php";</script>';
    exit;
}
?>

<body>
<div class="body-wrapper">
    <div class="container">
        <div class="left">
            <div class="tag">PRÜFUNG: Mock Exam #<?= htmlspecialchars($examId) ?></div>
            <div class="tag">Fragenanzahl: <?= count($mockQuestions) ?></div>
        </div>

        <div class="right">
            <form method="post" action="saveAnswers.php?exam_id=<?= htmlspecialchars($examId) ?>">
                <div class="section-title">Beantworte die Fragen:</div>

                <?php foreach ($mockQuestions as $index => $question): ?>
                    <div class="section question">
                        <h3>Frage <?= $index + 1 ?>:</h3>
                        <p><?= htmlspecialchars($question['question']) ?></p>

                        <label for="antwort_<?= (int)$question['question_id'] ?>">Antwort:</label><br>
                        <textarea id="antwort_<?= (int)$question['question_id'] ?>"
                                  name="antworten[<?= (int)$question['question_id'] ?>]"
                                  rows="4" cols="60"
                                  required></textarea>
                    </div>
                <?php endforeach; ?>

                <div class="button-container">
                    <button type="submit" class="button">Antworten speichern</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
</body>

</html>