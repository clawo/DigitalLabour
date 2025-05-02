<!DOCTYPE html>
<html lang="de">

<head>
    <?php include '../includes/htmlHead.php'; ?>
    <title>Prüfungsfragen erstellen</title>
    <style>
        .contentBody {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .section {
            margin-bottom: 20px;
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        h1 {
            font-family: 'Bebas Neue', sans-serif;
            color: #1e2a38;
        }
        
        select, textarea, input {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-family: inherit;
            box-sizing: border-box;
        }
        
        select {
            height: 40px;
        }
        
        textarea {
            min-height: 150px;
            resize: vertical;
        }
        
        .btn {
            background-color: #1e2a38;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            font-family: inherit;
        }
        
        .btn:hover {
            background-color: #2e3e50;
        }
        
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .preview-container {
            margin-top: 20px;
            padding: 15px;
            background-color: #f9f9f9;
            border: 1px solid #e9e9e9;
            border-radius: 4px;
        }
        
        .preview-title {
            font-weight: bold;
            margin-bottom: 10px;
        }

        .question-item {
            padding: 15px;
            margin-bottom: 15px;
            background-color: white;
            border-radius: 3px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
    </style>
</head>

<?php include '../includes/header.php'; ?>

<?php
require_once '../includes/db_controller.php';
require_once '../includes/db_connect.php';

// Check if user is logged in and is a dozent (role_id = 1)
if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 1) {
    echo '<script>alert("Sie müssen als Dozent angemeldet sein, um auf diese Seite zuzugreifen."); window.location.href="../index.php";</script>';
    exit;
}

$db_controller = new DatabaseController();
$message = "";
$messageType = "";

// Get all modules for the dropdown
$modules = $db_controller->getAllModules();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $moduleId = $_POST['module_id'] ?? '';
    $questionText = $_POST['question_text'] ?? '';
    $answerExample = $_POST['answer_example'] ?? '';
    $points = $_POST['points'] ?? 0;
    
    // Basic validation
    if (empty($moduleId) || empty($questionText)) {
        $message = "Bitte füllen Sie alle Pflichtfelder aus.";
        $messageType = "danger";
    } else {
        try {
            // Get PDO connection directly
            $pdo = getDB();
            
            // First, let's check the structure of the questions table
            $tableInfoStmt = $pdo->query("DESCRIBE questions");
            $tableColumns = $tableInfoStmt->fetchAll(PDO::FETCH_COLUMN);
            
            // Create the SQL query based on the table structure
            $columns = ['module_id', 'question'];
            $values = [$moduleId, $questionText];
            
            // Add optional fields if they exist in the table
            if (in_array('answer_example', $tableColumns)) {
                $columns[] = 'answer_example';
                $values[] = $answerExample;
            }
            
            if (in_array('points', $tableColumns)) {
                $columns[] = 'points';
                $values[] = $points;
            }
            
            // Add user ID if the column exists
            if (in_array('created_by', $tableColumns)) {
                $columns[] = 'created_by';
                $values[] = $_SESSION['user']['user_id'];
            }
            
            $columnsStr = implode(', ', $columns);
            $placeholders = implode(', ', array_fill(0, count($columns), '?'));
            
            // Insert the question
            $stmt = $pdo->prepare("
                INSERT INTO questions ($columnsStr)
                VALUES ($placeholders)
            ");
            
            $result = $stmt->execute($values);
            
            if ($result) {
                $message = "Die Frage wurde erfolgreich erstellt.";
                $messageType = "success";
                
                // Reset form
                $_POST = [];
            } else {
                $message = "Beim Erstellen der Frage ist ein Fehler aufgetreten.";
                $messageType = "danger";
            }
        } catch (PDOException $e) {
            $message = "Datenbankfehler: " . $e->getMessage();
            $messageType = "danger";
        }
    }
}

// Get recent questions - without relying on created_by column
try {
    $pdo = getDB();
    $stmt = $pdo->prepare("
        SELECT q.*, m.module_name, m.module_label 
        FROM questions q
        JOIN modules m ON q.module_id = m.module_id
        ORDER BY q.question_id DESC
        LIMIT 10
    ");
    $stmt->execute();
    $recentQuestions = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $recentQuestions = [];
    $message = "Fehler beim Abrufen der kürzlich erstellten Fragen: " . $e->getMessage();
    $messageType = "danger";
}
?>

<body>
    <div class="contentBody">
        <h1>Prüfungsfragen erstellen</h1>
        
        <?php if (!empty($message)): ?>
            <div class="alert alert-<?= $messageType ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>
        
        <div class="section">
            <form method="POST" action="">
                <div>
                    <label for="module_id"><strong>Modul auswählen:</strong></label>
                    <select id="module_id" name="module_id" required>
                        <option value="">-- Bitte wählen Sie ein Modul --</option>
                        <?php foreach ($modules as $module): ?>
                            <option value="<?= $module['module_id'] ?>" <?= (isset($_POST['module_id']) && $_POST['module_id'] == $module['module_id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($module['module_name']) ?> (<?= htmlspecialchars($module['module_label']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div>
                    <label for="question_text"><strong>Frage:</strong></label>
                    <textarea id="question_text" name="question_text" required placeholder="Geben Sie hier Ihre Prüfungsfrage ein..."><?= htmlspecialchars($_POST['question_text'] ?? '') ?></textarea>
                </div>
                
                <div>
                    <label for="answer_example"><strong>Beispielantwort (optional):</strong></label>
                    <textarea id="answer_example" name="answer_example" placeholder="Geben Sie hier eine Beispielantwort oder Bewertungskriterien ein..."><?= htmlspecialchars($_POST['answer_example'] ?? '') ?></textarea>
                </div>
                
                <div>
                    <label for="points"><strong>Punkte:</strong></label>
                    <input type="number" id="points" name="points" min="1" max="100" value="<?= htmlspecialchars($_POST['points'] ?? '10') ?>" required>
                </div>
                
                <div class="preview-container">
                    <div class="preview-title">Vorschau:</div>
                    <div id="question-preview">Frage wird hier angezeigt...</div>
                </div>
                
                <button type="submit" name="submit" class="btn">Frage erstellen</button>
            </form>
        </div>
        
        <div class="section">
            <h2>Kürzlich erstellte Fragen</h2>
            <?php if (empty($recentQuestions)): ?>
                <p>Es wurden noch keine Fragen erstellt.</p>
            <?php else: ?>
                <?php foreach ($recentQuestions as $index => $question): ?>
                    <div class="question-item">
                        <h4><strong>Modul:</strong> <?= htmlspecialchars($question['module_name'] ?? '') ?> (<?= htmlspecialchars($question['module_label'] ?? '') ?>)</h4>
                        <p><strong>Frage <?= $index + 1 ?>:</strong> <?= htmlspecialchars($question['question']) ?></p>
                        <?php if (!empty($question['answer_example'])): ?>
                            <p><strong>Beispielantwort:</strong> <?= htmlspecialchars($question['answer_example']) ?></p>
                        <?php endif; ?>
                        <?php if (isset($question['points'])): ?>
                            <p><strong>Punkte:</strong> <?= htmlspecialchars($question['points']) ?></p>
                        <?php endif; ?>
                        <?php if (isset($question['created_at'])): ?>
                            <p><strong>Erstellt am:</strong> <?= date('d.m.Y H:i', strtotime($question['created_at'])) ?></p>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    
    <script>
        // Live preview of the question as user types
        document.addEventListener('DOMContentLoaded', function() {
            const questionInput = document.getElementById('question_text');
            const questionPreview = document.getElementById('question-preview');
            
            function updatePreview() {
                const text = questionInput.value.trim();
                questionPreview.textContent = text ? text : 'Frage wird hier angezeigt...';
            }
            
            questionInput.addEventListener('input', updatePreview);
            updatePreview(); // Initial update
        });
    </script>
</body>

<?php include '../includes/footer.php'; ?>

</html>