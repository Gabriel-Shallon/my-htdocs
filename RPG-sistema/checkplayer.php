<?php
    include 'inc/func.php';
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Lista de Personagens</title>
    <style>body{font-family:sans-serif;max-width:600px;margin:2em auto;} table{width:100%;border-collapse:collapse;} th,td{border:1px solid #ccc;padding:.5em;text-align:left;} </style>
</head>
<body>
    <h1>Personagens Existentes</h1>
    <?php
    // Busca todos os personagens
    $pdo = conecta();
    $stmt = $pdo->query('SELECT * FROM RPG.player');
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if($rows):
    ?>
    <table>
        <tr><th>Nome</th><th>F</th><th>H</th><th>R</th><th>A</th><th>PdF</th><th>PV</th><th>PM</th><th>PE</th></tr>
        <?php foreach($rows as $p): ?>
        <tr>
            <?php foreach($p as $v): ?><td><?=htmlspecialchars($v)?></td><?php endforeach; ?>
        </tr>
        <?php endforeach; ?>
    </table>
    <?php else: ?>
        <p>Nenhum personagem cadastrado.</p>
    <?php endif; ?>
</body>
</html>