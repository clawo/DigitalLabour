<!DOCTYPE html>
<html lang="de">

<head>
    <?php include '../includes/htmlHead.php'; ?>
    <title>Mock Exam</title>
</head>
<?php include '../includes/header.php'; ?>

<?
// include the database controller
require_once '../includes/db_controller.php';

// create an instance of the DatabaseController
$db_controller = new DatabaseController();

// check if the user is logged in
if (!isset($_SESSION['user'])) {
    echo '<script>window.location.href = "../index.php";</script>';
    exit;
}

# load module information with id from URL
$moduleId = $_GET['module_id'] ?? null;
if ($moduleId) {
    $module = $db_controller->getModuleById($moduleId);

    if (empty($module)) {
        echo '<script>alert("Modul nicht gefunden.");</script>';
        echo '<script>window.location.href = "../index.php";</script>';
        exit;
    }

    $questionCount = $db_controller->getQuestionCountByModule($moduleId);
} else {
    echo '<script>alert("Modul-ID nicht angegeben.");</script>';
    echo '<script>window.location.href = "../index.php";</script>';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // select random amount of questions from module
    $questionCount = $_POST['questionCount'] ?? 1;
    $questions = $db_controller->getRandomQuestionsByModule($moduleId, $questionCount);
    $userId = $_SESSION['user']['user_id'];

    if ($questions) {
        # create mock exam
        $examId = $db_controller->createMockExam($userId, $moduleId, $questions);

        echo '<script>window.location.href = "answerExam.php?exam_id=' . $examId . '";</script>';
    } else {
        echo '<script>alert("Keine Fragen gefunden.");</script>';
    }
}
?>
<body>
<div class="body-wrapper">
        <div class="container">
            <!-- column left -->
            <div class="left">
                <div class="tag">MODUL: <?= htmlspecialchars($module['module_name']) ?></div>
                <div class="tag">TITEL: <?= htmlspecialchars($module['module_title']) ?></div>

            <!-- column right -->
            <div class="right">
                <div class="section-title">3. ANZAHL DER FRAGEN:</div>
                <input type="number" class="input-field" id="questionCount" name="questionCount" value="1" min="1" max="<?= htmlspecialchars($questionCount) ?>">

                <button class="button">Erstellen</button>
            </div>
        </div>
    </div>
</div>
</body>

<?php include '../includes/footer.php'; ?>

</html>