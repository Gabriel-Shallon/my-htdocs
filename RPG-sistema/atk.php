<?php
// atk.php - Simula um único ataque entre dois personagens.
include 'inc/traitFuncs.php';

$pdo = conecta();
$allPlayers = getAllPlayers();
$allDamageTypes = getAllDmgTypes();
$resultText = '';

if (isset($_POST['run'])) {
    $atk = $_POST['atacante'];
    $def = $_POST['defensor'];
    $atkType = $_POST['atkType'];
    $dmgType = $_POST['dmgType'];
    $dFA = (int)$_POST['dadoFA'];
    $mode = $_POST['mode'];
    $dD  = (int)($_POST['dadoD'] ?? 0);

    // Usa H do atacante para cálculo de esquiva
    $H_atacante = (int)getPlayerStat($atk, 'H');

    switch ($mode) {
        case 'normal':
            $dmg = FAFDresult($atk, $def, $dFA, $dD, $atkType, $dmgType, $H_atacante);
            $text = "Dano: $dmg (FA vs FD normal)";
            break;
        case 'indefeso':
            $dmg = FAFDindefeso($atk, $def, $dFA, $atkType, $dmgType, $H_atacante);
            $text = "Dano: $dmg (Defensor indefeso)";
            break;
        case 'esquiva':
            $dmg = FAFDesquiva($atk, $def, $dD, $dFA, $atkType, $dmgType, $H_atacante);
            $text = ($dmg === 0) ? "Esquiva bem-sucedida! Dano: 0" : "Esquiva falhou! Dano: $dmg";
            break;
        default:
            $dmg = 0;
            $text = "Modo de defesa inválido.";
    }
    $resultText = htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Simular Ataque Rápido</title>
    <style>
        body { font-family: sans-serif; max-width: 600px; margin: 2em auto; }
        form { border: 1px solid #ccc; padding: 1em; }
        label { display: block; margin: .5em 0; }
        input, select { width: 100%; padding: .4em; box-sizing: border-box; }
        button { margin-top: 1em; padding: 0.5em 1em; }
        .result { background: #efe; padding: .8em; margin-top: 1em; border-left: 4px solid #4a4; }
        .nav-link { display: inline-block; margin-left: 1em; }
    </style>
</head>
<body>
    <h1>Simular Ataque Rápido</h1>
    <form method="post" action="atk.php">
        <label>Atacante:
            <select name="atacante" required>
                <?php foreach ($allPlayers as $p) { echo '<option>' . htmlspecialchars($p['nome']) . '</option>'; } ?>
            </select>
        </label>
        <label>Tipo de Ataque:
            <select name="atkType">
                <option value="F">Corpo a Corpo (F)</option>
                <option value="PdF">Distância (PdF)</option>
            </select>
        </label>
         <label>Tipo de Dano:
            <select name="dmgType" required>
                <?php foreach ($allDamageTypes as $type) { echo '<option>' . htmlspecialchars($type) . '</option>'; } ?>
            </select>
        </label>
        <label>Dado FA (1-6): <input type="number" name="dadoFA" min="1" max="6" required></label>
        <hr>
        <label>Defensor:
            <select name="defensor" required>
                <?php foreach ($allPlayers as $p) { echo '<option>' . htmlspecialchars($p['nome']) . '</option>'; } ?>
            </select>
        </label>
        <label>Modo de Defesa:
            <select name="mode" id="defenseMode">
                <option value="normal">Normal (FD)</option>
                <option value="indefeso">Indefeso</option>
                <option value="esquiva">Esquiva (H)</option>
            </select>
        </label>
        <label id="dadoDLabel">Dado Defesa/Esquiva (1-6): <input type="number" name="dadoD" min="1" max="6"></label>
        <button type="submit" name="run">Calcular</button>
        <a href="index.php" class="nav-link">Voltar</a>
    </form>

    <?php if (!empty($resultText)): ?>
        <div class="result"><?= $resultText ?></div>
    <?php endif; ?>

    <script>
        document.getElementById('defenseMode').addEventListener('change', function() {
            var label = document.getElementById('dadoDLabel');
            label.style.display = this.value === 'indefeso' ? 'none' : 'block';
            label.querySelector('input').required = this.value !== 'indefeso';
        });
        // Trigger change on load to set initial state
        document.getElementById('defenseMode').dispatchEvent(new Event('change'));
    </script>
</body>
</html>