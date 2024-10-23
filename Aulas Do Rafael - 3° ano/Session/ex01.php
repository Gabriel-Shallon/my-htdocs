<?php

    //iniciar uma sessão
    session_start();

    //criar uma variável na sessão
    $_SESSION['usuario'] = 'Rafael';
    $_SESSION['senha'] = '1234';

    //exibir variáveis session
    echo $_SESSION['usuario'].'<br>';
    echo $_SESSION['senha'];

?>