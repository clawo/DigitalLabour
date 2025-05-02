<!DOCTYPE html>
<html lang="de">

<head>
    <?php include '../includes/htmlHead.php'; ?>
    <title>Mein Profil - Prüfungsübersicht</title>
    <style>
        .profile-container {
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
        .grade-1 { color: #5cb85c; }
        .grade-2 { color: #5cb85c; }
        .grade-3 { color: #f0ad4e; }
        .grade-4 { color: #f0ad4e; }
        .grade-5 { color: #d9534f; }
        .grade-6 { color: #d9534f; }
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
        h3 {
            margin-top: 0;
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

<body>
    <?php
    // Start session if not already started
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    // Include database controller
    require_once('db_connect.php');
    $controller = new DatabaseController();

    // Check if user is logged in
    $userId = $_SESSION['user']['user_id'] ?? null;
    if (!$userId) {
        header('Location: login.php');
        exit;
    }

    // Get user's exams
    $userExams = $controller->getExamsByUser($userId);

    // Get exam details if an exam is selected
    $selectedExam = null;
    $examQuestions = [];
    if (isset($_GET['exam_id']) && !empty($_GET['exam_id'])) {
        $examId = $_GET['exam_id'];
        $selectedExam = $controller->getMockExam($examId);
        
        // Make sure the exam belongs to the current user
        if ($selectedExam && $selectedExam['user_id'] == $userId) {
            $examQuestions = $controller->getMockQuestionsByExam($examId);
        }
    }
    ?>
    
    <div class="profile-container">
        <h2>Mein Profil - Prüfungsübersicht</h2>
        
        <div class="clearfix">
            <div class="exams-list">
                <h3>Meine Prüfungen</h3>
                
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
            
            <div class="exam-details">
                <?php if ($selectedExam): ?>
                    <h3>
                        Prüfungsdetails: 
                        <?php 
                        $moduleName = $selectedExam['module_name'] ?? '';
                        if (empty($moduleName) && !empty($selectedExam['module_id'])) {
                            $module = $controller->getModuleById($selectedExam['module_id']);
                            $moduleName = $module['module_name'] ?? $module['module_label'] ?? 'Unbekanntes Modul';
                        }
                        echo htmlspecialchars($moduleName);
                        ?>
                        <span class="grade grade-<?php echo (int)$selectedExam['grade']; ?>">
                            <?php echo number_format($selectedExam['grade'], 1); ?>
                        </span>
                    </h3>
                    
                    <?php if (empty($examQuestions)): ?>
                        <p>Keine Fragen für diese Prüfung gefunden.</p>
                    <?php else: ?>
                        <?php foreach ($examQuestions as $index => $question): ?>
                            <div class="question-item">
                                <h4>Frage <?php echo $index + 1; ?>:</h4>
                                <p><?php echo htmlspecialchars($question['question']); ?></p>
                                
                                <h5>Ihre Antwort:</h5>
                                <p><?php echo nl2br(htmlspecialchars($question['answer'])); ?></p>
                                
                                <?php if (!empty($question['judgement'])): ?>
                                    <h5>Feedback:</h5>
                                    <p><?php echo nl2br(htmlspecialchars($question['judgement'])); ?></p>
                                    
                                    <h5>Note:</h5>
                                    <p class="grade grade-<?php echo (int)$question['grade']; ?>">
                                        <?php echo number_format($question['grade'], 1); ?>
                                    </p>
                                <?php else: ?>
                                    <p><em>Diese Antwort wurde noch nicht bewertet.</em></p>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="no-exams">
                        <p>Bitte wählen Sie eine Prüfung aus der Liste, um die Details anzuzeigen.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>

<?php include '../includes/footer.php'; ?>

</html>