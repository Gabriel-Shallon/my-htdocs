<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Simular Combate</title>
    <style>body{font-family:sans-serif;max-width:600px;margin:2em auto;} form{border:1px solid #ccc;padding:1em;} label{display:block;margin:.5em 0;} input,select{width:100%;padding:.4em;} button{margin-top:1em;} .result{background:#efe;padding:.8em;margin-top:1em;} </style>
</head>
<body>
    <?php 
        include 'inc/func.php'; 
        $pdo = conecta();
    ?>
    <h1>Simular Combate</n1>
    <form method="post" action="battle.php">
        <label>Atacante: 
            <select name="atacante"><?php foreach($pdo->query('SELECT nome FROM RPG.player') as $row){ echo '<option>'.htmlspecialchars($row['nome']).'</option>'; } ?></select>
        </label>
        <label>Tipo de Ataque:
            <select name="atkType">
                <option value="F">Corpo a Corpo (F)</option>
                <option value="PdF">Distância (PdF)</option>
            </select>
        </label>
        <label>Dado FA (1-6): <input type="number" name="dadoFA" min="1" max="6" required></label>
        <label>Defensor: 
            <select name="defensor"><?php foreach($pdo->query('SELECT nome FROM RPG.player') as $row){ echo '<option>'.htmlspecialchars($row['nome']).'</option>'; } ?></select>
        </label>
        <label>Modo de Defesa:
            <select name="mode">
                <option value="normal">Normal (FD)</option>
                <option value="indefeso">Indefeso</option>
                <option value="esquiva">Esquiva (H)</option>
            </select>
        </label>
        <label>Dado Defesa/Esquiva (1-6): <input type="number" name="dadoD" min="1" max="6"></label>
        <button type="submit" name="run">Calcular</button>
        <a href="index.php">voltar</a>
    </form>
    <?php
    if(isset($_POST['run'])){
        $atk = $_POST['atacante'];
        $def = $_POST['defensor'];
        $atkType = $_POST['atkType'];
        $dFA = (int)$_POST['dadoFA'];
        $mode = $_POST['mode'];
        $dD  = (int)($_POST['dadoD'] ?? 0);
        switch($mode){
            case 'normal':
                $dmg = FAFDresult($atk,$def,$dFA,$dD,$atkType);
                $text = "Dano: $dmg (FA-FD normal)";
                break;
            case 'indefeso':
                $dmg = FAFDindefeso($atk,$def,$dFA,$atkType);
                $text = "Dano: $dmg (Defensor indefeso)";
                break;
            case 'esquiva':
                $dmg = FAFDesquiva($atk,$def,$dD,$dFA,$atkType);
                $text = "Dano: $dmg (Tentativa de esquiva)";
                break;
        }
        echo '<div class="result">'.htmlspecialchars($text).'</div>';
    }
    ?>
</body>
</html>
