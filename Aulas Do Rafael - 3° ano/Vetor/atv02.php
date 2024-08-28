<?php


    $carros = array('gol', 'saveiro', 'celta');

    foreach ($carros as $pos => $carro){
        if($carro == 'celta')
            $carros[$pos] = 'onix';
    }

    echo '<br>';
    echo '<pre>';
    print_r($carros);
    echo '<pre/>';

?>