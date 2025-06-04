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

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = $_POST['users_id'];
        $username = $_POST['username'];
        $email = $_POST['email'];

        // Mise à jour des données
        $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ? WHERE users_id = ?");
        $stmt->execute([$username, $email, $id]);
    }

    $users = $pdo->query("SELECT users_id, username, email, doctor FROM users")->fetchAll(PDO::FETCH_ASSOC);

    $sql_user = "SELECT username FROM users WHERE users_id = :user_id";
    $stmt_user = $pdo->prepare($sql_user);
    $stmt_user->execute(['user_id' => $user_id]);
    $user = $stmt_user->fetch(PDO::FETCH_ASSOC);

    if ($user && $user['username'] !== 'admin') {
        header("Location: dashboard.php");
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

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_user'])) {
    $users_id = intval($_POST['users_id']);

        try {
            // DÉBUT DE TRANSACTION
            $pdo->beginTransaction();

            // Suppression dans les autres tables si nécessaire
            $pdo->prepare("DELETE FROM rdv2 WHERE patient_id = ?")->execute([$users_id]);
            $pdo->prepare("DELETE FROM rdv2 WHERE doctor_id = ?")->execute([$users_id]);
            $pdo->prepare("DELETE FROM infos WHERE patient_id = ?")->execute([$users_id]);

            // Suppression de l'utilisateur
            $pdo->prepare("DELETE FROM users WHERE users_id = ?")->execute([$users_id]);

            // COMMIT
            $pdo->commit();
        } catch (Exception $e) {
            $pdo->rollBack();
            echo "Erreur lors de la suppression : " . $e->getMessage();
        }
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $users_id = $_POST['users_id'];
        $username = $_POST['username'];
        $email = $_POST['email'];
        $doctor = $_POST['doctor'];

        // Sécurisation basique
        $users_id = intval($users_id);
        $doctor = intval($doctor);

        // Mise à jour en base de données
        $stmt = $pdo->prepare('UPDATE users SET username = ?, email = ?, doctor = ? WHERE users_id = ?');
        $stmt->execute([$username, $email, $doctor, $users_id]);

        $stmt = $pdo->query('SELECT * FROM users');
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard administrateur</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <aside> <!-- Sidebar -->
        <nav>
            <ul>
                <li><a href="logout.php">Se déconnecter</a></li>
            </ul>
        </nav>
    </aside>
    <main>
        <?php foreach ($users as $user): ?>
            <form method="POST">
                <input type="hidden" name="users_id" value="<?= htmlspecialchars($user['users_id']) ?>">
                <table>
                    <tr>
                        <th>Username</th>
                        <td><input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>"></td>
                    </tr>
                    <tr>
                        <th>Email</th>
                        <td><input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>"></td>
                    </tr>
                    <tr>
                        <th>Docteur</th>
                        <td><select name="doctor">
                            <option value=0 <?= $user['doctor'] == 0 ? 'selected' : '' ?>>Patient</option>
                            <option value=1 <?= $user['doctor'] == 1 ? 'selected' : '' ?>>Docteur</option>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <button type="submit">Mettre à jour</button>
                            <button type="submit" name="delete_user" onclick="return confirm('Supprimer cet utilisateur ?');" style="background-color:red;color:white;">Supprimer</button>
                        </td>
                    </tr>
                </table>
            </form>
        <?php endforeach; ?>
    </main>
</body>
</html>