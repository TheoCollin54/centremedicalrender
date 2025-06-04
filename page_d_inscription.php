<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page d'inscription</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Inscription</h1>
        <form action="register.php" method="POST"> <!--Formulaire d'inscription-->
            <label for="username">Nom d'utilisateur :</label>
            <input type="text" id="username" name="username" required>

            <label for="email">Email :</label>
            <input type="email" id="email" name="email" required>

            <label for="password">Mot de passe :</label>
            <input type="password" id="password" name="password" required>

            <button class="btn" type="submit">S'inscrire</button>
        </form>
        <p>Vous avez déjà un compte ? Connectez-vous !</p>
        <button class="btn" onclick="window.location.href='index.php'">Se connecter</button>
    </div>
</body>
</html>