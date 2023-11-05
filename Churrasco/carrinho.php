<?php
require_once('App.php');
require_once('Espetinho.php');

$precos = ['carne' => 5.00, 'frango' => 4.00];

$app = new App();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $carrinho = $_POST['espetinho'];
    $totalCompra = 0;

    // Adicione os itens selecionados ao carrinho e calcule o total
    foreach ($carrinho as $nome => $quantidade) {
        if ($quantidade > 0 && isset($precos[$nome])) {
            $totalCompra += $precos[$nome] * $quantidade;
            $espetinho = new Espetinho($nome, $precos[$nome], $quantidade);
            $app->adicionarAoCarrinho($espetinho, $quantidade);
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Churrascaria - Carrinho de Compras</title>
</head>
<body>
    <h1>Carrinho de Compras</h1>
    <table>
        <tr>
            <th>Nome</th>
            <th>Quantidade</th>
            <th>Preço Unitário</th>
            <th>Subtotal</th>
        </tr>
        <?php foreach ($app->getCarrinho() as $item): ?>
        <tr>
            <td><?php echo $item['espetinho']->getNome(); ?></td>
            <td><?php echo $item['quantidade']; ?></td>
            <td>R$ <?php echo number_format($item['espetinho']->getPreco(), 2); ?></td>
            <td>R$ <?php echo number_format($item['espetinho']->getPreco() * $item['quantidade'], 2); ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
    <p>Total: R$ <?php echo number_format($totalCompra, 2); ?></p>
    <form action="pagamento.php" method="POST">
        <label for="metodoPagamento">Método de Pagamento:</label>
        <select name="metodoPagamento" id="metodoPagamento">
            <option value="dinheiro">Dinheiro</option>
            <option value="cartao">Cartão de Crédito</option>
        </select>
        <input href="infoCompra.php" type="submit" value="Finalizar Compra">
        <a href="Index.html">Voltar para o índice</a>
    </form>
</body>
</html>
