<?php

namespace App\Models;

use App\Database;
use PDO;

class Product
{
    private $conn;
    private $table_name = "produtos";

    public function __construct()
    {
        $this->conn = Database::getInstance()->getConnection();
    }

    public function create($name)
    {
        $query = "INSERT INTO " . $this->table_name . " (nome) VALUES (:nome)";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":nome", $name);

        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    public function update($id, $name)
    {
        $query = "UPDATE " . $this->table_name . " SET nome = :nome WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":id", $id);
        $stmt->bindParam(":nome", $name);

        return $stmt->execute();
    }

    public function find($id)
    {
        $query = "SELECT p.*, GROUP_CONCAT(CONCAT(s.id, ':', s.variacao, ':', s.quantidade, ':', s.preco)) AS variacoes_estoque
                  FROM " . $this->table_name . " p
                  LEFT JOIN estoque s ON p.id = s.produto_id
                  WHERE p.id = :id
                  GROUP BY p.id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($product && !empty($product['variacoes_estoque'])) {
            $variations = explode(',', $product['variacoes_estoque']);
            $product['variacoes'] = [];
            foreach ($variations as $variation) {
                list($var_id, $var_name, $var_qty, $var_price) = explode(':', $variation);
                $product['variacoes'][] = [
                    'id' => (int)$var_id,
                    'nome' => $var_name,
                    'quantidade' => (int)$var_qty,
                    'preco' => (float)$var_price
                ];
            }
        } else {
            $product['variacoes'] = [];
        }

        return $product;
    }

    public function all()
    {
        $query = "SELECT p.*, GROUP_CONCAT(CONCAT(s.id, ':', s.variacao, ':', s.quantidade, ':', s.preco)) AS variacoes_estoque
                  FROM " . $this->table_name . " p
                  LEFT JOIN estoque s ON p.id = s.produto_id
                  GROUP BY p.id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($products as &$product) {
            if (!empty($product['variacoes_estoque'])) {
                $variations = explode(',', $product['variacoes_estoque']);
                $product['variacoes'] = [];
                foreach ($variations as $variation) {
                    list($var_id, $var_name, $var_qty, $var_price) = explode(':', $variation);
                    $product['variacoes'][] = [
                        'id' => (int)$var_id,
                        'nome' => $var_name,
                        'quantidade' => (int)$var_qty,
                        'preco' => (float)$var_price
                    ];
                }
            } else {
                $product['variacoes'] = [];
            }
        }

        return $products;
    }

    public function delete($id)
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }
}
