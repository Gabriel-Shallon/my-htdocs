<?php
require_once 'api/Transaction.php';
require_once 'api/Connection.php';
require_once 'api/Criteria.php';
require_once 'api/Repository.php';
require_once 'api/Record.php';
require_once 'api/Logger.php';
require_once 'api/LoggerTXT.php';
require_once 'model/Produto.php';

try
{
    Transaction::open('arquivo');
    Transaction::setLogger(new LoggerTXT('tmp/log_collection_get.txt'));
    
    $criteria = new Criteria;
    $criteria->add('estoque', '>', 10);
    $criteria->add('origem',  '=', 'N');
    
    $repository = new Repository('Produto');
    $produtos = $repository->load( $criteria );
    if ($produtos)
    {
        foreach ($produtos as $produto)
        {
            print 'ID: ' . $produto->id;
            print ' - Descricao: ' . $produto->descricao;
            print ' - Estoque: ' . $produto->estoque;
            print '<br>';
        }
    }
    
    print 'Qtde: ' . $repository->count( $criteria );
    
    Transaction::close();
}
catch (Exception $e)
{
    print $e->getMessage();
    Transaction::rollback();
}