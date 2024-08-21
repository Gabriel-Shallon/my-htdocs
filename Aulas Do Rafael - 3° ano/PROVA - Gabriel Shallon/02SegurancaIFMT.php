<?php

    $alunoFullNome = 'Juan(como_juan_é_um_nome_de_apenas_4_caracteres,por_dever_de_minha_parte,acabo_de_extende-lo,ass=gabrielshallon) umnomeestranhotadinho Carlos Pereira Da Silva Junior Almeida De Azevedo Rocha Fernandes Albuquerque Segundo';
    $ComponentesDoNome = explode(' ', $alunoFullNome);
    
    $senha = str_replace (substr($ComponentesDoNome[0], -4), 'IFMT', $ComponentesDoNome[0]);
    
    echo 'Senha: '.$senha

?>