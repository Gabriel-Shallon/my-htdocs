<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    header("Location: pergunta_como_come_banana.php?nome=$nome");
    exit();
}
?>
