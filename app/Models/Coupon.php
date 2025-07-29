<?php

namespace App\Models;

use App\Database;
use PDO;

class Coupon
{
    private $conn;
    private $table_name = "cupons";

    public function __construct()
    {
        $this->conn = Database::getInstance()->getConnection();
    }

    public function create($code, $type, $value, $valid_from, $valid_to, $min_value = null)
    {
        $query = "INSERT INTO " . $this->table_name . " (codigo, tipo, valor, validade_inicio, validade_fim, valor_minimo)
                  VALUES (:codigo, :tipo, :valor, :validade_inicio, :validade_fim, :valor_minimo)";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":codigo", $code);
        $stmt->bindParam(":tipo", $type);
        $stmt->bindParam(":valor", $value);
        $stmt->bindParam(":validade_inicio", $valid_from);
        $stmt->bindParam(":validade_fim", $valid_to);
        $stmt->bindParam(":valor_minimo", $min_value);

        return $stmt->execute();
    }

    public function findByCode($code)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE codigo = :codigo AND ativo = TRUE";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":codigo", $code);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function all()
    {
        $query = "SELECT * FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function isValid($coupon, $subtotal)
    {
        $now = date('Y-m-d H:i:s');
        if ($now < $coupon['validade_inicio'] || $now > $coupon['validade_fim']) {
            return false;
        }
        if ($coupon['valor_minimo'] !== null && $subtotal < $coupon['valor_minimo']) {
            return false;
        }
        return true;
    }
}