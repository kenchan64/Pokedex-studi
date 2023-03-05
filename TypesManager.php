<?php

require("Type.php");

class TypesManager
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

    public function create(Type $type)
    {
        $req = $this->db->prepare("INSERT INTO `type` (name, color) VALUE (:name, :color)");

        $req->bindValue(":name", $type->getName(), PDO::PARAM_STR);
        $req->bindValue(":color", $type->getColor(), PDO::PARAM_STR);

        $req->execute();
    }

    public function get($id): Type
    {
        $req = $this->db->prepare("SELECT * FROM `type` WHERE id = :id");
        $req->bindValue(":id", $id, PDO::PARAM_INT);
        $data = $req->fetch();
        return new Type($data);
    }

    public function getAll()
    {
        $types = [];
        $req = $this->db->query("SELECT * FROM `type` ORDER BY name");
        $datas = $req->fetchAll();
        foreach ($datas as $data) {
            $type = new Type($data);
            $types[] = $type;
        }
        $req->closeCursor();
        return $types;
    }

    public function getAllByString($input)
    {
        $types = [];
        $req = $this->db->prepare("SELECT * FROM `type` WHERE name LIKE :input ORDER BY name");
        $req->bindValue(":input", $input, PDO::PARAM_STR);
        $datas = $req->fetchAll();
        foreach ($datas as $data) {
            $type = new Type($data);
            $types[] = $type;
        }
        return $types;
    }

    public function getAllByType($input)
    {
        $types = [];
        $req = $this->db->prepare("SELECT * FROM `type` WHERE type LIKE :input ORDER BY number");
        $req->bindValue(":input", $input, PDO::PARAM_STR);
        $datas = $req->fetchAll();
        foreach ($datas as $data) {
            $type = new Type($data);
            $types[] = $type;
        }
        return $types;
    }

    public function update(Type $type)
    {
        $req = $this->db->prepare("UPDATE `type` SET name = :name, color = :color");

        $req->bindValue(":name", $type->getName(), PDO::PARAM_STR);
        $req->bindValue(":color", $type->getColor(), PDO::PARAM_STR);

        $req->execute();
    }

    public function delete($id)
    {
        $req = $this->db->prepare("DELETE FROM `type` WHERE id = :id");
        $req->bindValue(":id", $id, PDO::PARAM_INT);
        $req->execute();
    }
}
