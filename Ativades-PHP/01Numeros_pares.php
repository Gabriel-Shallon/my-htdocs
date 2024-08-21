<?php 

    $input = 2013;
    if (($input%2)==1){
        $input-=1;
    }
    for ($i = 0; $i <= $input; $input-=2){
        echo $input.'<br>';
    }

?>