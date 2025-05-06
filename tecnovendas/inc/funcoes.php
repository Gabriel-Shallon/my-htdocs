<?php

session_start();
    //funcao conexao com banco de dados
    function conecta(){
        $pdo = new PDO('mysql:dbname=tecnovendas;charset=utf8','root','');
        //com xampp - não usa semha
        //se não usa xampp - senha 'root'
        $pdo -> setattribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
        //Ativar no PDO o modo de tratar erros ao conectar no banco

        return $pdo;
    }//conecta

    function cadastrar_usuario($nome,$tipo,$login,$senha){
        $pdo = conecta();
        $query = $pdo->prepare('INSERT INTO usuario(nomeus, tipo, login, senha) VALUES (:nome,:tipo,:login,:senha)');

        $query->bindvalue(':nome', $nome);
        $query->bindvalue(':tipo', $tipo);
        $query->bindvalue(':login', $login);
        $query->bindvalue(':senha', $senha);

        $query->execute();

        return "Cadastrado com sucesso";
    }

    function logar($login,$senha){
        $pdo = conecta();
        $query = $pdo->prepare('SELECT * FROM usuario WHERE login = :x AND senha = :y');
        $query->bindvalue(':x', $login);
        $query->bindvalue(':y', $senha);
        $query->execute();

        if($query->rowcount()<=0){
            return 'Falha ao logar!';
        }//verifica quantas linhas a query rodou, se retornar 0, a query não encontrou nada
        else{
            session_start();
            $usuario = $query->fetch(PDO::FETCH_ASSOC);
            $_SESSION['us']=$usuario['nomeus'];
            header('Location:main.php');
            $_SESSION['id']=$usuario['idus'];//id para caso fossemos implementar a venda
        }

        exit();
    }//logar

    function busca($descricao){
        $pdo = conecta();
        $query = $pdo->prepare('SELECT * FROM produto WHERE nomepro LIKE :x');
        $query->bindvalue(':x', $descricao.'%');
        $query->execute();
        $produtos = $query->fetchAll(PDO::FETCH_ASSOC);
        return $produtos;
    }

?>