<?php

namespace App\Controllers;

use App\Models\Cart;
use App\Models\Product;
use App\Models\Stock;
use App\Models\Order;
use App\Models\Coupon;
use App\Services\FreightService;
use App\Services\ViaCepService;
use App\Services\EmailService;
use App\Database;

class CartController
{
    private $cart;
    private $productModel;
    private $stockModel;
    private $orderModel;
    private $couponModel;
    private $freightService;
    private $viaCepService;
    private $emailService;
    private $dbConnection;

    public function __construct()
    {
        $this->cart = new Cart();
        $this->productModel = new Product();
        $this->stockModel = new Stock();
        $this->orderModel = new Order();
        $this->couponModel = new Coupon();
        $this->freightService = new FreightService();
        $this->viaCepService = new ViaCepService();
        $this->emailService = new EmailService();
        $this->dbConnection = Database::getInstance()->getConnection();
    }

    public function add()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $productId = $_POST['product_id'] ?? null;
            $stockId = $_POST['stock_id'] ?? null;
            $quantity = (int)($_POST['quantity'] ?? 1);

            if (!$productId || !$stockId || $quantity <= 0) {
                $_SESSION['message'] = ['type' => 'error', 'text' => MSG_INVALID_ITEM_FOR_CART];
                header('Location: /products');
                exit();
            }

            $product = $this->productModel->find($productId);
            if (!$product) {
                $_SESSION['message'] = ['type' => 'error', 'text' => MSG_PRODUCT_NOT_FOUND];
                header('Location: /products');
                exit();
            }

            $selectedVariation = null;
            foreach ($product['variacoes'] as $variation) {
                if ($variation['id'] == $stockId) {
                    $selectedVariation = $variation;
                    break;
                }
            }

            if (!$selectedVariation) {
                $_SESSION['message'] = ['type' => 'error', 'text' => MSG_PRODUCT_VARIATION_NOT_FOUND];
                header('Location: /products');
                exit();
            }

            if ($selectedVariation['quantidade'] < $quantity) {
                $_SESSION['message'] = ['type' => 'error', 'text' => MSG_INSUFFICIENT_STOCK_FOR_VARIATION_SELECTED];
                header('Location: /products');
                exit();
            }

            $this->cart->add(
                $product['id'],
                $selectedVariation['id'],
                $quantity,
                $selectedVariation['preco'],
                $product['nome'],
                $selectedVariation['nome']
            );

