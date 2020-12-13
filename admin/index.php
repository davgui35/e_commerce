<?php
session_start();
$admin = "admin1";
$password_admin = "azerty";
$is_valid = false;

    if(isset($_POST['submit'])){
       $username = htmlentities(strtolower(trim($_POST["username"])));
       $password = htmlentities(strtolower(trim($_POST['password'])));

       if(empty($username)){
           $message_username = "Le champ pseudo est vide";
           $is_valid = false;
       }
       
       if(empty($password)) {
           $message_password = "Le champ mot de passe est vide";
           $is_valid = false;
        }

        if($username == $admin && $password === $password_admin ){
            $is_valid = true;
            echo $is_valid;
        }
        if($is_valid){
            $_SESSION['username']  = $username;
            header('Location: admin.php');
            exit();
        }else{
            $message_admin = "Identifiants érronés";
        }
    }

   
?>
<link rel="stylesheet" href="../style/index_admin.css">
<h1>Administration - connexion</h1>
<form action="" method="post">
    <div>
        <label for="username">Pseudo</label>
        <input type="text" name="username" id="username">
        <?php if(isset($message_username)): ?>
            <p class="error"><?= $message_username ?></p>
        <?php endif; ?>
    </div>
    <div>
        <label for="password">Mot de passe</label>
        <input type="password" name="password" id="password">
        <?php if(isset($message_password)): ?>
            <p class="error"><?= $message_password ?></p>
        <?php endif; ?>
    </div>
    <input type="submit" name="submit" value="Se connecter">
    <?php if(isset($message_admin)): ?>
        <p class="error"><?= $message_admin?></p>
    <?php endif; ?>
</form>