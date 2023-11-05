<?php
require_once 'api/Connection.php';
require_once 'api/Transaction.php';
require_once 'model/Produto.php';

try {
    // Abra a transação
    Transaction::open('arquivo');

    // Verifique a ação
    if (isset($_POST['acao'])) {
        if ($_POST['acao'] === 'adicionar') {
            // Adicionar um novo produto
            $produto = new Produto();
            $produto->descricao = $_POST['descricao'];
            $produto->estoque = $_POST['estoque'];
            $produto->preco_custo = $_POST['preco_custo'];
            $produto->preco_venda = $_POST['preco_venda'];
            $produto->codigo_barras = $_POST['codigo_barras'];
            $produto->origem = $_POST['origem'];
            $produto->data_cadastro = date('Y-m-d');
            $produto->store();
        } elseif ($_POST['acao'] === 'atualizar') {
            // Atualizar o estoque de um produto existente
            $id_atualizar = $_POST['id_atualizar'];
            $estoque_atualizar = $_POST['estoque_atualizar'];

            $produto = Produto::find($id_atualizar);
            if ($produto) {
                $produto->estoque = $estoque_atualizar;
                $produto->store();
            }
        } elseif ($_POST['acao'] === 'remover') {
            // Remover um produto
            $id_remover = $_POST['id_remover'];
            $produto = Produto::find($id_remover);
            if ($produto) {
                $produto->delete();
            }
        }
    }

    // Feche a transação
    Transaction::close();

    // Redirecione de volta para a página principal
    header('Location: index.php');
} catch (Exception $e) {
    // Em caso de erro, faça o tratamento adequado
    echo 'Erro: ' . $e->getMessage();
    Transaction::rollback();
}
?>
