<?php
// connexion.php
session_start();
require_once 'db.php';    // doit définir $host, $username, $password, $dbname...

$errors = [];

// traitement du post
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $role     = $_POST['role']     ?? '';      // 'client' ou 'agence'
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $username = trim($_POST['username'] ?? ''); // seulement pour les clients

    // connexion PDO
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8",
                       $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        die("Erreur de connexion : " . $e->getMessage());
    }

    if ($role === 'utilisateurs') {
        $stmt = $pdo->prepare(
            "SELECT id, password FROM utilisateurs
             WHERE email = ? AND username = ?");
        $stmt->execute([$email, $username]);
    } elseif ($role === 'agence') {
        $stmt = $pdo->prepare(
            "SELECT id, password FROM agence
             WHERE email = ?");
        $stmt->execute([$email]);
    } else {
        $stmt = false;
    }

    if ($stmt) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user && password_verify($password, $user['password'])) {
            // connexion réussie
            $_SESSION[$role . '_id'] = $user['id'];
            $dest = $role === 'utilisateurs' ? 'utilisateur_dashboard.php'
                                       : 'agence_dashboard.php';
            header("Location: $dest");
            exit;
        }
    }
    $errors[] = 'Identifiants incorrects.';
}
?>
