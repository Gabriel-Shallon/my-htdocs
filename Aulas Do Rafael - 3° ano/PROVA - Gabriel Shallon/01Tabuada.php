<?php

    $input = 2;

    for ($i = 0, $flag = 0; $i <= 10; $i++, $flag += $input){

        if ($flag%3 != 0 || $flag%4 != 0 )
            echo $flag.'<br>';

    }

/*

    echo '<br>';

    $i = 0;
    $flag = 0;
    while ($i <= 10){
        $i++;
        $flag += $input;
        if ($flag%3 != 0 || $flag%4 != 0 )
            echo $flag.'<br>';

    }

*/

?>