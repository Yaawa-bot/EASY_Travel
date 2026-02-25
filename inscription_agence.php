<?php
include 'db.php';

// Vérifier si la requête POST a été envoyée et si le bouton de soumission a été cliqué
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit'])) {

    // Récupération des données du formulaire
    $nom_agence = $_POST['nom_agence'];
    $localisation = $_POST['localisation'];
    $nombre_bus = $_POST['nombre_bus'];
    $email_agence = $_POST['email_agence'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];


// Vérifier si les mots de passe correspondent
if ($password !== $confirm_password) {
    die("Les mots de passe ne correspondent pas.");
}

// Sécuriser le mot de passe en le hachant
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Préparer la requête d'insertion
$sql = "INSERT INTO agence (nom_agence, localisation, nombre_bus, email_agence, mot_de_passe) 
        VALUES (?, ?, ?, ?, ?)";

        // Préparer la requête avec PDO
        $stmt = $pdo->prepare($sql);    

        // Exécuter la requête avec les données du formulaire
        $stmt->execute([
            $nom_agence,
            $localisation,
            $nombre_bus,
            $email_agence,
            $hashed_password
        ]);

        echo "Inscription réussie !";

    } else {
        echo "Aucune donnée reçue.";
    }
?>