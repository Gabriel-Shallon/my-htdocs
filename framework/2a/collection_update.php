<?php
// carrega as classes necessárias 
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
    Transaction::setLogger(new LoggerTXT('tmp/log_collection_update.txt'));
    
    $criteria = new Criteria;
    $criteria->add('preco_venda', '<=', 35);
    $criteria->add('origem',      '=',  'N');
    
    $repository = new Repository('Produto');
    $produtos = $repository->load($criteria);
    
    if ($produtos)
    {
        foreach ($produtos as $produto)
        {
            $produto->preco_venda *= 1.3;
            $produto->store();
        }
    }
    
    
    Transaction::close();
}
catch (Exception $e)
{
    print $e->getMessage();
    Transaction::rollback();
}
