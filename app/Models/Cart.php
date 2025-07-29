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

    public function add($product_id, $stock_id, $quantity, $price, $product_name, $variation_name)
    {
        $item_key = $product_id . '_' . $stock_id;
        if (isset($_SESSION['cart'][$item_key])) {
            $_SESSION['cart'][$item_key]['quantity'] += $quantity;
        } else {
            $_SESSION['cart'][$item_key] = [
                'product_id' => $product_id,
                'stock_id' => $stock_id,
                'product_name' => $product_name,
                'variation_name' => $variation_name,
                'price' => $price,
                'quantity' => $quantity
            ];
        }
    }

    public function update($item_key, $quantity)
    {
        if (isset($_SESSION['cart'][$item_key])) {
            if ($quantity <= 0) {
                unset($_SESSION['cart'][$item_key]);
            } else {
                $_SESSION['cart'][$item_key]['quantity'] = $quantity;
            }
        }
    }

    public function remove($item_key)
    {
        if (isset($_SESSION['cart'][$item_key])) {
            unset($_SESSION['cart'][$item_key]);
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