<?php include 'kid.php';?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <title>Ajouter une transaction - <?php echo ucfirst($kid); ?></title>
</head>

<body style="zoom: 200%;font-family: arial;">
    <h2>Ajouter une transaction</h2>
    <form id="addForm" method="post" action="/<?php echo ($kid);?>/modify">
        <div>
            <label for="montant">Montant (€):</label>
            <input type="number" id="montant" name="montant" step="0.01" required>
        </div>
        <div>
            <label for="credit">Crédit:</label>
            <input type="checkbox" id="credit" name="credit" value="1" checked>
        </div>
        <div>
            <label for="raison">Raison:</label><br>
            <textarea id="raison" name="raison" rows="4" cols="50"></textarea>
        </div>
        <div>
            <label for="mdp">MDP:</label><br>
            <input type="password" id="mdp" name="mdp" step="0.01" required>
        </div>
        <div>
            <button type="submit">Envoyer</button>
            <a href="/">Annuler</a>
        </div>
        <div id="status" role="status" aria-live="polite" style="margin-top:10px;color:#a00;"></div>
    </form>

    <script>
        (function () {
            const form = document.getElementById('addForm');
            const status = document.getElementById('status');
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                status.textContent = 'Envoi...';
                const fd = new FormData(form);
                fetch(form.getAttribute('action'), {
                    method: 'POST',
                    body: fd,
                    credentials: 'same-origin'
                })
                    .then(function (resp) {
                        const ct = resp.headers.get('content-type') || '';
                        if (!resp.ok) return resp.text().then(function (t) { throw new Error(t || resp.statusText); });
                        if (ct.indexOf('application/json') !== -1) return resp.json();
                        return resp.text().then(function (t) { try { return JSON.parse(t); } catch (e) { return { success: false, message: 'Invalid JSON response' }; } });
                    })
                    .then(function (data) {
                        if (data && data.success) {
                            // redirect to home page on success
                            window.location.href = '/';
                        } else {
                            status.textContent = data && data.message ? data.message : 'Erreur lors de l\'insertion';
                        }
                    })
                    .catch(function (err) {
                        console.error('Submit error', err);
                        status.textContent = 'Erreur réseau ou serveur';
                    });
            });
        })();
    </script>

</body>

</html>
