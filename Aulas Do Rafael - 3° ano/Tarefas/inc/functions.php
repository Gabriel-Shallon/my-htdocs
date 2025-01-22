<?php

    session_start();
    $tarefas = empty( $_SESSION["tarefas"] ) ? array() : $_SESSION["tarefas"];
    $ids = empty( $_SESSION["ids"] ) ? 0 : $_SESSION['ids'];


    function cadastrar($fazer, $prioridade){
        global $tarefas;
        global $ids;
        array_push($tarefas,array(
            'id'=> $ids,
            'fazer'=> $fazer,
            'data'=> date('j/m/y'),
            'prioridade'=> $prioridade
        ));
        $_SESSION['tarefas'] = $tarefas;
        $ids++;
        $_SESSION['ids'] = $ids;
    }//cadastrar


    function listar(){
        global $tarefas;
        return $tarefas;
    }//listar


    function remover($id){
        global $tarefas;
        $pos = array_search($id, array_column($tarefas,'id'));
        
        if ($pos>=0)
            array_splice($tarefas, $pos, 1);

            $_SESSION['tarefas'] = $tarefas;
    }//remover


    function limpar(){
        session_destroy();
    }//limpar

?>