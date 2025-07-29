<?php require __DIR__ . '/../layout/header.php'; ?>

<h2>Lista de Produtos</h2>

<a href="/products/create" class="btn btn-success">Adicionar Novo Produto</a>

<?php if (empty($products)): ?>
    <p>Nenhum produto cadastrado ainda.</p>
<?php else: ?>
    <table>
        <thead>
        <tr>
            <th>ID</th>
            <th>Nome</th>
            <th>Variações (Estoque e Preço)</th>
            <th>Ações</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($products as $product): ?>
            <tr>
                <td><?php echo htmlspecialchars($product['id']); ?></td>
                <td><?php echo htmlspecialchars($product['nome']); ?></td>
                <td>
                    <?php if (!empty($product['variacoes'])): ?>
                        <ul>
                            <?php foreach ($product['variacoes'] as $variation): ?>
                                <li>
                                    <?php echo htmlspecialchars($variation['nome']); ?>:
                                    <?php echo htmlspecialchars($variation['quantidade']); ?> em estoque
                                    (R$ <?php echo number_format($variation['preco'], 2, ',', '.'); ?>)
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        N/A
                    <?php endif; ?>
                </td>
                <td>
                    <a href="/products/edit?id=<?php echo htmlspecialchars($product['id']); ?>" class="btn btn-warning">Editar</a>
                    <form action="/cart/add" method="POST" style="display:inline-block; margin-left: 5px;">
                        <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($product['id']); ?>">
                        <?php if (!empty($product['variacoes'])): ?>
                            <select name="stock_id" required>
                                <?php foreach ($product['variacoes'] as $variation): ?>
                                    <?php if ($variation['quantidade'] > 0): ?>
                                        <option value="<?php echo htmlspecialchars($variation['id']); ?>">
                                            <?php echo htmlspecialchars($variation['nome']); ?>
                                            (R$ <?php echo number_format($variation['preco'], 2, ',', '.'); ?>)
                                            - <?php echo htmlspecialchars($variation['quantidade']); ?> em estoque
                                        </option>
                                    <?php else: ?>
                                        <option value="<?php echo htmlspecialchars($variation['id']); ?>" disabled>
                                            <?php echo htmlspecialchars($variation['nome']); ?>
                                            (R$ <?php echo number_format($variation['preco'], 2, ',', '.'); ?>)
                                            - Esgotado
                                        </option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </select>
                        <?php else: ?>
                            <input type="hidden" name="stock_id" value="0"> <!-- Placeholder for products without variations -->
                        <?php endif; ?>
                        <input type="number" name="quantity" value="1" min="1" style="width: 60px;">
                        <button type="submit" class="btn btn-primary">Comprar</button>
                    </form>
                    <form action="/products/delete" method="POST" style="display:inline-block; margin-left: 5px;" onsubmit="return confirm('Tem certeza que deseja excluir este produto e todas as suas variações? Esta ação é irreversível.');">
                        <input type="hidden" name="id" value="<?php echo htmlspecialchars($product['id']); ?>">
                        <button type="submit" class="btn btn-danger">Excluir</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php require __DIR__ . '/../layout/footer.php'; ?>
