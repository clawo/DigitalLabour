<!DOCTYPE html>
<html lang="de">

<head>
    <?php include '../includes/htmlHead.php'; ?>
    <title>Grading with ChatGPT</title>
</head>

<?php include '../includes/header.php'; ?>

<body>
<div class="containerbody">
<div>
      <h1>GRUNDLAGEN DER INFORMATIK</h1>
      <p>Prof. Dr. Kamyar Sarshar</p>
    </div>
    <div class="search-bar">
      <input type="text" placeholder="Fragen durchsuchen">
      <span>&#128269;</span>
    </div>
  

  <div class="container">
    <!-- Aufgaben-Liste -->
    <div class="panel">
      <h2>WÄHLEN SIE EINE AUFGABE AUS</h2>
      <div class="task">
        <h3>TITEL DER FRAGE</h3>
        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
        <span class="edit-icon">&#9998;</span>
      </div>
      <div class="task">
        <h3>TITEL DER FRAGE</h3>
        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
        <span class="edit-icon">&#9998;</span>
      </div>
      <!-- Weitere Aufgaben hier -->
    </div>

    <!-- Editor -->
    <div class="panel editor">
      <h2>EDITOR</h2>
      <label for="titel">TITEL BEARBEITEN</label>
      <input type="text" id="titel" placeholder="Titel der Frage...">
      <label for="frage">FRAGE BEARBEITEN</label>
      <textarea id="frage" rows="10" placeholder="Fragetext..."></textarea>
      <div class="editor-buttons">
        <button class="delete">AUFGABE LÖSCHEN</button>
        <button class="save">SPEICHERN</button>
      </div>
    </div>
  </div>
  </div>
  </body>

<?php include '../includes/footer.php'; ?>

</html>