<?php
    require_once 'api/Connection.php';
    require_once 'api/Transaction.php';
    require_once 'api/Logger.php';
    require_once 'api/LoggerTXT.php';
    require_once 'api/Record.php';
    require_once 'model/Produto.php';

    try
    {
        Transaction::open('arquivo');
        Transaction::setLogger(new LoggerTXT('tmp/log_exibir.txt'));
        Transaction::log('Exibindo um produto');

        $produto1 = Produto::find(2);

        if ($produto1 instanceof Produto)
        {
            // print $produto1->descricao . '<br />';
            // print $produto1->estoque . '<br />';
            // print $produto1->preco_custo;
            $produto1->delete();
        }

        Transaction::close();
    }
    catch(Exception $e)
    {
        Transaction::rollback();
        print 'Transação não ativa. {$e}';
    }