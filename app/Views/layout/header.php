<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Minha Loja Online</title>
    <link rel="stylesheet" href="/css/style.css">
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; background-color: #f4f4f4; }
        .container { width: 80%; margin: 20px auto; background-color: #fff; padding: 20px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        nav { background-color: #333; color: #fff; padding: 10px 20px; display: flex; justify-content: space-between; align-items: center; }
        nav a { color: #fff; text-decoration: none; margin-right: 15px; }
        .message { padding: 10px; margin-bottom: 15px; border-radius: 5px; }
        .message.success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .message.error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .message.info { background-color: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; }
        .btn { padding: 8px 15px; background-color: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; display: inline-block; }
        .btn-success { background-color: #28a745; }
        .btn-warning { background-color: #ffc107; }
        .btn-danger { background-color: #dc3545; }
        .btn:hover { opacity: 0.9; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        table, th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        form { margin-top: 20px; background-color: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); }
        form label { display: block; margin-bottom: 5px; font-weight: bold; }
        form input[type="text"],
        form input[type="number"],
        form input[type="email"],
        form input[type="datetime-local"],
        form select {
            width: calc(100% - 22px);
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        form button { padding: 10px 20px; background-color: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; }
        .form-group { margin-bottom: 15px; }
        .cart-summary { border-top: 1px solid #eee; padding-top: 15px; margin-top: 15px; }
        .cart-summary div { display: flex; justify-content: space-between; margin-bottom: 5px; }
    </style>
</head>
<body>
<nav>
    <a href="/">Loja Online</a>
    <div>
        <a href="/products">Produtos</a>
        <a href="/products/create">Cadastrar Produto</a>
        <a href="/coupons">Cupons</a>
        <a href="/coupons/create">Criar Cupom</a>
        <a href="/cart/view">Carrinho (<?php echo count($_SESSION['cart'] ?? []); ?>)</a>
    </div>
</nav>
<div class="container">
    <?php if (isset($_SESSION['message'])): ?>
    <div class="message <?php echo $_SESSION['message']['type']; ?>">
        <?php echo htmlspecialchars($_SESSION['message']['text']); ?>
    </div>
    <?php unset($_SESSION['message']); ?>
<?php endif; ?>