<?php

    require_once 'api/Connection.php';
    require_once 'api/Transaction.php';
    require_once 'api/Logger.php';
    require_once 'api/LoggerTXT.php';
    require_once 'model/Produto.php';
    require_once 'api/Record.php';


    try {
    Transaction::open('arquivo');
    Transaction::setLogger(new LoggerTXT('tmp/log_exibir.txt'));
    Transaction::log('Freitas');

    $produto1 = new Produto(3);
    $produto1->descricao = 'Mouse Logitech';
    $produto1->estoque = 1;
    $produto1->preco_custo = 10;
    $produto1->preco_venda = 94.99;
    $produto1->codigo_barras = '12345678';
    $produto1->origem = 'n';
    $produto1->store();

    Transaction::close();
    } 
    catch (Exception $e) 
    {
        Transaction::rollback();
        print 'Transação não ativa. {$e}';
    }