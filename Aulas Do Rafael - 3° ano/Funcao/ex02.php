<?php

    function ola(){
        echo 'AAAAAAAAAAAAAAAAAA';
    }

    ola();


    function aReturn(){
        return '<br>Ola Jorge De La Cleive';
    }

    $ola = aReturn();
    echo $ola;

    function nome($nome){
        echo '<br>Ola '.$nome;
    }

    nome('JUORGEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEE');

    
    function nomegay($nome, $nome1){
        $gay = '<br>'.$nome.' é muito gay, mas o '.$nome1.' é mais';
        return $gay;
    }

    $jonatahn = nomegay('Bruno', 'Luiz');
    echo $jonatahn;
    

?>