<?php require_once 'includes/header.php';?>
<aside>
    <div>
        <h3>Derniers Articles</h3>

        <?php 
        $req = "SELECT * FROM products ORDER BY id DESC LIMIT 0,3";
        $articles = $bd->query($req);
        ?>

        <div style="text-align:center;">

            <?php foreach($articles as $article): ?>
                <?php
                $lenght = 45;
                 //Ajout des ... à partir de 50
                $new_decription=substr($article['description'],0,$lenght)."..."; 
                //retour à la ligne
                $description_finale=wordwrap($new_decription,35,'<br/>', true); ?>
                <div style="margin:10px;">
                    <img width="30%" src="admin/imgs/<?= $article['title']; ?>".jpg alt="image de l'article">
                    <h2><?= $article['title']; ?></h2>
                    <p><?= $description_finale; ?></p>
                    <h4><?= $article['price']; ?> €</h4>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</aside> 