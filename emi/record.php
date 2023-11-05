<?php
    require_once 'api/Transaction.php';
    require_once 'api/Connection.php';
    require_once 'api/Logger.php';
    require_once 'api/LoggerTXT.php';
    require_once 'api/LoggerXML.php';
    require_once 'api/Record.php';
    require_once 'model/Produto.php';

    try
    {
        Transaction::open('arquivo');
        Transaction::setLogger(new LoggerXML('tmp/log_excluir.xml'));
        Transaction::log('Excluir um produto.');

        $produto1 = Produto::find(12);
        if ($produto1 instanceof Produto)
        {
            $produto1->delete();
        }
        else
        {
            throw new Exception("Produto não localizado.");
        }

        Transaction::close();
    }
    catch(Exception $e) // PDO
    {
        Transaction::rollback();
        print $e->getMessage();
    }