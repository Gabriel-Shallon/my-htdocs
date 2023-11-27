<?php
require_once 'api/Transaction.php';
require_once 'api/Connection.php';
require_once 'api/Criteria.php';
require_once 'api/Repository.php';
require_once 'api/Record.php';
require_once 'api/Logger.php';
require_once 'api/LoggerTXT.php';
require_once 'model/Produto.php';
 

    Transaction::open('arquivo');
    Transaction::setLogger(new LoggerTXT('tmp/log_exibir.txt'));
    Transaction::log('Exibindo os produtos');

    $produto1 = Produto::find($_GET['id']);
    if ($produto1)


    $repository1 = new Repository("Produto");
    $produtos = $repository->load;

    foreach ($produtos as $produto){
        ?>

        <tr>
            <td><?= $produto->id ?></td>
            <td><?= $produto->descricao ?></td>
            <td><?= $produto->quantidade ?></td>
            <td><?= $produto->preco ?></td>
            <td><?= $produto->marca ?></td>
            <td><a href="form.php?id<?=$produto->id ?>"><img src="img/edit.php">
            <td><a href="form.php?id<?=$produto->id ?>"><img src="img/del.php">
        <?
    };
