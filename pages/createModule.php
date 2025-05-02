<body>
<div class="containerbody">
    <h1><?= $createMode ? 'Neues Modul anlegen' : htmlspecialchars($module['module_label']) ?></h1>

    <?php if (!$createMode): ?>
        <p>Angelegt von: <?= htmlspecialchars($moduleCreator['first_name'].' '.$moduleCreator['last_name']) ?></p>
    <?php endif; ?>

    <div class="container">

        <?php if ($createMode): ?>
            <?php if (!empty($error)) echo '<p class="error">'.$error.'</p>'; ?>

            <div class="panel editor">
                <h2>MODULDATEN</h2>

                <form method="post">
                    <label for="module_name">Name des Moduls</label>
                    <input type="text" id="module_name" name="module_name"
                           value="<?= htmlspecialchars($_POST['module_name'] ?? '') ?>">

                    <label for="module_label">Label (Kurzbezeichnung)</label>
                    <input type="text" id="module_label" name="module_label"
                           value="<?= htmlspecialchars($_POST['module_label'] ?? '') ?>">

                    <div class="editor-buttons">
                        <button type="submit" name="create_module" class="save">Modul erstellen</button>
                    </div>
                </form>
            </div>

        <?php else: ?>
            <!-- Aufgaben‑Liste -->
            <div class="panel" id="task-panel">
                <h2>WÄHLEN SIE EINE AUFGABE AUS</h2>

                <?php foreach ($questions as $question): ?>
                    <div class="task">
                        <h3>Frage #<?= htmlspecialchars($question['question_id']) ?></h3>
                        <p><?= nl2br(htmlspecialchars($question['question'])) ?></p>
                        <span class="edit-icon"
                              onclick='selectQuestion(<?= (int)$question["question_id"] ?>,
                                                      <?= json_encode($question["question"],
                                                                       JSON_HEX_APOS|JSON_HEX_QUOT) ?>)'>✏️</span>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Editor -->
            <div class="panel editor">
                <h2>EDITOR</h2>
                <label for="titel">TITEL BEARBEITEN</label>
                <input type="text" id="titel" placeholder="Titel der Frage..." readonly>
                <label for="frage">FRAGE BEARBEITEN</label>
                <textarea id="frage" rows="10" placeholder="Fragetext..."></textarea>
                <div class="editor-buttons">
                    <button class="delete">AUFGABE LÖSCHEN</button>
                    <button id="save-btn" class="save" style="display:none;">SPEICHERN</button>
                    <button id="create-btn" class="save">FRAGE ERSTELLEN</button>
                </div>
            </div>
        <?php endif; ?>

    </div><!-- /.container -->
</div><!-- /.containerbody -->

<script>
    let selectedQuestionId = null;

    function selectQuestion(id, frageText) {
        selectedQuestionId = id;
        document.getElementById('titel').value = "Frage #" + id;
        document.getElementById('frage').value = frageText;

        document.getElementById('save-btn').style.display = 'inline-block';
        document.getElementById('create-btn').style.display = 'none';
    }

    document.getElementById('create-btn').addEventListener('click', function () {
        const frage = document.getElementById('frage').value;

        if (!frage.trim()) {
            alert('Bitte gib einen Fragetext ein.');
            return;
        }

        fetch(window.location.href, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({
                action: 'create',
                module_id: <?= (int)$moduleId ?>,
                frage: frage
            })
        })
            .then(function () {
                window.location.reload();
            });
    });

    document.getElementById('save-btn').addEventListener('click', function () {
        if (!selectedQuestionId) {
            alert('Bitte wähle zuerst eine Aufgabe aus.');
            return;
        }
        const frage = document.getElementById('frage').value;

        fetch(window.location.href, {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: new URLSearchParams({
                action: 'save',
                question_id: selectedQuestionId,
                frage: frage
            })
        }).then(function () {
            window.location.reload();
        });
    });

    document.querySelector('.delete').addEventListener('click', function () {
        if (!selectedQuestionId) {
            alert('Bitte wähle zuerst eine Aufgabe aus.');
            return;
        }
        if (!confirm('Willst du diese Aufgabe wirklich löschen?')) {
            return;
        }

        fetch(window.location.href, {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: new URLSearchParams({
                action: 'delete',
                question_id: selectedQuestionId
            })
        }).then(function () {
            window.location.reload();
        });
    });

    document.querySelector('.search-bar input').addEventListener('input', function () {
        const searchTerm = this.value.toLowerCase();
        const tasks = document.querySelectorAll('.task');

        tasks.forEach(task => {
            const text = task.textContent.toLowerCase();
            if (text.includes(searchTerm)) {
                task.style.display = 'block';
            } else {
                task.style.display = 'none';
            }
        });
    });
</script>

</body>

<?php include '../includes/footer.php'; ?>

</html>