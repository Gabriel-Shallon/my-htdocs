<?php

    session_start();
    $registros = empty($_SESSION['reg']) ? $registros=array() : $_SESSION['reg'];
    // verifica se a variável reg existe. Caso n exista, cria um vetor vazio. Caso exista, pega os valores dela.

    function cadastrarEntrada($nome, $cpf){
        global $registros;
        
        array_push($registros, array(
            'nome'=>$nome,
            'cpf'=>$cpf,
            'data'=>date('j/m/y - H:i:s')
        ));
        $_SESSION['reg'] = $registros;

        echo 'Cadastrado!<br>';

    }//cadastrar entrada


    function buscaRegistro(){
        global $registros;
        return $registros;
    }//muda o tipo de acesso da variável e retorna ela


    function registrarSaida($cpf){
        global $registros;
        $posicao = array_search($cpf, array_column($registros, 'cpf'));
        //busca no vetor ($registros) qual posição tem o nome cpf, e registra a posição

        if($posicao>=0){
            
            unset($registros[$posicao]);
            // if($posicao != count($registros)){
            // $registros[$posicao]=$registros[$posicao+1];
            // }
            $_SESSION['reg'] = $registros;
        }
        
       echo "CPF $cpf removido.";
    }//registrar saida


    function limparDados(){
        session_destroy();
        echo 'Dados removidos com sucesso.';
    }

?>

