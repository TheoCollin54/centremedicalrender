<?php
// Connexion à la base de données
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

// Vérification si le formulaire est soumis
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Récupération et sécurisation des données
    $patient_id = htmlspecialchars(trim($_POST['users_id']));
    $title = htmlspecialchars(trim($_POST['title']));
    $description = htmlspecialchars(trim($_POST['description']));

    // Insertion en base de données
    try {
        $stmt = $pdo->prepare("INSERT INTO infos (patient_id, title, description) VALUES (?, ?, ?)");
        $stmt->execute([$patient_id, $title, $description]);
        echo "Ajout réussie !";
        header("Location: dashboard_doctor.php");
    } catch (PDOException $e) {
        if ($e->errorInfo[1] == 1062) {
            echo "Erreur";
        } else {
            echo "Erreur lors de l'inscription : " . $e->getMessage();
        }
    }
}
?>