<?php session_start(); ?>
<link rel="stylesheet" href="../style/style_admin.css">
<?php
try {

    $bd = new PDO('mysql:host=localhost;dbname=site-e-commerce;charset=utf8', 'root', '');
    $bd->setAttribute(PDO::ATTR_CASE, PDO::CASE_LOWER);
    $bd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Une erreur est survenue" . $e->getMessage();
    die();
}
?>

<h1>Bienvenue, <?= $_SESSION['username']; ?></h1>
<p><a href="?action=add">Ajouter un produit</a><a href="?action=modifyanddelete">Modifier ou supprimer un produit</a>
</p>
<p><a href="?action=add_category">Ajouter une categorie</a>
<a href="?action=modifyanddelete_category">Modifier ou supprimer une categorie</a><a href="?action=options">Options</a></p>
<?php
if (isset($_SESSION['username'])) {
    if (isset($_GET['action'])) {
        if ($_GET['action'] == 'add') {

            if (isset($_POST['submit'])) {
                //var_dump($_POST);
                $stock = $_POST['stock'];
                $title = $_POST['title'];
                $description = $_POST['description'];
                $price = $_POST['price'];
                $category = $_POST['category'];
                //Nom de l'image
                $img = $_FILES['img']['name'];
                //Emplacement temporaire de l'image
                $img_tmp = $_FILES['img']['tmp_name'];
                //Si l'emplacement temp n'est pas vide
                if (!empty($img_tmp)) {

                    //On explose au point la variable image
                    $image = explode('.', $img);
                    //Récupération de l'extension de l'image
                    $image_extension = end($image);
                    //print_r($image_extension);=>jpg

                    //Si on ne trouve pas les extensions dans le tableau
                    if (in_array(strtolower($image_extension), array('png', 'jpg', 'jpeg')) === false) {

                        echo "Veuillez rentrer une image ayant pour extension : png, jpg ou jpeg";
                    } else {
                        $image_size = getimagesize($img_tmp);
                        //print_r($image_size);

                        if ($image_size['mime'] == 'image/jpeg') {
                            $image_src = imagecreatefromjpeg($img_tmp);
                        } else if ($image_size['mine'] == 'image/png') {
                            $image_src = imagecreatefrompng($img_tmp);
                        } else {
                            $image_src = false;
                            echo "Veuillez entrer une image valide";
                        }

                        //Redimensionner l'image 200/200
                        if ($image_src !== false) {
                            $image_width = 200;
                            if ($image_size[0] == $image_width) {
                                $image_finale = $image_src;
                            } else {
                                $new_width[0] = $image_width;
                                $new_height[1] = 200;
                                $image_finale = imagecreatetruecolor($new_width[0], $new_height[1]);

                                imagecopyresampled($image_finale, $image_src, 0, 0, 0, 0, $new_width[0], $new_height[1], $image_size[0], $image_size[1]);
                            }
                            //Envoie dans le dossier imgs 
                            imagejpeg($image_finale, 'imgs/' . $title . '.jpg');
                        }
                    }
                } else {
                    echo 'Veuillez entrer une image!!!';
                }
            }

            if(isset($title) && isset($description) && isset($price) &&isset($stock)){
                $category = $_POST['category'];
                $weight = $_POST['weight'];

                $select = $bd->query("SELECT price FROM weight WHERE name='$weight'");
                $data = $select->fetch(PDO::FETCH_OBJ);

                $shipping = $data->price;
                $old_price = $price;
                $final_price = $old_price + $shipping;

                
                $select=$bd->query("SELECT tva FROM products");
                $data1 = $select->fetch(PDO::FETCH_OBJ);
                $tva = $data1->tva;
                $result_final = ($final_price* $tva/100) + $final_price;

                //Insertion d'un article dans la base de données
                $req = $bd->prepare("INSERT INTO products (title, description, price, category, weight, shipping, tva, final_price, stock) VALUES (:title, :description, :price, :category, :weight, :shipping, :tva, :final_price, :stock)");
                $req->bindValue(':title', $title);
                $req->bindValue(':description', $description);
                $req->bindValue(':price', $price);
                $req->bindValue(':category', $category);
                $req->bindValue(':weight', $weight);
                $req->bindValue(':shipping', $shipping);
                $req->bindValue(':tva', $tva);
                $req->bindValue(':final_price', $result_final);
                $req->bindValue(':stock', $stock);
                $result = $req->execute();
                if(!$result){
                    echo "Un problème est survenu, l'enregistrement n'a pas été effectué!";
                }else{
                    echo "Le produit est enregistré!!";
                }


            }else{
                $message_error = "Veuillez remplir tous les champs";
            }
?>

            <form action="" method="post" enctype="multipart/form-data">
                <div>
                    <label for="title">Titre :</label>
                    <input type="text" name="title" id="title">
                </div>
                <div>
                    <label for="description">Description :</label>
                    <textarea name="description" id="description"></textarea>
                </div>
                <div>
                    <label for="price">Prix :</label>
                    <input type="text" name="price" id="price">
                </div>
                <div>
                    <label for="img">Image :</label>
                    <input type="file" name="img" id="img">
                </div>
                <div>
                    <label for="category">Catégorie :</label>
                    <select name="category" id="category">
                        <?php
                        $req = "SELECT * FROM category";
                        $categories = $bd->query($req);
                        ?>
                        <?php foreach ($categories as $category) : ?>
                           <option name="category" id="category"><?= $category['name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label for="poids">Poids <em>(Plus de :)</em></label>
                    <select name="weight" id="weight">
                        <?php 
                            $req="SELECT * FROM weight";
                            $weight = $bd->query($req);
                        ?>

                        <?php foreach($weight as $article_weight): ?>
                            <option name="weight"><?= $article_weight['name']?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label for="stock">Stock :</label>
                    <input type="text" name="stock" id="stock">
                </div>
                <input type="submit" value="valider" name="submit">
            </form>
            <?php if (isset($message_error)) : ?>
                <p class="error"><?= $message_error; ?></p>
            <?php endif; ?>

            <?php


        } elseif ($_GET['action'] == 'modifyanddelete') {

            if ($bd) {
                //Selection de tous les articles
                $req = "SELECT * FROM products";
                $articles = $bd->query($req);
                foreach ($articles as $article) {
                    echo $article['title'];
            ?>
                    <a href="?action=modify&amp;id=<?php echo $article['id']; ?>">Modifier</a>
                    <a href="?action=delete&amp;id=<?php echo $article['id']; ?>">Supprimer</a><br /><br />
            <?php
                }
            }
        } elseif ($_GET['action'] == 'modify') {
            //Récupération d'un article selon id
            $id = $_GET['id'];
            $req = "SELECT * FROM products WHERE id=$id";
            $result = $bd->prepare($req);
            $result->execute();
            $data = $result->fetch(PDO::FETCH_ASSOC);

            //var_dump($data);

            ?>

            <form action="" method="post">
                <div>
                    <label for="title">Titre :</label>
                    <input type="text" name="title" id="title" value="<?= $data['title']; ?>">
                </div>
                <div>
                    <label for="description">Description :</label>
                    <textarea name="description" id="description"><?= $data['description']; ?></textarea>
                </div>
                <div>
                    <label for="price">Prix :</label>
                    <input type="text" name="price" id="price" value="<?= $data['price']; ?>">
                </div>
                <div>
                    <label for="stock">Stock :</label>
                    <input type="text" value="<?= $data['stock']; ?>" name="stock" id="stock">
                </div>
                <input type="submit" value="Modifier" name="submit">
            </form>
<?php
            if (isset($_POST['submit'])) {

                $data = [
                    'stock' => $_POST['stock'],
                    'title' => $_POST['title'],
                    'description' => $_POST['description'],
                    'price' => $_POST['price'],
                ];

                $req = "UPDATE products SET title=:title,description=:description, price=:price, stock=:stock WHERE id=$id";
                $result = $bd->prepare($req);
                $result->execute($data);

                header('Location: admin.php?action=modifyanddelete');
            }
        } elseif ($_GET['action'] == 'delete') {
            //Suppression d'un article
            $id = $_GET['id'];
            $req = $bd->prepare("DELETE FROM products WHERE id=$id");
            $result = $req->execute();
            if (!$result) {
                echo "Le produit n'a pas été supprimé. Une erreur est survenue!!!";
            } else {
                echo "Le produit a bien été supprimé!";
            }
        }else if($_GET['action'] == 'add_category'){
            //Ajoute une categorie
            if(isset($_POST['submit'])) {
                $name = $_POST['name_category'];
                $req = $bd->prepare("INSERT INTO category (name) VALUES (:name)");
                $req->bindValue(":name", $name);
                $result = $req->execute();
                if(!$result){
                    echo "Un problème est survenu, l'enregistrement n'a pas été effectué!";
                }else{
                    echo "La categorie est enregistrée!!";
                }
            }else{
                echo "Veuillez remplir tous les champs";
            }
            ?>

            <form action="" method="POST">
                <label for="name_category">Titre de la catégorie :</label>
                <input type="text" name="name_category" id="name_category">
                <input type="submit" value="Ajouter" name="submit">
            </form>

            <?php
        
    }elseif ($_GET['action'] == 'modifyanddelete_category') {
            //Selection de tous les categories
            $req = "SELECT * FROM category";
            $categories = $bd->query($req);
            foreach ($categories as $category) {
                echo $category['name'];
        ?>
                <a href="?action=modify_category&amp;id=<?php echo $category['id']; ?>">Modifier</a>
                <a href="?action=delete_category&amp;id=<?php echo $category['id']; ?>">Supprimer</a><br /><br />
        <?php
            }        
    }else if($_GET['action'] == 'modify_category'){
       //Récupération d'un article selon id
       $id = $_GET['id'];
       $req = "SELECT * FROM category WHERE id=$id";
       $result = $bd->prepare($req);
       $result->execute();
       $data = $result->fetch(PDO::FETCH_ASSOC);

        var_dump($data);
       ?>

       <form action="" method="post">
           <div>
               <label for="name">Titre de la categorie :</label>
               <input type="text" name="name" id="name" value="<?= $data['name']; ?>">
           </div>
           <input type="submit" value="Modifier" name="submit">
       </form>
<?php
       if (isset($_POST['submit'])) {
           var_dump($_POST);
           $name = $_POST['name'];
           $req = "UPDATE category SET name=:name WHERE id=$id";
           $result = $bd->prepare($req);
           $result->execute(array('name'=>$name));
           if(!$result){
            echo "Un problème est survenu, l'enregistrement n'a pas été effectué!";
        }else{
            echo "La categorie est modifiée!!";
        }

        header('Location: admin.php?action=modifyanddelete_category');

       }

    }else if($_GET['action'] == 'delete_category'){
         //Suppression d'un article
         $id = $_GET['id'];
         $req = $bd->prepare("DELETE FROM category WHERE id=$id");
         $result = $req->execute();
         header('Location: admin.php?action=modifyanddelete_category');
    
    }else if($_GET['action'] == 'options'){
        ?>

        <h2>Frais de ports :</h2>
        <h3>Options de poids <em>(Plus de )</em></h3>

        <?php

        $req = "SELECT * FROM weight";
        $select = $bd->query($req);

        while($data = $select->fetch(PDO::FETCH_OBJ)):?>

            <form action="" method="post">
                <input type="text" name="weight" id="weight" value="<?= $data->name; ?>">
               <a href="?action=modify_weight&amp;name=<?= $data->name; ?>">Modifier</a>
            </form>

        <?php endwhile;?>

        <?php 
        
        $select = $bd->query("SELECT tva FROM products");
        $data = $select->fetch(PDO::FETCH_OBJ);

            if(isset($_POST['submitTVA'])){
                $tva = $_POST['tva'];
                if($tva) {
                    $update = $bd->query("UPDATE products SET tva=$tva");
                }
                header('Location: admin/admin.php?action=options');
            }

        ?>


        <form action="" method="post">
        <h3>TVA :</h3>
            <input type="text" name="tva" id="tva" value="<?= $data->tva; ?>">
            <input type="submit" name="submitTVA" value="Modifier">
        </form>

        <?php

    
    }else if($_GET['action']== 'modify_weight'){
        $old_weight = $_GET['name'];
        $select = $bd->query("SELECT * FROM weight WHERE name=$old_weight");
        $data = $select->fetch(PDO::FETCH_OBJ);

        if(isset($_POST['submit'])){

            $weight = $_POST['weight'];
            $price = $_POST['price'];

            if($weight&&$price){
                $req = "UPDATE weight SET name=$weight, price=$price WHERE name=$old_weight";
                $update = $bd->query($req);
            }

        }
        ?>

        <h2>Frais de ports :</h2>
        <h3>Options de poids <em>(Plus de )</em></h3>
        <form action="" method="post">
            <div>
                <label for="weight">Poids :</label>
                <input type="text" name="weight" id="weight" value="<?= $_GET['name']; ?>">
            </div>
            <div>
                <label for="price">Correspond à :</label>
                <input type="text" name="price" id="price" value="<?= $data->price; ?>">€
            </div>
            <input type="submit" name="submit" value="Modifier">
        </form>

        <?php

    }else {
            die('Une erreur s\'est produite');
        }
    }
} else {
    header('Location: ../index.php');
}

?>