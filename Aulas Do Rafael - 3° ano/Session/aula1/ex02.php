<?php

    session_start();
    echo $_SESSION['usuario'].'<br>';
    //mesmo as variáveis n sendo criadas aqui, elas foram salvas no servidor

    if(isset($_SESSION['usuario'])){
        //verifica se há alguma variável session com o nome especificado
        echo 'User logado.';
    }else
        echo 'User não logado.';

    echo '<br>';
    var_dump($_SESSION);
    //mostra tudo que está na session
    
    echo '<br>';
    echo session_id();
    //cada sesseion possui um ID, e usando session_id() podemos ve-la

    echo '<br>';
    echo session_save_path();
    //caminho para onde a session está armazenada no server

?>