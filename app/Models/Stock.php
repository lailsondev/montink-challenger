<?php

namespace App\Models;

use App\Database;
use PDO;

class Stock
{
    private $conn;
    private $tableName = "estoque";

    public function __construct()
    {
        $this->conn = Database::getInstance()->getConnection();
    }

    public function create($productId, $variation, $quantity, $price)
    {
        $query = "INSERT INTO {$this->tableName} (produto_id, variacao, quantidade, preco) VALUES (:produto_id, :variacao, :quantidade, :preco)";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":produto_id", $productId, PDO::PARAM_INT);
        $stmt->bindParam(":variacao", $variation);
        $stmt->bindParam(":quantidade", $quantity, PDO::PARAM_INT);
        $stmt->bindParam(":preco", $price);

        return $stmt->execute();
    }

    public function update($id, $variation, $quantity, $price)
    {
        $query = "UPDATE {$this->tableName} SET variacao = :variacao, quantidade = :quantidade, preco = :preco WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->bindParam(":variacao", $variation);
        $stmt->bindParam(":quantidade", $quantity, PDO::PARAM_INT);
        $stmt->bindParam(":preco", $price);

        return $stmt->execute();
    }

    public function delete($id)
    {
        $query = "DELETE FROM {$this->tableName} WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function deleteByProductId($productId)
    {
        $query = "DELETE FROM {$this->tableName} WHERE produto_id = :produto_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":produto_id", $productId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function getStockForProduct($productId)
    {
        $query = "SELECT id, variacao, quantidade, preco FROM {$this->tableName} WHERE produto_id = :produto_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":produto_id", $productId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function decreaseStock($stockId, $quantity)
    {
        $query = "UPDATE estoque 
          SET quantidade = quantidade - :quantity 
          WHERE id = :stock_id AND quantidade >= :min_quantity";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":quantity", $quantity, PDO::PARAM_INT);
        $stmt->bindParam(":min_quantity", $quantity, PDO::PARAM_INT);
        $stmt->bindParam(":stock_id", $stockId, PDO::PARAM_INT);

        return $stmt->execute() && $stmt->rowCount() > 0;
    }

    public function increaseStock($stockId, $quantity)
    {
        $query = "UPDATE {$this->tableName} SET quantidade = quantidade + :quantity WHERE id = :stock_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":quantity", $quantity, PDO::PARAM_INT);
        $stmt->bindParam(":stock_id", $stockId, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
