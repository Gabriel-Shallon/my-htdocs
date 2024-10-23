<?php

    //faça um programa que em PHP que o usuário tenha apenas 20s de sessão para usar o sistema antes de ser bloqueado.
    session_start();

    $limite = 20; //tempo disponível
    $_SESSION['ult_acesso'] = time(); //momento que entrei no sistema
    //Após salvar, sempre que a página ser reiniciada ele resetará para o tempo no momento. Para salvar o tempo de inicio, deve-se inicia-lo e comenta-lo (resultados visíveis se reiniciarmos a página)

    if (isset($_SESSION['ult_acesso'])){

        $uso = time() - $_SESSION['ult_acesso'];
        //Tempo em que a session ficou aberto
        echo $uso;

        if (time() >= $_SESSION['ult_acesso']+$limite){

            echo 'Sessão finalizada.';
            session_destroy();
            //destrói a sessão após ela atingir o limite de tempo

        }

    }
?>