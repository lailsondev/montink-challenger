<?php

namespace App\Models;

class Cart
{
    public function __construct()
    {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
    }

    public function add($productId, $stockId, $quantity, $price, $productName, $variationName)
    {
        $itemKey = $productId . '_' . $stockId;
        if (isset($_SESSION['cart'][$itemKey])) {
            $_SESSION['cart'][$itemKey]['quantity'] += $quantity;
        } else {
            $_SESSION['cart'][$itemKey] = [
                'product_id' => $productId,
                'stock_id' => $stockId,
                'product_name' => $productName,
                'variation_name' => $variationName,
                'price' => $price,
                'quantity' => $quantity
            ];
        }
    }

    public function update($itemKey, $quantity)
    {
        if (isset($_SESSION['cart'][$itemKey])) {
            if ($quantity <= 0) {
                unset($_SESSION['cart'][$itemKey]);
            } else {
                $_SESSION['cart'][$itemKey]['quantity'] = $quantity;
            }
        }
    }

    public function remove($itemKey)
    {
        if (isset($_SESSION['cart'][$itemKey])) {
            unset($_SESSION['cart'][$itemKey]);
        }
    }

    public function getItems()
    {
        return $_SESSION['cart'];
    }

    public function getTotal()
    {
        $total = 0;
        foreach ($_SESSION['cart'] as $item) {
            $total += $item['price'] * $item['quantity'];
        }
        return $total;
    }

    public function clear()
    {
        $_SESSION['cart'] = [];
        $_SESSION['applied_coupon'] = null;
    }

    public function setCoupon($coupon_data)
    {
        $_SESSION['applied_coupon'] = $coupon_data;
    }

    public function getCoupon()
    {
        return $_SESSION['applied_coupon'] ?? null;
    }

    public function removeCoupon()
    {
        unset($_SESSION['applied_coupon']);
    }

    public function getDiscountedTotal()
    {
        $subtotal = $this->getTotal();
        $coupon = $this->getCoupon();

        if ($coupon) {
            if ($coupon['tipo'] === 'percentual') {
                $subtotal -= $subtotal * ($coupon['valor'] / 100);
            } elseif ($coupon['tipo'] === 'fixo') {
                $subtotal -= $coupon['valor'];
            }
            return max(0, $subtotal);
        }
        return $subtotal;
    }
}