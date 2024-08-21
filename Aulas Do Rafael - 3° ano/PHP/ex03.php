<?php
    //interpolação

    $nome = 'Rafael';
    $sobrenome = 'garcia';
    var_dump($nome, $sobrenome);
    echo '<br>';
    echo "nome $nome";
    //mostra o valor da variavel
    echo '<br>';
    echo 'nome $nome';
    //o valor da variavel não é lido (lê como texto)
    echo '<br>';
    echo strtolower($nome);
    //deixa tudo minusculo
    echo '<br>';
    echo strtoupper($nome);
    //deixa tudo maiusculo
    echo '<br>';
    echo ucwords($sobrenome);
    //primeira letra maiuscula
    echo '<br>';
    echo str_replace ('r', 'c', $nome);
    //substitui a(s) parte(s) selecionada(s) de uma string pelo oque foi especificado (no caso, todas as letras r serão substituidas por c (letra minuscula e maiuscula tem diferença))
    $teste = 'Rafael Garcia Da Silva';
    echo str_replace(' ', '_', $teste);
?>