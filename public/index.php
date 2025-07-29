<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/messages.php';

use Dotenv\Dotenv;
use App\Router;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

session_start();

$router = new Router();

// Rotas para Produtos
$router->addRoute('GET', '/', ['ProductController', 'index']);
$router->addRoute('GET', '/products', ['ProductController', 'index']);
$router->addRoute('GET', '/products/create', ['ProductController', 'create']);
$router->addRoute('POST', '/products/store', ['ProductController', 'store']);
$router->addRoute('GET', '/products/edit', ['ProductController', 'edit']);
$router->addRoute('POST', '/products/update', ['ProductController', 'update']);
$router->addRoute('POST', '/products/delete', ['ProductController', 'delete']);

// Rotas para Carrinho
$router->addRoute('POST', '/cart/add', ['CartController', 'add']);
$router->addRoute('GET', '/cart/view', ['CartController', 'view']);
$router->addRoute('POST', '/cart/update', ['CartController', 'update']);
$router->addRoute('POST', '/cart/remove', ['CartController', 'remove']);
$router->addRoute('GET', '/cart/checkout', ['CartController', 'checkout']);
$router->addRoute('POST', '/cart/process-checkout', ['CartController', 'processCheckout']);

// Rotas para Cupons
$router->addRoute('GET', '/coupons', ['CouponController', 'index']);
$router->addRoute('GET', '/coupons/create', ['CouponController', 'create']);
$router->addRoute('POST', '/coupons/store', ['CouponController', 'store']);
$router->addRoute('POST', '/cart/apply-coupon', ['CartController', 'applyCoupon']);

// Rota para Webhook
$router->addRoute('POST', '/webhook/order-status', ['WebhookController', 'handle']);

$router->dispatch();
