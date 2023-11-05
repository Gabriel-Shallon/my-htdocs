<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Processar o pagamento e gerar informações da compra
    $metodoPagamento = $_POST['metodoPagamento'];
    $app->processarPagamento($metodoPagamento);

    // Obter informações da compra
    $compra = $app->getCompra();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Churrascaria - Compra Concluída</title>
</head>
<body>
    <h1>Compra Concluída</h1>
    <h2>Itens Comprados</h2>
    <ul>
        <?php foreach ($compra['itens'] as $item): ?>
            <li><?php echo $item['quantidade']; ?> x <?php echo $item['nome']; ?> (R$ <?php echo number_format($item['preco'], 2); ?> cada)</li>
        <?php endforeach; ?>
    </ul>
    <p>Total da Compra: R$ <?php echo number_format($compra['total'], 2); ?></p>
    <p>Método de Pagamento: <?php echo $compra['metodoPagamento']; ?></p>
    <a href="index.php">Voltar para o índice</a>
</body>
</html>
