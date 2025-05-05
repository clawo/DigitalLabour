<!DOCTYPE html>
<html lang="de">

<head>
    <?php include '../includes/htmlHead.php'; ?>
    <title>Answer Exam</title>
</head>

<?php include '../includes/header.php'; ?>

<?php
require_once '../includes/db_controller.php';
require_once '../includes/gpt_evaluate.php';

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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['antworten'])) {
    foreach ($_POST['antworten'] as $questionId => $answer) {
        $questionId = (int)$questionId;
        $answer = trim($answer);

        $db_controller->updateMockAnswer($examId, $questionId, $answer);

        // evaluate the answer using ChatGPT
        $evaluation = evaluateAnswer($mockQuestions[$questionId]['question'], $answer);
        if ($evaluation == "Fehler bei der API-Anfrage." || $evaluation == "API-Schlüssel nicht gesetzt.") {
            echo '<script>alert("Fehler bei der Auswertung der Antwort.");</script>';
            continue;
        }

        $feedback = extractFeedback($evaluation);
        $grade = extractGrade($evaluation);

        // save the evaluation result
        $db_controller->updateMockEvaluation($examId, $questionId, $feedback, $grade);
    }

    $averageGrade = $db_controller->getAverageGradeByExam($examId);
    if ($averageGrade) {
        preg_match('/\d+(\.\d+)?/', $averageGrade, $matches);
        $averageGrade = $matches[0];

        $db_controller->updateMockGrade($examId, $averageGrade);
    }

    echo '<script>window.location.href = "grading.php?exam_id=' . $examId . '";</script>';
    exit;
}
?>

<body>
<div class="contentBody">
    <h1>PRÜFUNG: Mock Exam #<?= htmlspecialchars($examId) ?></h1>
    <h2>Fragenanzahl: <?= count($mockQuestions) ?></h2>
    <h2>Beantworte die Fragen:</h2>

    <div class="section">
        <h2><strong>Teil A: Wissensfragen</strong></h2>
    </div>

    <form method="post" action="">
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

<?php include '../includes/footer.php'; ?>
</body>

</html>