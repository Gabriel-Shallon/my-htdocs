<?php
    include 'inc/func.php';
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Criar Personagem</title>
    <style>body{font-family:sans-serif;max-width:600px;margin:2em auto;} form{border:1px solid #ccc;padding:1em;} label{display:block;margin:.5em 0;} input{width:100%;padding:.4em;} button{margin-top:1em;} .msg{background:#eef;padding:.8em;} </style>
</head>
<body>
    <h1>Criar Novo Personagem</h1>
    <?php if(!empty($_GET['msg'])): ?><div class="msg"><?=htmlspecialchars($_GET['msg'])?></div><?php endif; ?>
    <form method="post" action="newplayer.php">
        <label>Nome: <input type="text" name="nome" required></label>
        <label>Força (F): <input type="number" name="F" required></label>
        <label>Habilidade (H): <input type="number" name="H" required></label>
        <label>Resistência (R): <input type="number" name="R" required></label>
        <label>Armadura (A): <input type="number" name="A" required></label>
        <label>Poder de Fogo (PdF): <input type="number" name="PdF" required></label>
        <label>Pontos de Vida (PV): <input type="number" name="PV" required></label>
        <label>Pontos de Magia (PM): <input type="number" name="PM" required></label>
        <label>Pontos de Experiência (PE): <input type="number" name="PE" required></label>
        <button type="submit" name="submit">Criar Personagem</button>
        <a href="index.php">voltar</a>
    </form>
    <?php
    if(isset($_POST['submit'])){
        newPlayer(
            $_POST['nome'],
            $_POST['F'],$_POST['H'],$_POST['R'],$_POST['A'],
            $_POST['PdF'],$_POST['PV'],$_POST['PM'],$_POST['PE']
        );
        header('Location: newplayer.php?msg=Personagem+'.urlencode($_POST['nome']).'+criado+com+sucesso');
        exit;
    }
    ?>
</body>
</html>