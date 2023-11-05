<?php
    
    require_once 'Conexao.class';

    if (isset($_GET['Nome'])){
    $nome = $_GET['Nome'];
    print "Nome: {$nome} <br/>";
    }
    else
    $nome = null;
    
    if (isset($_GET['Email'])){  
    $email = $_GET['Email'];
    print "E-mail: {$email}";
    }
    else
    $email = null;

    //$nome = $_GET['Nome'];
    //$email = $_GET['Email'];

    //print "Nome: {$nome} <br/> E-mail: {$email}"; 




