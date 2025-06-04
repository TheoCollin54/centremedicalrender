<?php
    session_start();

    // Vérifie que l'utilisateur est connecté
    if (!isset($_SESSION['users_id'])) {
        header("Location: index.php"); // redirection vers la page de connexion si non connecté
        exit();
    }

    $user_id = $_SESSION['users_id'];

    $host = getenv('DB_HOST');
    $port = getenv('DB_PORT');
    $dbname = getenv('DB_NAME');
    $user = getenv('DB_USER');
    $password = getenv('DB_PASS');


    try {
        // Chaîne de connexion CORRECTE
        $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";
        $pdo = new PDO($dsn, $user, $password);

        // Bonnes pratiques
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->exec("SET NAMES 'UTF8'");
    } catch (PDOException $e) {
        die("Erreur de connexion : " . $e->getMessage());
    }

    $sql_user = "SELECT username FROM users WHERE users_id = :user_id";
    $stmt_user = $pdo->prepare($sql_user);
    $stmt_user->execute(['user_id' => $user_id]);
    $user = $stmt_user->fetch(PDO::FETCH_ASSOC);

    if ($user && $user['username'] === 'admin') {
        header("Location: dashboard_admin.php");
        exit();
    }

    $sql_doctor = "SELECT doctor FROM users WHERE users_id = :user_id";
    $stmt_doctor = $pdo->prepare($sql_doctor);
    $stmt_doctor->execute(['user_id' => $user_id]);
    $doctor = $stmt_doctor->fetch(PDO::FETCH_ASSOC);

    if ($doctor && $doctor['doctor'] === 1) {
        header("Location: dashboard_doctor.php");
        exit();
    }

    $stmt = $pdo->query("SELECT users_id, username FROM users WHERE doctor = 1");
    $doctors = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes rendez-vous</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <aside> <!-- Sidebar -->
        <nav>
            <ul>
                <li><a href="dashboard.php">Mes rendez-vous</a></li>
                <li><a href="infos_sante.php">Informations de santé</a></li>
                <li>Demander un rendez-vous</li>
                <li><a href="logout.php">Se déconnecter</a></li>
            </ul>
        </nav>
    </aside>
    <main>
        <div class="container">
            <form  action="rdv_demande.php" method="POST">
                <input type="hidden" name="users_id" value="<?php echo $_SESSION['users_id']; ?>">

                <label for="doctor_id">Docteur:</label>
                <select name="doctor_id" id="users" required>
                    <option value="">-- Sélectionner --</option>
                    <?php foreach ($doctors as $user): ?>
                        <option value="<?= htmlspecialchars($user['users_id']) ?>">
                            <?= htmlspecialchars($user['username']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label for="raison"><strong>Raison du rendez-vous:</strong></label>
                <input type="text" id="raison" name="raison" required>

                <label for="description"><strong>Description plus précise de la raison du rendez-vous:</strong></label>
                <input type="text" id="description" name="description" required>

                <label for="num_secu"><strong>Numéro de sécurité sociale:</strong></label>
                <input type="number" id="num_secu" name="num_secu" required>

                <label for="date"><strong>Date de disponnibilité:</strong></label>
                <input type="date" id="date" name="date" required>

                <button class="btn" type="submit" class="btn">Ajouter</button>
            </form>
        </div>
    </main>
</body>
</html>