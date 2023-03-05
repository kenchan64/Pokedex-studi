<?php

require("Image.php");

class ImagesManager
{
    private $db;

    public function __construct()
    {
        $dbName = "studi-pokedex";
        $port = 3306;
        $username = "root";
        $password = "";
        try {
            $this->db = new PDO("mysql:host=localhost;dbname=$dbName;port=$port", $username, $password);
        } catch (PDOException $exception) {
            echo $exception->getMessage();
        }
    }

    public function create(Image $image)
    {
        $req = $this->db->prepare("INSERT INTO `image` (name, path) VALUE (:name, :path)");

        $req->bindValue(":name", $image->getName(), PDO::PARAM_STR);
        $req->bindValue(":path", $image->getPath(), PDO::PARAM_STR);

        $req->execute();
    }

    public function get($id)
    {
        $req = $this->db->prepare("SELECT * FROM `image` WHERE id = :id");
        $req->execute([":id" => $id]);
        $data = $req->fetch();
        return new Image($data);
    }

    public function getLastImageId()
    {
        $req = $this->db->query("SELECT * FROM `image` ORDER BY id DESC LIMIT 1");
        return $req->fetch()["id"];
    }

    public function update(Image $image)
    {
        $req = $this->db->prepare("UPDATE `image` SET name = :name, path = :path");

        $req->bindValue(":name", $image->getName(), PDO::PARAM_STR);
        $req->bindValue(":path", $image->getPath(), PDO::PARAM_STR);

        $req->execute();
    }

    public function delete($id)
    {
        $req = $this->db->prepare("DELETE FROM `image` WHERE id = :id");
        $req->bindValue(":id", $id, PDO::PARAM_INT);
        $req->execute();
    }
}
