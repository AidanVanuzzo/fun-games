<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light dark">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@2/css/pico.min.css">

    <title>S'inscrire pour l'activité</title>
</head>

<body>
    <main class="container">
        <h1>Créer un nouveau rendez-vous</h1>

        <form action="Inscription.php" method="POST">
            <label for="date">Date du fun</label>
            <input type="date" id="date" name="date" required min="2025-09-30"/>

            <label for="time">Heure de début du fun</label>
            <select id="time" name="time" required>
                <option value="10h">10h</option>
                <option value="11h">11h</option>
                <option value="13h">13h</option>
                <option value="14h">14h</option>
                <option value="15h">15h</option>
                <option value="16h">16h</option>
                <option value="17h">17h</option>
                <option value="18h">18h</option>
                <option value="19h">19h</option>
            </select>

            <label for="number">Nombre de participants au fun</label>
            <input type="number" id="number" name="number" required min="4" max="8"/>

            <label for="name">Nom du groupe</label>
            <input type="text" id="name" name="name" minlength="2" maxlength="25"/>

            <button type="submit">Créer</button>
        </form>
    </main>
</body>

</html>