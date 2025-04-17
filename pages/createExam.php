<!DOCTYPE html>
<html lang="de">

<head>
    <?php include '../includes/htmlHead.php'; ?>
    <title>Mock Exam</title>
    
</head>
<?php include '../includes/header.php'; ?>


<body>
<div class="body-wrapper">
        <div class="container">
            <!-- Linke Seite -->
            <div class="left">
                <div class="tag">MODUL: 1</div>
                <div class="tag">TITEL: MOCK EXAM</div>

                <div class="section-title">1. LERNZIELE / KOMPETENZEN</div>
                <div class="box"></div>

                <div class="section-title">2. PRÜFUNGSFORM:</div>
                <div class="checkbox-group">
                    <label><input type="checkbox" checked> SCHRIFTLICH</label>
                    <label><input type="checkbox"> MÜNDLICH</label>
                    <label><input type="checkbox"> DIGITAL</label>
                    <label>
                        <input type="checkbox"> ANDERE:
                        <input type="text" />
                    </label>
                </div>

                <div class="section-title">FORMAT:</div>
                <div class="format-group">
                    <label><input type="checkbox" checked> OFFENE FRAGEN</label>
                    <label><input type="checkbox"> MULTIPLE CHOICE</label>
                    <label><input type="checkbox"> LÜCKENTEXT</label>
                    <label><input type="checkbox"> FALLANALYSE</label>
                    <label>
                        <input type="checkbox"> ANDERE:
                        <input type="text" />
                    </label>
                </div>
            </div>

            <!-- Rechte Seite -->
            <div class="right">
                <div class="section-title">3. ANZAHL DER FRAGEN:</div>
                <input type="text" />

                <div class="section-title">4. AUFGABENÜBERSICHT</div>
                <table>
                    <tr>
                        <th>AUFGABE:</th>
                        <th>THEMA:</th>
                        <th>KERNKOMPETENZEN:</th>
                        <th>PUNKTE:</th>
                    </tr>
                    <tr>
                        <td>A1</td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>A2</td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>B1</td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>B2</td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>C1</td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>C2</td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                </table>

                <div class="section-title">5. ERWARTUNGSHORIZONT / BEWERTUNGSKRITERIEN:</div>
                <div class="criteria">
                    <label>AUFGABE A1: <input type="text" /></label>
                    <label>AUFGABE A2: <input type="text" /></label>
                    <label>AUFGABE B1: <input type="text" /></label>
                    <label>AUFGABE B2: <input type="text" /></label>
                    <label>AUFGABE C1: <input type="text" /></label>
                    <label>AUFGABE C2: <input type="text" /></label>
                </div>

                <button class="button">Erstellen</button>
            </div>
        </div>
    </div>
</body>

<?php include '../includes/footer.php'; ?>

</html>