<?php

    $valor = 'um';
    switch ($valor){
        case 'um': case 'Um': case 'UM':
            echo 'digitou um';
        default:
            echo 'número inválido!';
    }

?>