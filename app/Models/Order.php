<?php

namespace App\Models;

use App\Database;
use PDO;

class Order
{
    private $conn;
    private $table_name = "pedidos";
    private $item_table_name = "pedido_itens";

    public function __construct()
    {
        $this->conn = Database::getInstance()->getConnection();
    }

    public function create($subtotal, $freight, $total, $cep, $address, $number, $complement, $neighborhood, $city, $state, $customer_email, $coupon_id = null)
    {
        $query = "INSERT INTO " . $this->table_name . " (subtotal, frete, total, cep, endereco, numero, complemento, bairro, cidade, estado, email_cliente, cupom_id)
                  VALUES (:subtotal, :frete, :total, :cep, :endereco, :numero, :complemento, :bairro, :cidade, :estado, :email_cliente, :cupom_id)";
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
        $stmt->bindParam(":email_cliente", $customer_email);
        $stmt->bindParam(":cupom_id", $coupon_id, $coupon_id === null ? PDO::PARAM_NULL : PDO::PARAM_INT);

        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    public function addOrderItem($order_id, $product_id, $stock_id, $quantity, $unit_price)
    {
        $query = "INSERT INTO " . $this->item_table_name . " (pedido_id, produto_id, variacao_id, quantidade, preco_unitario)
                  VALUES (:pedido_id, :produto_id, :variacao_id, :quantidade, :preco_unitario)";
        $stmt = $this->conn->prepare($query);

        $bind_variacao_id = ($stock_id == 0) ? null : $stock_id;
        $bind_variacao_type = ($stock_id == 0) ? PDO::PARAM_NULL : PDO::PARAM_INT;

        $stmt->bindParam(":pedido_id", $order_id, PDO::PARAM_INT);
        $stmt->bindParam(":produto_id", $product_id, PDO::PARAM_INT);
        $stmt->bindParam(":variacao_id", $bind_variacao_id, $bind_variacao_type);
        $stmt->bindParam(":quantidade", $quantity, PDO::PARAM_INT);
        $stmt->bindParam(":preco_unitario", $unit_price);

        return $stmt->execute();
    }

    public function find($id)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateStatus($id, $status)
    {
        $query = "UPDATE " . $this->table_name . " SET status = :status WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":status", $status);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }

    public function delete($id)
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }

    public function getOrderItems($order_id)
    {
        $query = "SELECT oi.*, p.nome as produto_nome, s.variacao as variacao_nome
                  FROM " . $this->item_table_name . " oi
                  JOIN produtos p ON oi.produto_id = p.id
                  LEFT JOIN estoque s ON oi.variacao_id = s.id
                  WHERE oi.pedido_id = :pedido_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":pedido_id", $order_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
