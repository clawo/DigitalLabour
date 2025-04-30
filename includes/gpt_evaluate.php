<?php
function getApiKey() {
    $env = parse_ini_file('/var/www/vhosts/examwise.eu/.env');
    return $env['OPENAI_API_KEY'] ?? null;
}

// API-Aufruf an OpenAI
function evaluateAnswer($question, $answer) {
    $apiUrl = 'https://api.openai.com/v1/chat/completions';
    $apiKey = getApiKey();

    // check if API key is set
    if (empty($apiKey)) {
        return 'API-Schlüssel nicht gesetzt.';
    }

    // Erstellen des Prompts
    $prompt = "Bewerte die folgende Antwort auf die Frage nach dem deutschen Notensystem mit den Noten (1.0, 1.3, 1.7, 2.0, ..., 6.0). Gib das Ergebnis bitte in **diesem strukturierten Format** aus:

    **Note:** <Note zwischen 1.0 und 6.0>  
    **Begründung & Verbesserung:** <kurze Bewertung, warum die Note vergeben wurde und gib einen konkreten Vorschlag, wie die Antwort verbessert werden kann>  
    Hier ist die Frage und die Antwort:

    Frage: $question

    Antwort: $answer";

    // API-Daten
    $postData = [
        'model' => 'gpt-3.5-turbo',
        'messages' => [
            ['role' => 'system', 'content' => 'Du bist ein Bewertungssystem für Prüfungsantworten.'],
            ['role' => 'user', 'content' => $prompt]
        ],
        'max_tokens' => 300,
        'temperature' => 0.7,
    ];


    // HTTP-Stream-Context für den POST-Aufruf
    $contextOptions = [
        'http' => [
            'method' => 'POST',
            'header' => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $apiKey
            ],
            'content' => json_encode($postData),
        ],
    ];

    $context = stream_context_create($contextOptions);

    $response = file_get_contents($apiUrl, false, $context);

    if ($response === FALSE) {
        return 'Fehler bei der API-Anfrage.';
    }

    $responseData = json_decode($response, true);
    return $responseData['choices'][0]['message']['content'] ?? 'Keine Antwort erhalten.';
}

// Note und Begründung extrahieren
function extractGrade($evaluation) {
    preg_match('/\*\*Note:\*\*\s*([0-9]+\.[0-9])/', $evaluation, $matches);
    return $matches[1] ?? 'Keine Note gefunden';
}

function extractFeedback($evaluation) {
    preg_match('/\*\*Begründung & Verbesserung:\*\*\s*(.*)/', $evaluation, $matches);
    return $matches[1] ?? 'Keine Begründung gefunden';
}

/*
// Wenn das Formular abgeschickt wurde, die Daten auswerten
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['antworten'])) {
    $questionId = key($_POST['antworten']); // ID der Frage
    $answer = $_POST['antworten'][$questionId];

    // Beispielhafte Frage (muss natürlich dynamisch sein)
    $question = "Was ist der Unterschied zwischen einem Integer und einem Float in der Programmiersprache PHP?";

    // Auswertung durch ChatGPT
    $evaluation = evaluateAnswer($question, $answer);

    // Die extrahierte Note und Begründung
    $grade = extractGrade($evaluation);
    $feedback = extractFeedback($evaluation);
    
    // Weitergabe der Daten an das Frontend
    echo "Note: " . $grade . "<br>";
    echo "Begründung & Verbesserung: " . $feedback . "<br>";
}
*/

// implement test of above functions
$testQuestion = "Was ist der Unterschied zwischen einem Integer und einem Float in der Programmiersprache PHP?";
$testAnswer = "Ein Integer ist eine ganze Zahl, während ein Float eine Fließkommazahl ist.";
$evaluation = evaluateAnswer($testQuestion, $testAnswer);
$grade = extractGrade($evaluation);
$feedback = extractFeedback($evaluation);
echo "Test Evaluation: " . $evaluation . "<br>";
echo "Test Note: " . $grade . "<br>";
echo "Test Begründung: " . $feedback . "<br>";
?>
