<!doctype html>
<html lang="fr-FR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous">
    </script>
    <title>Pokedex Studi - Créer un Pokemon</title>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="./index.php">
            <img src="./images/logo.png" width="60" alt="Logo Pokedex">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="./index.php">Accueil</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Types</a>
                </li>
                <form class="d-flex">
                    <input class="form-control me-2" type="search" placeholder="Chercher" aria-label="Search">
                    <button class="btn btn-outline-success" type="submit">Chercher</button>
                </form>
        </div>
    </div>
</nav>

<?php
require("PokemonsManager.php");
require("TypesManager.php");
require("ImagesManager.php");
$pokemonManager = new PokemonsManager();
$typeManager = new TypesManager();
$types = $typeManager->getAll();
$error = null;

if ($_POST) {
    $number = $_POST["number"];
    $name = $_POST["name"];
    $description = $_POST["description"];
    $idType1 = $_POST["type1"];
    $idType2 = $_POST["type2"] === "null" ? : $_POST["type2"];

    try {
        if ($_FILES["image"]["size"] < 2000000) {
            $imagesManager = new ImagesManager();
            $fileName = $_FILES["image"]["name"];
            if (!is_dir("upload/")) {
                mkdir("upload/");
            }
            $targetFile = "upload/{$fileName}";
            $fileExtension = pathinfo($targetFile, PATHINFO_EXTENSION);
            define("EXTENSIONS", ["png", "jpeg", "jpg", "webp"]);

            if (in_array(strtolower($fileExtension), EXTENSIONS)) {
                if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
                    $imagesManager = new ImagesManager();
                    $image = new Image(["name" => $fileName, "path" => $targetFile]);
                    $imagesManager->create($image);
                } else {
                    throw new Exception("Une erreur est survenue...");
                }
            } else {
                throw new Exception("L'extension du fichier n'est pas correcte.");
            }
        } else {
            throw new Exception("Le fichier soumis est trop volumineux.");
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }

    $idImage = $imagesManager->getLastImageId();
    $newPokemon = new Pokemon([
            "number" => $number,
            "name" => $name,
            "description" => $description,
            "type1" => $idType1,
            "type2" => $idType2,
            "image" => $idImage,
    ]);

    $pokemonManager->create($newPokemon);
    header("Location: index.php");
}
?>

<main class="container">
    <?php
    if ($error) {
        echo "<p class='alert alert-danger'>$error</p>";
    }
    ?>
    <form method="post" enctype="multipart/form-data">
        <label for="number" class="form-label">Numéro</label>
        <input type="number" name="number" placeholder="Le numéro du Pokemon" id="number" class="form-control" min=1
               max=901>
        <label for="name" class="form-label">Nom</label>
        <input type="text" name="name" placeholder="Le nom du Pokemon" id="name" class="form-control" minlength="3"
               maxlength="40">
        <label for="description" class="form-label">Description</label>
        <textarea name="description" id="description" class="form-control" rows="6"
                  placeholder="La description du Pokemon" minlength="10" maxlength="500"></textarea>
        <label for="type1" class="form-label">Type 1</label>
        <select name="type1" id="type1" class="form-select">
            <option value="">--</option>
            <?php foreach ($types as $type): ?>
                <option value="<?= $type->getId() ?>"><?= $type->getName() ?></option>
            <?php endforeach ?>
        </select>

        <label for="type2" class="form-label">Type 2</label>
        <select name="type2" id="type2" class="form-select">
            <option value="null">--</option>
            <?php foreach ($types as $type): ?>
                <option value="<?= $type->getId() ?>"><?= $type->getName() ?></option>
            <?php endforeach ?>
        </select>
        <br>
        <label for="image" class="form-label">Image</label>
        <input type="file" name="image" id="image" class="form-control">
        <input type="submit" class="btn btn-success mt-3" value="Créer">
    </form>
</main>
</body>
</html>
