<?php

namespace App\Models;

use App\Database;
use PDO;

class Stock
{
    private $conn;
    private $table_name = "estoque";

    public function __construct()
    {
        $this->conn = Database::getInstance()->getConnection();
    }

    public function create($product_id, $variation, $quantity, $price)
    {
        $query = "INSERT INTO " . $this->table_name . " (produto_id, variacao, quantidade, preco) VALUES (:produto_id, :variacao, :quantidade, :preco)";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":produto_id", $product_id, PDO::PARAM_INT);
        $stmt->bindParam(":variacao", $variation);
        $stmt->bindParam(":quantidade", $quantity, PDO::PARAM_INT);
        $stmt->bindParam(":preco", $price);

        return $stmt->execute();
    }

    public function update($id, $variation, $quantity, $price)
    {
        $query = "UPDATE " . $this->table_name . " SET variacao = :variacao, quantidade = :quantidade, preco = :preco WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->bindParam(":variacao", $variation);
        $stmt->bindParam(":quantidade", $quantity, PDO::PARAM_INT);
        $stmt->bindParam(":preco", $price);

        return $stmt->execute();
    }

    public function delete($id)
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function deleteByProductId($product_id)
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE produto_id = :produto_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":produto_id", $product_id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function getStockForProduct($product_id)
    {
        $query = "SELECT id, variacao, quantidade, preco FROM " . $this->table_name . " WHERE produto_id = :produto_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":produto_id", $product_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function decreaseStock($stock_id, $quantity)
    {
        $query = "UPDATE estoque 
          SET quantidade = quantidade - :quantity 
          WHERE id = :stock_id AND quantidade >= :min_quantity";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":quantity", $quantity, PDO::PARAM_INT);
        $stmt->bindParam(":min_quantity", $quantity, PDO::PARAM_INT);
        $stmt->bindParam(":stock_id", $stock_id, PDO::PARAM_INT);

        // Removi os var_dump/die que você adicionou para depuração
        return $stmt->execute() && $stmt->rowCount() > 0;
    }

    public function increaseStock($stock_id, $quantity)
    {
        $query = "UPDATE " . $this->table_name . " SET quantidade = quantidade + :quantity WHERE id = :stock_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":quantity", $quantity, PDO::PARAM_INT);
        $stmt->bindParam(":stock_id", $stock_id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
