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
    $doctor_id = htmlspecialchars(trim($_POST['doctor_id']));
    $raison = htmlspecialchars(trim($_POST['raison']));
    $description = htmlspecialchars(trim($_POST['description']));
    $num_secu = htmlspecialchars(trim($_POST['num_secu']));
    $date = htmlspecialchars(trim($_POST['date']));

    // Insertion en base de données
    try {
        $stmt = $pdo->prepare("INSERT INTO demande_rdv (patient_id, doctor_id, raison, description, num_secu, date) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$patient_id, $doctor_id, $raison, $description, $num_secu, $date]);
        echo "Demande réussie !";
        header("Location: demande_rdv.php");
    } catch (PDOException $e) {
        if ($e->errorInfo[1] == 1062) {
            echo "Erreur : La demande de rendez-vous a échouée. Veuillez réessayer ultérieurement.";
        } else {
            echo "Erreur lors de l'inscription : " . $e->getMessage();
        }
    }
}
?>