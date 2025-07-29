<?php require __DIR__ . '/../layout/header.php'; ?>

    <h2>Seu Carrinho de Compras</h2>

<?php if (empty($cart_items)): ?>
    <p>Seu carrinho está vazio. <a href="/products">Adicione alguns produtos!</a></p>
<?php else: ?>
    <table>
        <thead>
        <tr>
            <th>Produto</th>
            <th>Variação</th>
            <th>Preço Unitário</th>
            <th>Quantidade</th>
            <th>Subtotal</th>
            <th>Ações</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($cart_items as $item_key => $item): ?>
            <tr>
                <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                <td><?php echo htmlspecialchars($item['variation_name']); ?></td>
                <td>R$ <?php echo number_format($item['price'], 2, ',', '.'); ?></td>
                <td>
                    <form action="/cart/update" method="POST" style="display:inline-flex; align-items:center;">
                        <input type="hidden" name="item_key" value="<?php echo htmlspecialchars($item_key); ?>">
                        <input type="number" name="quantity" value="<?php echo htmlspecialchars($item['quantity']); ?>" min="1" style="width: 60px;">
                        <button type="submit" class="btn btn-primary" style="margin-left: 5px;">Atualizar</button>
                    </form>
                </td>
                <td>R$ <?php echo number_format($item['price'] * $item['quantity'], 2, ',', '.'); ?></td>
                <td>
                    <form action="/cart/remove" method="POST" style="display:inline-block;">
                        <input type="hidden" name="item_key" value="<?php echo htmlspecialchars($item_key); ?>">
                        <button type="submit" class="btn btn-danger">Remover</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <div class="cart-summary">
        <div><span>Subtotal dos Produtos:</span> <span>R$ <?php echo number_format($subtotal, 2, ',', '.'); ?></span></div>
        <?php if ($coupon): ?>
            <div><span>Cupom Aplicado (<?php echo htmlspecialchars($coupon['codigo']); ?>):</span> <span>- R$ <?php echo number_format($subtotal - $discounted_subtotal, 2, ',', '.'); ?></span></div>
        <?php endif; ?>
        <div><span>Subtotal com Desconto:</span> <span>R$ <?php echo number_format($discounted_subtotal, 2, ',', '.'); ?></span></div>
        <div><span>Frete:</span> <span>R$ <?php echo number_format($freight, 2, ',', '.'); ?></span></div>
        <h3>Total do Pedido: R$ <?php echo number_format($total, 2, ',', '.'); ?></h3>
    </div>

    <h3>Aplicar Cupom:</h3>
    <form action="/cart/apply-coupon" method="POST">
        <div class="form-group" style="display: flex;">
            <input type="text" name="coupon_code" placeholder="Digite o código do cupom" value="<?php echo htmlspecialchars($coupon['codigo'] ?? ''); ?>" style="flex-grow: 1; margin-right: 10px;">
            <button type="submit" class="btn btn-primary">Aplicar Cupom</button>
            <?php if ($coupon): ?>
                <button type="submit" name="coupon_code" value="" class="btn btn-danger" style="margin-left: 10px;">Remover Cupom</button>
            <?php endif; ?>
        </div>
    </form>


    <hr>

    <h3>Informações de Entrega:</h3>
    <form action="/cart/process-checkout" method="POST">
        <div class="form-group">
            <label for="email">E-mail:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($address_data['email'] ?? ''); ?>" required>
        </div>
        <div class="form-group">
            <label for="cep">CEP:</label>
            <input type="text" id="cep" name="cep" placeholder="Ex: 00000-000" value="<?php echo htmlspecialchars($address_data['cep'] ?? ''); ?>" required maxlength="9">
            <button type="button" id="lookup-cep" class="btn">Buscar CEP</button>
        </div>
        <div class="form-group">
            <label for="address">Endereço:</label>
            <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($address_data['logradouro'] ?? ''); ?>" readonly required>
        </div>
        <div class="form-group">
            <label for="number">Número:</label>
            <input type="text" id="number" name="number" value="<?php echo htmlspecialchars($address_data['number'] ?? ''); ?>" required>
        </div>
        <div class="form-group">
            <label for="complement">Complemento (Opcional):</label>
            <input type="text" id="complement" name="complement" value="<?php echo htmlspecialchars($address_data['complement'] ?? ''); ?>">
        </div>
        <div class="form-group">
            <label for="neighborhood">Bairro:</label>
            <input type="text" id="neighborhood" name="neighborhood" value="<?php echo htmlspecialchars($address_data['bairro'] ?? ''); ?>" readonly required>
        </div>
        <div class="form-group">
            <label for="city">Cidade:</label>
            <input type="text" id="city" name="city" value="<?php echo htmlspecialchars($address_data['localidade'] ?? ''); ?>" readonly required>
        </div>
        <div class="form-group">
            <label for="state">Estado:</label>
            <input type="text" id="state" name="state" value="<?php echo htmlspecialchars($address_data['uf'] ?? ''); ?>" readonly required maxlength="2">
        </div>

        <button type="submit" class="btn btn-success">Finalizar Pedido</button>
    </form>

    <script>
        document.getElementById('lookup-cep').addEventListener('click', function() {
            const cep = document.getElementById('cep').value.replace(/\D/g, '');
            if (cep.length === 8) {
                fetch(`https://viacep.com.br/ws/${cep}/json/`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.erro) {
                            alert('CEP não encontrado.');
                            document.getElementById('address').value = '';
                            document.getElementById('neighborhood').value = '';
                            document.getElementById('city').value = '';
                            document.getElementById('state').value = '';
                        } else {
                            document.getElementById('address').value = data.logradouro;
                            document.getElementById('neighborhood').value = data.bairro;
                            document.getElementById('city').value = data.localidade;
                            document.getElementById('state').value = data.uf;
                        }
                    })
                    .catch(error => {
                        console.error('Erro ao buscar CEP:', error);
                        alert('Erro ao buscar CEP. Tente novamente.');
                    });
            } else {
                alert('Por favor, digite um CEP válido com 8 dígitos.');
            }
        });
    </script>

<?php endif; ?>

<?php require __DIR__ . '/../layout/footer.php'; ?>