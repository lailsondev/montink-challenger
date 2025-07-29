<?php require __DIR__ . '/../layout/header.php'; ?>

    <h2>Criar Novo Cupom</h2>

    <form action="/coupons/store" method="POST">
        <div class="form-group">
            <label for="code">Código do Cupom:</label>
            <input type="text" id="code" name="code" required>
        </div>

        <div class="form-group">
            <label for="type">Tipo:</label>
            <select id="type" name="type" required>
                <option value="percentual">Percentual (%)</option>
                <option value="fixo">Valor Fixo (R$)</option>
            </select>
        </div>

        <div class="form-group">
            <label for="value">Valor:</label>
            <input type="number" id="value" name="value" step="0.01" min="0.01" required>
        </div>

        <div class="form-group">
            <label for="valid_from">Válido de:</label>
            <input type="datetime-local" id="valid_from" name="valid_from" required>
        </div>

        <div class="form-group">
            <label for="valid_to">Válido até:</label>
            <input type="datetime-local" id="valid_to" name="valid_to" required>
        </div>

        <div class="form-group">
            <label for="min_value">Valor Mínimo do Carrinho (Opcional):</label>
            <input type="number" id="min_value" name="min_value" step="0.01" min="0">
        </div>

        <button type="submit" class="btn btn-primary">Salvar Cupom</button>
        <a href="/coupons" class="btn">Cancelar</a>
    </form>

<?php require __DIR__ . '/../layout/footer.php'; ?>