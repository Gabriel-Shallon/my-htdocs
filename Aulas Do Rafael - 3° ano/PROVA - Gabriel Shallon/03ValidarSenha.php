<?php

    $senha = 'Juan Carlos Silva'; //Ele tem um nome normal agora :D (tinha, mas tem um 7 no meio agora D,: 
    $flag = false;
    
    if(strlen($senha)>8){
        echo "Senha Muito longa.<br>";
        $senha = substr($senha, 0, 8);
        $flag = true;
    }

    
    // echo substr($senha, (strlen($senha))/2, -(strlen($senha))/2);
    // echo substr($senha, (strlen($senha))/2, -(strlen($senha)-1)/2);

if (strlen($senha)%2 == 1){
    if(substr($senha, (strlen($senha))/2, -(strlen($senha))/2) != '7'){
        echo "Senha deve conter um 7 no meio (case1).<br>";
        $senha = str_replace(substr($senha, (strlen($senha))/2, -(strlen($senha))/2), '7', $senha);
        $flag = true;
    }
}



if (strlen($senha)%2 == 0){
    if(substr($senha, (strlen($senha))/2, -(strlen($senha)-1)/2) != '7'){
        echo "Senha deve conter um 7 no meio (case2).<br>";
        $senha = str_ireplace(substr($senha, (strlen($senha))/2, -(strlen($senha)-1)/2), '7', $senha);
        $flag = true;
    }
}

if ($flag == true){
    echo '<br>Nova senha = '.$senha;
}else{
    echo '<br>Senha ['.$senha.'] está nos padrões corretos';
}

?>