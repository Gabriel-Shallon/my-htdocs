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
    Transaction::setLogger(new LoggerTXT('tmp/log_collection_delete.txt'));
    
    $criteria = new Criteria;
    $criteria->add('descricao', 'like', '%WEBC%');
    $criteria->add('descricao', 'like', '%FILMAD%', 'or');
    
    $repository = new Repository('Produto');
    $repository->delete($criteria);
    
    
    Transaction::close();
}
catch (Exception $e)
{
    print $e->getMessage();
    Transaction::rollback();
}
