<?php

namespace App\Models;

use App\Database;
use PDO;

class Coupon
{
    private $conn;
    private $tableName = "cupons";

    public function __construct()
    {
        $this->conn = Database::getInstance()->getConnection();
    }

    public function create($code, $type, $value, $validFrom, $validTo, $minValue = null)
    {
        $query = "INSERT INTO {$this->tableName} (codigo, tipo, valor, validade_inicio, validade_fim, valor_minimo)
                  VALUES (:codigo, :tipo, :valor, :validade_inicio, :validade_fim, :valor_minimo)";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":codigo", $code);
        $stmt->bindParam(":tipo", $type);
        $stmt->bindParam(":valor", $value);
        $stmt->bindParam(":validade_inicio", $validFrom);
        $stmt->bindParam(":validade_fim", $validTo);
        $stmt->bindParam(":valor_minimo", $minValue);

        return $stmt->execute();
    }

    public function findByCode($code)
    {
        $query = "SELECT * FROM {$this->tableName} WHERE codigo = :codigo AND ativo = TRUE";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":codigo", $code);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function all()
    {
        $query = "SELECT * FROM {$this->tableName}";
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