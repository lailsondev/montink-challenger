<?php require __DIR__ . '/../layout/header.php'; ?>

<h2><?php echo isset($product) ? 'Editar Produto' : 'Adicionar Novo Produto'; ?></h2>

<form action="<?php echo isset($product) ? '/products/update' : '/products/store'; ?>" method="POST">
    <?php if (isset($product)): ?>
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($product['id']); ?>">
    <?php endif; ?>

    <div class="form-group">
        <label for="name">Nome do Produto:</label>
        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($product['nome'] ?? ''); ?>" required>
    </div>

    <!-- O campo de preço do produto foi removido daqui, pois o preço agora é por variação -->

    <h3>Variações e Estoque:</h3>
    <div id="variations-container">
        <?php if (isset($product) && !empty($product['variacoes'])): ?>
            <?php foreach ($product['variacoes'] as $index => $variation): ?>
                <div class="variation-item" data-id="<?php echo htmlspecialchars($variation['id']); ?>">
                    <input type="hidden" name="variations[<?php echo $index; ?>][id]" value="<?php echo htmlspecialchars($variation['id']); ?>">
                    <div class="form-group">
                        <label for="variation_name_<?php echo $index; ?>">Nome da Variação:</label>
                        <input type="text" id="variation_name_<?php echo $index; ?>" name="variations[<?php echo $index; ?>][name]" value="<?php echo htmlspecialchars($variation['nome']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="variation_quantity_<?php echo $index; ?>">Quantidade:</label>
                        <input type="number" id="variation_quantity_<?php echo $index; ?>" name="variations[<?php echo $index; ?>][quantity]" min="0" value="<?php echo htmlspecialchars($variation['quantidade']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="variation_price_<?php echo $index; ?>">Preço da Variação:</label>
                        <input type="number" id="variation_price_<?php echo $index; ?>" name="variations[<?php echo $index; ?>][price]" step="0.01" min="0.01" value="<?php echo htmlspecialchars($variation['preco']); ?>" required>
                    </div>
                    <button type="button" class="btn btn-danger remove-variation">Remover</button>
                    <hr>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    <button type="button" id="add-variation" class="btn btn-success">Adicionar Variação</button>
    <input type="hidden" id="deleted-variations" name="deleted_variations" value="">

    <br><br>
    <button type="submit" class="btn btn-primary"><?php echo isset($product) ? 'Atualizar Produto' : 'Salvar Produto'; ?></button>
    <a href="/products" class="btn">Cancelar</a>
</form>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        let variationIndex = <?php echo isset($product) && !empty($product['variacoes']) ? count($product['variacoes']) : 0; ?>;
        const variationsContainer = document.getElementById('variations-container');
        const addVariationBtn = document.getElementById('add-variation');
        const deletedVariationsInput = document.getElementById('deleted-variations');
        let deletedVariationIds = [];

        addVariationBtn.addEventListener('click', function() {
            const newVariationHtml = `
                <div class="variation-item new-variation">
                    <div class="form-group">
                        <label for="variation_name_${variationIndex}">Nome da Variação:</label>
                        <input type="text" id="variation_name_${variationIndex}" name="variations[${variationIndex}][name]" required>
                    </div>
                    <div class="form-group">
                        <label for="variation_quantity_${variationIndex}">Quantidade:</label>
                        <input type="number" id="variation_quantity_${variationIndex}" name="variations[${variationIndex}][quantity]" min="0" value="0" required>
                    </div>
                    <div class="form-group">
                        <label for="variation_price_${variationIndex}">Preço da Variação:</label>
                        <input type="number" id="variation_price_${variationIndex}" name="variations[${variationIndex}][price]" step="0.01" min="0.01" value="0.01" required>
                    </div>
                    <button type="button" class="btn btn-danger remove-variation">Remover</button>
                    <hr>
                </div>
            `;
            variationsContainer.insertAdjacentHTML('beforeend', newVariationHtml);
            variationIndex++;
        });

        variationsContainer.addEventListener('click', function(event) {
            if (event.target.classList.contains('remove-variation')) {
                const variationItem = event.target.closest('.variation-item');
                if (variationItem) {
                    const variationId = variationItem.dataset.id;
                    if (variationId) {
                        deletedVariationIds.push(variationId);
                        deletedVariationsInput.value = deletedVariationIds.join(',');
                    }
                    variationItem.remove();
                }
            }
        });
    });
</script>

<?php require __DIR__ . '/../layout/footer.php'; ?>
