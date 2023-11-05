<?php
include 'conexao.php';
include 'funcoes.php';
include 'logger.php';


if ($_SERVER["REQUEST_METHOD"] == "POST"){
$artista = $_POST['artista'];
$album = $_POST['album'];
$gravadora = $_POST['gravadora'];

$artista = ajustarConsulta($artista);
$album = ajustarConsulta($album);
$gravadora = ajustarConsulta($gravadora);


$sql = "SELECT * FROM musicas WHERE artista $artista AND album $album AND gravadora $gravadora";
$resultados = executarConsulta($sql);

registrarLog($sql, $resultados);

if ($resultados){
    echo "<h2>Resultados da Busca:</h2>";
    echo "<ul>";
    foreach ($resultados as $resultado) {
        echo "<li>" . $resultado['titulo'] . " - " . $resultados['artista'] . "</li>";
    }
    echo "</ul>";
} else {
    echo "Nenhum resultado encontrado.";
}

}
