
<?php
    require_once('db_connect.php');

    class DatabaseController {
        private $pdo;

        public function __construct() {
            echo '<script>console.log("Initializing DatabaseController...");</script>';
            $this->pdo = getDB();
        }
        
        // ===== AUTHENTICATION =====
        public function authorize($userId, $allowedRoles) {
            if (!is_array($allowedRoles)) {
                $allowedRoles = [$allowedRoles];
            }
        
            $stmt = $this->pdo->prepare("
                SELECT r.role_name
                FROM users u
                JOIN roles r ON u.role_id = r.role_id
                WHERE u.user_id = ?
            ");
            $stmt->execute([$userId]);
            $userRole = $stmt->fetchColumn();
        
            return in_array($userRole, $allowedRoles);
        }        

        public function registerUser($data) {
            // check if username or email already exists
            $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$data['username'], $data['email']]);
            if ($stmt->fetchColumn() > 0) {
                return ['success' => false, 'message' => 'Username oder Email existiert bereits.'];
            }
        
            // hash password and insert new user
            $stmt = $this->pdo->prepare("
                INSERT INTO users (username, email, first_name, last_name, role_id, password)
                VALUES (?, ?, ?, ?, ?, ?)
            ");

            $stmt->execute([
                $data['username'], $data['email'], $data['first_name'], $data['last_name'],
                $data['role_id'], password_hash($data['password'], PASSWORD_DEFAULT)
            ]);
        
            return ['success' => true, 'user_id' => $this->pdo->lastInsertId()];
        }        

        public function loginUser($usernameOrEmail, $password) {
            $stmt = $this->pdo->prepare("
                SELECT * FROM users
                WHERE username = ? OR email = ?
            ");
            $stmt->execute([$usernameOrEmail, $usernameOrEmail]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
            if ($user && password_verify($password, $user['password'])) {
                unset($user['password']); // security: remove password from user data
                return ['success' => true, 'user' => $user];
            }
        
            return ['success' => false, 'message' => 'Login fehlgeschlagen.'];
        }        

        public function changePassword($userId, $newPassword) {
            $stmt = $this->pdo->prepare("
                UPDATE users SET password = ?
                WHERE user_id = ?
            ");
            return $stmt->execute([
                password_hash($newPassword, PASSWORD_DEFAULT),
                $userId
            ]);
        }        


        // ===== USERS =====
        public function getAllUsers() {
            $stmt = $this->pdo->query("SELECT user_id, username, email, first_name, last_name, role_id FROM users");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        public function getUsersByRole($roleId) {
            $stmt = $this->pdo->prepare("
                SELECT u.user_id, u.username, u.email, u.first_name, u.last_name, r.role_name, r.role_label
                FROM users u
                JOIN roles r ON u.role_id = r.role_id
                WHERE u.role_id = ?
            ");
            $stmt->execute([$roleId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        
        public function getUserById($userId) {
            $stmt = $this->pdo->prepare("SELECT * FROM users WHERE user_id = ?");
            $stmt->execute([$userId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }

        public function getUserByUsername($username) {
            $stmt = $this->pdo->prepare("SELECT * FROM users WHERE username = ?");
            $stmt->execute([$username]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }        

        public function createUser($data) {
            $stmt = $this->pdo->prepare("
                INSERT INTO users (username, email, first_name, last_name, role_id, password)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $data['username'], $data['email'], $data['first_name'],
                $data['last_name'], $data['role_id'], password_hash($data['password'], PASSWORD_DEFAULT)
            ]);
            return $this->pdo->lastInsertId();
        }

        public function updateUser($userId, $data) {
            $stmt = $this->pdo->prepare("
                UPDATE users SET username = ?, email = ?, first_name = ?, last_name = ?, role_id = ?
                WHERE user_id = ?
            ");
            return $stmt->execute([
                $data['username'], $data['email'], $data['first_name'],
                $data['last_name'], $data['role_id'], $userId
            ]);
        }

        public function deleteUser($userId) {
            $stmt = $this->pdo->prepare("DELETE FROM users WHERE user_id = ?");
            return $stmt->execute([$userId]);
        }


        // ===== ROLES =====
        public function getAllRoles() {
            return $this->pdo->query("SELECT * FROM roles")->fetchAll(PDO::FETCH_ASSOC);
        }

        public function getRoleById($roleId) {
            $stmt = $this->pdo->prepare("SELECT * FROM roles WHERE role_id = ?");
            $stmt->execute([$roleId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }

        public function getRoleByName($roleName) {
            $stmt = $this->pdo->prepare("SELECT * FROM roles WHERE role_name = ?");
            $stmt->execute([$roleName]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }        


        // ===== MODULES =====
        public function createModule($module_name, $module_label, $userId) {
            $stmt = $this->pdo->prepare("
                INSERT INTO modules (module_name, module_label, created_by)
                VALUES (?, ?, ?)
            ");
            $stmt->execute([$module_name, $module_label, $userId]);
            return $this->pdo->lastInsertId();
        }

        public function checkModuleCreatedByUser($moduleId, $userId) {
            $stmt = $this->pdo->prepare("
                SELECT COUNT(*) FROM modules
                WHERE module_id = ? AND created_by = ?
            ");
            $stmt->execute([$moduleId, $userId]);
            return $stmt->fetchColumn() > 0;
        }

        public function getAllModules(): array {
            return $this->pdo->query("SELECT * FROM modules")->fetchAll(PDO::FETCH_ASSOC);
        }

        public function getModulesFiltered($sortierung = 'name', $suche = '') {
            $allowedSortFields = [
                'name' => 'module_name'
            ];

            // fallback to default sort field
            $sortField = $allowedSortFields[$sortierung] ?? 'module_name';

            $query = "SELECT * FROM modules";

            if (!empty($suche)) {
                $query .= " WHERE module_name LIKE :suche OR module_label LIKE :suche";
            }

            $query .= " ORDER BY $sortField ASC";

            $stmt = $this->pdo->prepare($query);

            if (!empty($suche)) {
                $sucheParam = '%' . $suche . '%';
                $stmt->bindParam(':suche', $sucheParam, PDO::PARAM_STR);
            }

            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        public function getModuleById($moduleId): array {
            $stmt = $this->pdo->prepare("SELECT * FROM modules WHERE module_id = ?");
            $stmt->execute([$moduleId]);
            $module = $stmt->fetch(PDO::FETCH_ASSOC);

            return $module;
        }

        public function getModuleByName($moduleName) {
            $stmt = $this->pdo->prepare("SELECT * FROM modules WHERE module_name = ?");
            $stmt->execute([$moduleName]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }        


        // ===== QUESTIONS =====
        public function updateQuestion($questionId, $newQuestionText) {
            $stmt = $this->pdo->prepare("UPDATE questions SET question = ? WHERE question_id = ?");
            return $stmt->execute([$newQuestionText, $questionId]);
        }

        public function deleteQuestion($questionId) {
            $stmt = $this->pdo->prepare("DELETE FROM questions WHERE question_id = ?");
            return $stmt->execute([$questionId]);
        }

        public function getQuestionsByModule($moduleId) {
            $stmt = $this->pdo->prepare("SELECT * FROM questions WHERE module_id = ?");
            $stmt->execute([$moduleId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        public function getRandomQuestionsByModule($moduleId, $count) {
            $count = (int)$count;
            $stmt = $this->pdo->prepare("
                SELECT * FROM questions
                WHERE module_id = ?
                ORDER BY RAND()
                LIMIT $count
            ");
            $stmt->execute([$moduleId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }


        public function getQuestionCountByModule($moduleId) {
            $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM questions WHERE module_id = ?");
            $stmt->execute([$moduleId]);
            return $stmt->fetchColumn();
        }

        public function getQuestion($questionId) {
            $stmt = $this->pdo->prepare("SELECT * FROM questions WHERE question_id = ?");
            $stmt->execute([$questionId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }


        // ===== MOCK EXAMS =====
        public function createMockExam($moduleId, $userId, $questions) {
            $stmt = $this->pdo->prepare("
                INSERT INTO mock_exams (module_id, user_id)
                VALUES (?, ?)
            ");
            $stmt->execute([$moduleId, $userId]);

            $examId = $this->pdo->lastInsertId();

            foreach ($questions as $question) {
                $this->insertMockQuestion($examId, $question['question_id'], null, null, null);
            }

            return $examId;
        }

        public function getMockExam($examId) {
            $stmt = $this->pdo->prepare("SELECT * FROM mock_exams WHERE exam_id = ?");
            $stmt->execute([$examId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }

        public function checkUserExamAccess($userId, $examId) {
            $stmt = $this->pdo->prepare("
                SELECT COUNT(*) FROM mock_exams
                WHERE user_id = ? AND exam_id = ?
            ");
            $stmt->execute([$userId, $examId]);

            if ($stmt->fetchColumn() > 0) {
                return $this->getMockExam($examId);
            } else {
                return false;
            }
        }

        public function getExamsByUser($userId) {
            $stmt = $this->pdo->prepare("
                SELECT me.*, m.module_name, m.module_label
                FROM mock_exams me
                JOIN modules m ON me.module_id = m.module_id
                WHERE me.user_id = ?
            ");
            $stmt->execute([$userId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        public function getLatestExamByUser($userId) {
            $stmt = $this->pdo->prepare("
                SELECT * FROM mock_exams
                WHERE user_id = ?
                ORDER BY created_at DESC
                LIMIT 1
            ");
            $stmt->execute([$userId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }        

        public function deleteMockExam($examId) {
            $stmt = $this->pdo->prepare("DELETE FROM mock_exams WHERE exam_id = ?");
            return $stmt->execute([$examId]);
        }        

        // ===== MOCK QUESTIONS =====
        public function insertMockQuestion($examId, $questionId, $answer, $judgement, $grade) {
            $stmt = $this->pdo->prepare("
                INSERT INTO mock_questions (exam_id, question_id, answer, judgement, grade)
                VALUES (?, ?, ?, ?, ?)
            ");
            return $stmt->execute([$examId, $questionId, $answer, $judgement, $grade]);
        }

        public function getMockQuestionsByExam($examId) {
            $stmt = $this->pdo->prepare("
                SELECT mq.*, q.question
                FROM mock_questions mq
                JOIN questions q ON mq.question_id = q.question_id
                WHERE mq.exam_id = ?
            ");
            $stmt->execute([$examId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        public function getMockQuestion($examId, $questionId) {
            $stmt = $this->pdo->prepare("
                SELECT * FROM mock_questions
                WHERE exam_id = ? AND question_id = ?
            ");
            $stmt->execute([$examId, $questionId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }        

        public function checkIfAnswered($examId, $questionId) {
            $stmt = $this->pdo->prepare("
                SELECT answer FROM mock_questions
                WHERE exam_id = ? AND question_id = ?
            ");
            $stmt->execute([$examId, $questionId]);
            return !empty($stmt->fetchColumn());
        }        

        public function getUnreviewedAnswers($examId) {
            $stmt = $this->pdo->prepare("
                SELECT * FROM mock_questions
                WHERE exam_id = ? AND judgement IS NULL
            ");
            $stmt->execute([$examId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        
        public function hasUnreviewedAnswers($examId) {
            $stmt = $this->pdo->prepare("
                SELECT COUNT(*) FROM mock_questions
                WHERE exam_id = ? AND judgement IS NULL
            ");
            $stmt->execute([$examId]);
            return $stmt->fetchColumn() > 0;
        }        

        public function updateMockAnswer($examId, $questionId, $answer) {
            $stmt = $this->pdo->prepare("
                UPDATE mock_questions
                SET answer = ?
                WHERE exam_id = ? AND question_id = ?
            ");
            return $stmt->execute([$answer, $examId, $questionId]);
        }

        public function updateMockEvaluation($examId, $questionId, $judgement, $grade) {
            $stmt = $this->pdo->prepare("
                UPDATE mock_questions
                SET judgement = ?, grade = ?
                WHERE exam_id = ? AND question_id = ?
            ");
            return $stmt->execute([$judgement, $grade, $examId, $questionId]);
        }

        public function deleteMockQuestionsByExam($examId) {
            $stmt = $this->pdo->prepare("DELETE FROM mock_questions WHERE exam_id = ?");
            return $stmt->execute([$examId]);
        }        


        // ===== STATISTICS =====
        public function getAverageGradePerUser() {
            return $this->pdo->query("
                SELECT u.user_id, u.username, AVG(me.grade) as avg_grade
                FROM users u
                JOIN mock_exams me ON u.user_id = me.user_id
                GROUP BY u.user_id
            ")->fetchAll(PDO::FETCH_ASSOC);
        }

        public function getAverageGradePerModule() {
            return $this->pdo->query("
                SELECT m.module_id, m.module_name, AVG(me.grade) as avg_grade
                FROM modules m
                JOIN mock_exams me ON m.module_id = me.module_id
                GROUP BY m.module_id
            ")->fetchAll(PDO::FETCH_ASSOC);
        }

        public function getAverageGradeByExam($examId) {
            $stmt = $this->pdo->prepare("
                SELECT AVG(grade) as avg_grade
                FROM mock_questions
                WHERE exam_id = ?
            ");
            $stmt->execute([$examId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            $avgGrade = round($result['avg_grade'], 1);

            $possibleGrades = [1.0, 1.3, 1.7, 2.0, 2.3, 2.7, 3.0, 3.3, 3.7, 4.0, 4.3, 4.7, 5.0];
            $closestGrade = null;
            $closestDiff = PHP_INT_MAX;
            foreach ($possibleGrades as $grade) {
                $diff = abs($avgGrade - $grade);
                if ($diff < $closestDiff) {
                    $closestDiff = $diff;
                    $closestGrade = $grade;
                }
            }

            return $closestGrade;
        }

        public function countAnsweredQuestions($examId) {
            $stmt = $this->pdo->prepare("
                SELECT COUNT(*) FROM mock_questions
                WHERE exam_id = ? AND answer IS NOT NULL
            ");
            $stmt->execute([$examId]);
            return $stmt->fetchColumn();
        }
    }
?>
