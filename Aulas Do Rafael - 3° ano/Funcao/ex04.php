<?php

    $status = 'Desligada';
    $canal = 0.0;
    $volume = 25;

    function liga(){
        global $status;
        $status = 'Ligada';
    }
    function desliga(){
        global $status;
        $status = 'Desligada';
    }

    function trocaCanal($numero){
        global $canal;
        $canal = $numero;
    }

    function aumentaVol(){
        global $volume;
        $volume++;
    }

    function diminuiVol(){
        global $volume;
        $volume--;
    }

    function mute(){
        global $volume;
        $volume = 0;
    }

    function aumentaCanal(){
        global $canal;
        $canal++;
    }

    function diminuiCanal(){
        global $canal;
        $canal--;
    }

    function config(){
        global $status;
        global $canal;
        global $volume;
        echo '<br>Status da tv: '.$status;
        echo '<br>Canal da tv: '.$canal;
        echo '<br>Volume da tv: '.$volume;
    }

?>