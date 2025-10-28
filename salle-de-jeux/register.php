<?php
require_once("includes/header.php");
?>

<main>
    <h2>Créer un compte</h2>
    <form method="POST" action="">
        <label>Nom :</label><br>
        <input type="text" name="nom" required><br><br>

        <label>Email :</label><br>
        <input type="email" name="email" required><br><br>

        <label>Mot de passe :</label><br>
        <input type="password" name="password" required><br><br>

        <input type="submit" name="register" value="S'inscrire" class="btn">
    </form>

    <?php
    if (isset($_POST['register'])) {
        $nom = $_POST['nom'];
        $email = $_POST['email'];
        $password = $_POST['password'];

        echo "<p class='success'>Bienvenue $nom ! Ton compte a été créé (simulation).</p>";
    }
    ?>
</main>

<?php
require_once("includes/footer.php");
?>
