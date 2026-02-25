<?php
include 'db.php';

// Vérifier si la requête POST a été envoyée et si le bouton de soumission a été cliqué
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit'])) {

    // Récupération des données du formulaire
    $email = $_POST['email'];
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $telephone = $_POST['telephone'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];


// Vérifier si les mots de passe correspondent
if ($password !== $confirm_password) {
    die("Les mots de passe ne correspondent pas.");
}

// Sécuriser le mot de passe en le hachant
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Préparer la requête d'insertion
$sql = "INSERT INTO utilisateurs (email, nom, prenom, telephone, password) 
        VALUES (?, ?, ?, ?, ?)";

        // Préparer la requête avec PDO
        $stmt = $pdo->prepare($sql);    

        // Exécuter la requête avec les données du formulaire
        $stmt->execute([
            $email,
            $nom,
            $prenom, 
            $telephone,   
            $hashed_password;
        ]);

        echo "Inscription réussie !";

    } else {
        echo "Aucune donnée reçue.";
    }
?>