<?php
require_once("includes/header.php");
?>

<main>
    <h2>Connexion</h2>
    <form method="POST" action="">
        <label for="email">Email :</label><br>
        <input type="email" name="email" value="<?= htmlspecialchars($email ?? '') ?>" required><br><br>

        <label for="password">Mot de passe :</label><br>
        <input type="password" name="password" value="<?= htmlspecialchars($password ?? '') ?>" required><br><br>

        <input type="submit" name="login" value="Se connecter" class="btn">
    </form>

    <?php
    if (isset($_POST['login'])) {
        $email = $_POST['email'];
        $password = $_POST['password'];

    }


        // Version sans base de données (test)
        if ($email === "matteo@test.com" && $password === "1234") {
            echo "<p class='success'>Connexion réussie ! Bienvenue, Matteo 🎉</p>";
        } else {
            echo "<p class='error'>Identifiants incorrects.</p>";
        }
    ?>
</main>

<?php
require_once("includes/footer.php");
?>
