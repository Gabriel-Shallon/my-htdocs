<?php 

require_once 'config.php';

function conectarBanco() {
    global $db_host, $db_user, $db_password, $db_name;

    $conn = new mysqli($db_host, $db_user, $db_user, $db_password, $db_name);

    if ($conn->connect_error) {
        die("Erro na comexão com o banco de dados: " . $conn->connect_error);
    }

    return $conn;
}

function executarConsulta($sql){
    $conn = conectarBanco();

    $resultados = $conn->query($sql);

    if (!$resultados) {
        echo "Erro na consulta: "
        $conn->close
    }
}