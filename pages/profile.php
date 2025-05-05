<!DOCTYPE html>
<html lang="de">

<head>
    <?php include '../includes/htmlHead.php'; ?>
    <title>Mein Profil - Prüfungsübersicht</title>
    <style>
        .contentBody {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .exams-list {
            float: left;
            width: 30%;
            padding: 15px;
            background-color: #f5f5f5;
            border-radius: 5px;
            margin-right: 2%;
        }
        .exam-details {
            float: left;
            width: 65%;
            padding: 15px;
            background-color: #f9f9f9;
            border-radius: 5px;
        }
        .exam-item {
            padding: 10px;
            margin-bottom: 10px;
            background-color: white;
            border-radius: 3px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .exam-item:hover {
            background-color: #e9e9e9;
        }
        .exam-item.active {
            background-color: #d9edf7;
            border-left: 3px solid #31708f;
        }
        .grade {
            float: right;
            font-weight: bold;
        }
        .grade-1, .grade-2 { color: #5cb85c; }
        .grade-3, .grade-4 { color: #f0ad4e; }
        .grade-5, .grade-6 { color: #d9534f; }
        .question-item {
            padding: 15px;
            margin-bottom: 15px;
            background-color: white;
            border-radius: 3px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }
        .section {
            margin-bottom: 20px;
            background-color: white;
            padding: 15px;
            border-radius: 5px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .no-exams {
            padding: 20px;
            text-align: center;
            background-color: #f9f9f9;
            border-radius: 5px;
        }
        .date-info {
            font-size: 0.8em;
            color: #777;
        }
    </style>
</head>

<?php include '../includes/header.php'; ?>

<?php
require_once '../includes/db_controller.php';

if (!isset($_SESSION['user'])) {
    echo '<script>window.location.href = "../index.php";</script>';
    exit;
}

$db_controller = new DatabaseController();
$userId = $_SESSION['user']['user_id'];

// Get user's exams
$userExams = $db_controller->getExamsByUser($userId);

// Get exam details if an exam is selected
$selectedExam = null;
$examQuestions = [];
$module = null;

if (isset($_GET['exam_id']) && !empty($_GET['exam_id'])) {
    $examId = $_GET['exam_id'];
    
    // Check if the exam belongs to the user
    $exam = $db_controller->checkUserExamAccess($userId, $examId);
    if (!$exam) {
        echo '<script>alert("Zugriff auf diese Prüfung nicht erlaubt."); window.location.href="../index.php";</script>';
        exit;
    }
    
    $selectedExam = $db_controller->getMockExam($examId);
    if ($selectedExam) {
        $module = $db_controller->getModuleById($selectedExam['module_id']);
        $examQuestions = $db_controller->getMockQuestionsByExam($examId);
        $averageGrade = $db_controller->getAverageGradeByExam($examId);
    }
}
?>

<body>
    <div class="contentBody">
        <h1>Mein Profil - Prüfungsübersicht</h1>
        
        <div class="clearfix">
            <div class="exams-list">
                <div class="section">
                    <h2>Meine Prüfungen</h2>
                    
                    <?php if (empty($userExams)): ?>
                        <div class="no-exams">
                            <p>Sie haben noch keine Prüfungen absolviert.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($userExams as $exam): ?>
                            <div class="exam-item <?php echo (isset($_GET['exam_id']) && $_GET['exam_id'] == $exam['exam_id']) ? 'active' : ''; ?>" 
                                 onclick="window.location.href='profile.php?exam_id=<?php echo $exam['exam_id']; ?>'">
                                <span class="module-name"><?php echo htmlspecialchars($exam['module_label'] ?? $exam['module_name'] ?? 'Unbekanntes Modul'); ?></span>
                                <span class="grade grade-<?php echo (int)$exam['grade']; ?>">
                                    <?php echo number_format($exam['grade'], 1); ?>
                                </span>
                                <br>
                                <span class="date-info">
                                    <?php echo date('d.m.Y H:i', strtotime($exam['created_at'])); ?>
                                </span>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="exam-details">
                <?php if ($selectedExam && $module): ?>
                    <div class="section">
                        <h2>Modul: <?= htmlspecialchars($module['module_name']) ?></h2>
                        <h3>Note: <?= htmlspecialchars($averageGrade) ?></h3>
                    </div>
                    
                    <?php if (empty($examQuestions)): ?>
                        <div class="section">
                            <p>Keine Fragen für diese Prüfung gefunden.</p>
                        </div>
                    <?php else: ?>
                        <div class="section">
                            <h2>Prüfungsfragen</h2>
                        </div>
                        
                        <?php foreach ($examQuestions as $index => $question): ?>
                            <div class="section question">
                                <h3><strong>Frage: </strong><?= htmlspecialchars($question['question']) ?></h3>
                                <label>Antwort:</label><br>
                                <textarea readonly rows="4" cols="60"><?= htmlspecialchars($question['answer'] ?? 'Keine Antwort vorhanden.') ?></textarea>
                                <br><br>
                                <?php if (!empty($question['judgement'])): ?>
                                    <label>Bewertung:</label><br>
                                    <textarea readonly rows="4" cols="60"><?= htmlspecialchars($question['judgement']) ?></textarea>
                                    <br><br>
                                    <label>Note:</label><br>
                                    <textarea readonly rows="1" cols="10"><?= htmlspecialchars($question['grade']) ?></textarea>
                                <?php else: ?>
                                    <p><em>Diese Antwort wurde noch nicht bewertet.</em></p>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="section">
                        <h2>Prüfungsdetails</h2>
                        <p>Bitte wählen Sie eine Prüfung aus der Liste, um die Details anzuzeigen.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>

<?php include '../includes/footer.php'; ?>

</html>