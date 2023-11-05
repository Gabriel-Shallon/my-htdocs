<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $comoComeBanana = $_POST['comoComeBanana'];

    if ($comoComeBanana === 'Garfo e faca' || $comoComeBanana === 'Tirando os pedaços') {
        header("Location: pergunta_roupa_intima.php?nome=$nome");
    } elseif ($comoComeBanana === 'Da Casca') {
        header("Location: pergunta_posicao_come_banana.php?nome=$nome");
    } else {
        header("Location: resultado.php?nome=$nome&resultado=Resultado inválido");
    }
    exit();
}
?>
