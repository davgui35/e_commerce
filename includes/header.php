<?php
session_start();
try{

    $bd = new PDO('mysql:host=localhost;dbname=site-e-commerce;charset=utf8', 'root', '');
    $bd->setAttribute(PDO::ATTR_CASE, PDO::CASE_LOWER);
    $bd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}catch(PDOException $e){
    echo "Une erreur est survenue". $e->getMessage();
    die();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./style/style.css">
</head>
<header>
    <h1>Site E-Commerce</h1>
    <nav class="menu">
        <ul>
            <li><a href="./index.php">Accueil</a></li>
            <li><a href="./Boutique.php">Boutique</a></li>
            <li><a href="./panier.php">Panier</a></li>
            <li><a href="./inscription.php">Inscription</a></li>
            <li><a href="./connexion.php">Connexion</a></li>
        </ul>
    </nav>
</header>
<?php require_once 'includes/aside.php'; ?>