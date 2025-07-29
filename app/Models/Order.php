<?php

namespace App\Models;

use App\Database;
use PDO;

class Order
{
    private $conn;
    private $tableName = "pedidos";
    private $itemTableName = "pedido_itens";

    public function __construct()
    {
        $this->conn = Database::getInstance()->getConnection();
    }

    public function create($subtotal, $freight, $total, $cep, $address, $number, $complement, $neighborhood, $city, $state, $customerEmail, $couponId = null)
    {
        $query = "INSERT INTO {$this->tableName} (subtotal, frete, total, cep, endereco, numero, complemento, bairro, cidade, estado, email_cliente, cupom_id)
                  VALUES (:subtotal, :frete, :total, :cep, :endereco, :numero, :complemento, :bairro, :cidade, :estado, :emailCliente, :cupomId)";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":subtotal", $subtotal);
        $stmt->bindParam(":frete", $freight);
        $stmt->bindParam(":total", $total);
        $stmt->bindParam(":cep", $cep);
        $stmt->bindParam(":endereco", $address);
        $stmt->bindParam(":numero", $number);
        $stmt->bindParam(":complemento", $complement);
        $stmt->bindParam(":bairro", $neighborhood);
        $stmt->bindParam(":cidade", $city);
        $stmt->bindParam(":estado", $state);
        $stmt->bindParam(":emailCliente", $customerEmail);
        $stmt->bindParam(":cupomId", $couponId, $couponId === null ? PDO::PARAM_NULL : PDO::PARAM_INT);

        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    public function addOrderItem($orderId, $productId, $stockId, $quantity, $unitPrice)
    {
        $query = "INSERT INTO " . $this->itemTableName . " (pedido_id, produto_id, variacao_id, quantidade, preco_unitario)
                  VALUES (:pedido_id, :produto_id, :variacao_id, :quantidade, :preco_unitario)";
        $stmt = $this->conn->prepare($query);

        $bindVariationId = ($stockId == 0) ? null : $stockId;
        $bindVariationType = ($stockId == 0) ? PDO::PARAM_NULL : PDO::PARAM_INT;

        $stmt->bindParam(":pedido_id", $orderId, PDO::PARAM_INT);
        $stmt->bindParam(":produto_id", $productId, PDO::PARAM_INT);
        $stmt->bindParam(":variacao_id", $bindVariationId, $bindVariationType);
        $stmt->bindParam(":quantidade", $quantity, PDO::PARAM_INT);
        $stmt->bindParam(":preco_unitario", $unitPrice);

        return $stmt->execute();
    }

    public function find($id)
    {
        $query = "SELECT * FROM {$this->tableName} WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateStatus($id, $status)
    {
        $query = "UPDATE {$this->tableName} SET status = :status WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":status", $status);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }

    public function delete($id)
    {
        $query = "DELETE FROM {$this->tableName} WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }

    public function getOrderItems($orderId)
    {
        $query = "SELECT oi.*, p.nome as produto_nome, s.variacao as variacao_nome
                  FROM " . $this->itemTableName . " oi
                  JOIN produtos p ON oi.produto_id = p.id
                  LEFT JOIN estoque s ON oi.variacao_id = s.id
                  WHERE oi.pedido_id = :pedido_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":pedido_id", $orderId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
