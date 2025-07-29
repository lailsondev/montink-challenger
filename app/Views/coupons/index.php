<?php require __DIR__ . '/../layout/header.php'; ?>

    <h2>Cupons Disponíveis</h2>

    <a href="/coupons/create" class="btn btn-success">Criar Novo Cupom</a>

<?php if (empty($coupons)): ?>
    <p>Nenhum cupom cadastrado ainda.</p>
<?php else: ?>
    <table>
        <thead>
        <tr>
            <th>ID</th>
            <th>Código</th>
            <th>Tipo</th>
            <th>Valor</th>
            <th>Válido de</th>
            <th>Válido até</th>
            <th>Valor Mínimo</th>
            <th>Ativo</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($coupons as $coupon): ?>
            <tr>
                <td><?php echo htmlspecialchars($coupon['id']); ?></td>
                <td><?php echo htmlspecialchars($coupon['codigo']); ?></td>
                <td><?php echo htmlspecialchars($coupon['tipo']); ?></td>
                <td><?php echo number_format($coupon['valor'], 2, ',', '.'); ?></td>
                <td><?php echo htmlspecialchars(date('d/m/Y H:i', strtotime($coupon['validade_inicio']))); ?></td>
                <td><?php echo htmlspecialchars(date('d/m/Y H:i', strtotime($coupon['validade_fim']))); ?></td>
                <td><?php echo $coupon['valor_minimo'] ? 'R$ ' . number_format($coupon['valor_minimo'], 2, ',', '.') : 'N/A'; ?></td>
                <td><?php echo $coupon['ativo'] ? 'Sim' : 'Não'; ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php require __DIR__ . '/../layout/footer.php'; ?>