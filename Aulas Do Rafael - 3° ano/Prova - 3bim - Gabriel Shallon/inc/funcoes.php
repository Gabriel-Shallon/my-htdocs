<?php
    session_start();
    $alertas = empty($_SESSION['alert']) ? $alertas=array() : $_SESSION['alert'];

    function cadastro($setor){
        global $alertas;

        array_push($alertas, array(
            'setor' => $setor,
            'tempo' => date('j/m/y - H:i:s')
        ));

        $_SESSION['alert'] = $alertas;
    }

    function recuperarAlertas(){
        global $alertas;
        return $alertas;
    }



?>