            $_SESSION['message'] = ['type' => 'success', 'text' => MSG_PRODUCT_ADD_TO_CART];
            header('Location: /cart/view');
            exit();
        }
    }

    public function view()
    {
        $cartItems = $this->cart->getItems();
        $subtotal = $this->cart->getTotal();
        $discounted_subtotal = $this->cart->getDiscountedTotal();
        $freight = $this->freightService->calculateFreight($discounted_subtotal);
        $coupon = $this->cart->getCoupon();
        $total = $discounted_subtotal + $freight;
        require __DIR__ . '/../Views/cart/checkout.php';
    }

    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $itemKey = $_POST['item_key'] ?? null;
            $quantity = (int)($_POST['quantity'] ?? 0);

            if ($itemKey !== null && $quantity >= 0) {
                list($productId, $stockId) = explode('_', $itemKey);
                $product = $this->productModel->find($productId);
                if ($product) {
                    $selectedVariation = null;
                    foreach ($product['variacoes'] as $variation) {
                        if ($variation['id'] == $stockId) {
                            $selectedVariation = $variation;
                            break;
                        }
                    }

                    if ($selectedVariation) {
                        if ($quantity > $selectedVariation['quantidade']) {
                            $_SESSION['message'] = ['type' => 'error', 'text' => MSG_QTTY_EXCEEDED_FOR_VARIATION];
                        } else {
                            $this->cart->update($itemKey, $quantity);
                            $_SESSION['message'] = ['type' => 'success', 'text' => MSG_CART_UPDATED];
                        }
                    } else {
                        $_SESSION['message'] = ['type' => 'error', 'text' => MSG_PRODUCT_VARIATION_NOT_FOUND];
                    }
                } else {
                    $_SESSION['message'] = ['type' => 'error', 'text' => MSG_PRODUCT_NOT_FOUND_FOR_UPDATE_THIS_CART];
                }
            } else {
                $_SESSION['message'] = ['type' => 'error', 'text' => MSG_INVALID_DATA_FOR_UPDATE_CART];
            }
            header('Location: /cart/view');
            exit();
        }
    }

    public function remove()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $itemKey = $_POST['item_key'] ?? null;
            if ($itemKey !== null) {
                $this->cart->remove($itemKey);
                $_SESSION['message'] = ['type' => 'success', 'text' => MSG_ITEM_REMOVED_TO_CART];
            } else {
                $_SESSION['message'] = ['type' => 'error', 'text' => MSG_INVALID_ITEM_FOR_REMOVE];
            }
            header('Location: /cart/view');
            exit();
        }
    }

    public function checkout()
    {
        $cartItems = $this->cart->getItems();
        if (empty($cartItems)) {
            $_SESSION['message'] = ['type' => 'info', 'text' => MSG_CART_IS_EMPTY];
            header('Location: /products');
            exit();
        }

        $subtotal = $this->cart->getTotal();
        $discountedSubtotal = $this->cart->getDiscountedTotal();
        $freight = $this->freightService->calculateFreight($discountedSubtotal);
        $coupon = $this->cart->getCoupon();
        $total = $discountedSubtotal + $freight;

        $addressData = $_SESSION['address_data'] ?? [];
        unset($_SESSION['address_data']);

        require __DIR__ . '/../Views/cart/checkout.php';
    }

    public function processCheckout()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $cartItems = $this->cart->getItems();
            if (empty($cartItems)) {
                $_SESSION['message'] = ['type' => 'error', 'text' => MSG_CART_IS_EMPTY];
                header('Location: /products');
                exit();
            }

            $customerEmail = $_POST['email'] ?? '';
            $cep = $_POST['cep'] ?? '';
            $number = $_POST['number'] ?? '';
            $complement = $_POST['complement'] ?? '';

            if (empty($customerEmail) || empty($cep) || empty($number)) {
                $_SESSION['message'] = ['type' => 'error', 'text' => MSG_ALL_FIELDS_IS_REQUIRED];
                $_SESSION['address_data'] = $_POST;
                header('Location: /cart/checkout');
                exit();
            }

            $address = $this->viaCepService->getAddressByCep($cep);
            if (!$address) {
                $_SESSION['message'] = ['type' => 'error', 'text' => MSG_INVALID_CEP_OR_NOT_FOUND];
                $_SESSION['address_data'] = $_POST;
                header('Location: /cart/checkout');
                exit();
            }

            $subtotal = $this->cart->getTotal();
            $discounted_subtotal = $this->cart->getDiscountedTotal();
            $freight = $this->freightService->calculateFreight($discounted_subtotal);
            $total = $discounted_subtotal + $freight;
            $coupon = $this->cart->getCoupon();
            $coupon_id = $coupon['id'] ?? null;

            $this->dbConnection->beginTransaction();

            try {
                $orderId = $this->orderModel->create(
                    $subtotal,
                    $freight,
                    $total,
                    $cep,
                    $address['logradouro'],
                    $number,
                    $complement,
                    $address['bairro'],
                    $address['localidade'],
                    $address['uf'],
                    $customerEmail,
                    $coupon_id
                );

                if (!$orderId) {
                    throw new \Exception("Erro ao criar o pedido.");
                }

                foreach ($cartItems as $itemKey => $item) {
                    $stockUpdated = $this->stockModel->decreaseStock($item['stock_id'], $item['quantity']);
                    if (!$stockUpdated) {
                        throw new \Exception("Estoque insuficiente para o item: " . $item['product_name'] . " (" . $item['variation_name'] . "). Por favor, ajuste a quantidade.");
                    }
                    $this->orderModel->addOrderItem($orderId, $item['product_id'], $item['stock_id'], $item['quantity'], $item['price']);
                }

                $this->dbConnection->commit();

                $this->cart->clear();

                $orderDataForEmail = $this->orderModel->find($orderId);
                $orderItemsForEmail = $this->orderModel->getOrderItems($orderId);
                $this->emailService->sendOrderConfirmationEmail($customerEmail, $orderDataForEmail, $orderItemsForEmail, MSG_SEND_CONFIRMATION_EMAIL_CREATED_SUCCESSFULLY);


                $_SESSION['message'] = ['type' => 'success', 'text' =>MSG_ORDER_PLACEMENT_SUCCESS . $orderId];
                header('Location: /');
                exit();

            } catch (\Exception $e) {
                $this->dbConnection->rollBack();
                $_SESSION['message'] = ['type' => 'error', 'text' => MSG_ORDER_PLACEMENT_ERROR . $e->getMessage()];
                $_SESSION['address_data'] = $_POST;
                header('Location: /cart/checkout');
                exit();
            }
        }
    }

    public function applyCoupon()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $couponCode = $_POST['coupon_code'] ?? '';

            if (empty($couponCode)) {
                $this->cart->removeCoupon();
                $_SESSION['message'] = ['type' => 'info', 'text' => MSG_COUPON_REMOVED];
                header('Location: /cart/view');
                exit();
            }

            $coupon = $this->couponModel->findByCode($couponCode);
            if (!$coupon) {
                $_SESSION['message'] = ['type' => 'error', 'text' => MSG_INVALID_COUPON];
                header('Location: /cart/view');
                exit();
            }

            $subtotalBeforeDiscount = $this->cart->getTotal();
            if (!$this->couponModel->isValid($coupon, $subtotalBeforeDiscount)) {
                $_SESSION['message'] = ['type' => 'error', 'text' => MSG_INVALID_COUPON_OR_EXPIRED];
                header('Location: /cart/view');
                exit();
            }

            $this->cart->setCoupon($coupon);
            $_SESSION['message'] = ['type' => 'success', 'text' => 'Cupom "' . htmlspecialchars($couponCode) . '" aplicado com sucesso!'];
            header('Location: /cart/view');
            exit();
        }
    }
}
