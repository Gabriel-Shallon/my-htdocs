<?php
// newplayer.php - Formulário completo para criar um novo personagem
include 'inc/generalFuncs.php';
include 'inc/traitFuncs.php';

$pdo = conecta();
$advantages = $pdo->query('SELECT id, name, effect_text FROM RPG.advantages ORDER BY name')->fetchAll(PDO::FETCH_ASSOC);
$disadvantages = $pdo->query('SELECT id, name, effect_text FROM RPG.disadvantages ORDER BY name')->fetchAll(PDO::FETCH_ASSOC);
$allPlayers = getAllPlayers();
$allDamageTypes = getAllDmgTypes();
$allVulnerabilities = getAllVulnerabilities();
$allInvulnerabilities = getAllInvulnerabilities();
$allExtraArmorTypes = getAllExtraArmorTypes();

// Processa a criação
if (isset($_POST['submit'])) {
    $nome = $_POST['nome'];
    newPlayer(
        $nome, (int)$_POST['F'], (int)$_POST['H'], (int)$_POST['R'],
        (int)$_POST['A'], (int)$_POST['PdF'], (int)$_POST['PE'],
        $_POST['inventario'] ?? '',
        $_POST['equipado']   ?? ''
    );

    // Associa vantagens, desvantagens (com contagem)
    foreach ($_POST['traits'] ?? [] as $key => $count) {
        if (($count = (int)$count) > 0) {
            list($type, $id) = explode(':', $key);
            for ($i = 0; $i < $count; $i++) addPlayerTrait($nome, (int)$id, $type);
        }
    }

    // Associa tipos de dano e modificadores
    foreach ($_POST['damage_types'] ?? [] as $name) addPlayerDmgType($nome, $name);
    foreach ($_POST['vulnerabilities'] ?? [] as $name) addPlayerVulnerability($nome, $name);
    foreach ($_POST['invulnerabilities'] ?? [] as $name) addPlayerInvulnerability($nome, $name);
    foreach ($_POST['extra_armors'] ?? [] as $name) addPlayerExtraArmor($nome, $name);
    
    // Associa aliados
    foreach ($_POST['allies'] ?? [] as $allyName) addPlayerAlly($nome, $allyName);
    
    header('Location: newplayer.php?msg=Personagem ' . urlencode($nome) . ' criado com sucesso!');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Criar Novo Personagem</title>
  <style>
    body{font-family:sans-serif;max-width:800px;margin:2em auto;} form{border:1px solid #ccc;padding:1em;} fieldset{margin:1.5em 0;padding:1em;border:1px solid #aaa;} legend{font-weight:bold;font-size:1.2em;} label{display:block;margin:0.5em 0;} input[type=text], input[type=number]{width:100%;padding:0.4em;box-sizing:border-box;} textarea{width:100%;min-height:80px;padding:.4em;box-sizing:border-box;} button{margin-top:1em;padding:0.5em 1.5em;} .msg{background:#eef;padding:.8em;margin-bottom:1em;border-left:4px solid #44d;} .traits-grid{display:grid;grid-template-columns:repeat(auto-fit, minmax(250px, 1fr));gap:1em;} .count{width:4em;margin-left:.5em;}
  </style>
</head>
<body>
  <h1>Criar Novo Personagem</h1>
  <?php if (!empty($_GET['msg'])): ?>
    <div class="msg"><?= htmlspecialchars($_GET['msg']) ?></div>
  <?php endif; ?>

  <form method="post">
    <fieldset><legend>Atributos Básicos</legend>
        <label>Nome: <input type="text" name="nome" required></label>
        <label>Força (F): <input type="number" name="F" value="0" min="0" required></label>
        <label>Habilidade (H): <input type="number" name="H" value="0" min="0" required></label>
        <label>Resistência (R): <input type="number" name="R" value="0" min="0" required></label>
        <label>Armadura (A): <input type="number" name="A" value="0" min="0" required></label>
        <label>Poder de Fogo (PdF): <input type="number" name="PdF" value="0" min="0" required></label>
        <label>Experiência (PE): <input type="number" name="PE" value="0" min="0" required></label>
    </fieldset>

    <fieldset><legend>Tipos de Dano Padrão</legend>
        <div class="traits-grid">
            <?php foreach ($allDamageTypes as $type): $safe_type = htmlspecialchars($type, ENT_QUOTES); ?>
            <label><input type="checkbox" name="damage_types[]" value="<?= $safe_type ?>"> <?= $safe_type ?></label>
            <?php endforeach; ?>
        </div>
    </fieldset>

    <fieldset><legend>Modificadores de Dano</legend>
        <div class="traits-grid" style="grid-template-columns: 1fr 1fr 1fr;">
            <div><strong>Vulnerabilidades</strong><?php foreach($allVulnerabilities as $type): $safe_type=htmlspecialchars($type,ENT_QUOTES); ?><label><input type="checkbox" name="vulnerabilities[]" value="<?=$safe_type?>"> <?=$safe_type?></label><?php endforeach; ?></div>
            <div><strong>Invulnerabilidades</strong><?php foreach($allInvulnerabilities as $type): $safe_type=htmlspecialchars($type,ENT_QUOTES); ?><label><input type="checkbox" name="invulnerabilities[]" value="<?=$safe_type?>"> <?=$safe_type?></label><?php endforeach; ?></div>
            <div><strong>Armadura Extra</strong><?php foreach($allExtraArmorTypes as $type): $safe_type=htmlspecialchars($type,ENT_QUOTES); ?><label><input type="checkbox" name="extra_armors[]" value="<?=$safe_type?>"> <?=$safe_type?></label><?php endforeach; ?></div>
        </div>
    </fieldset>

    <fieldset><legend>Vantagens</legend>
        <div class="traits-grid">
        <?php foreach ($advantages as $a): $key = "advantage:{$a['id']}"; ?>
            <label title="<?= htmlspecialchars($a['effect_text'], ENT_QUOTES) ?>">
            <?= htmlspecialchars($a['name'], ENT_QUOTES) ?>
            <input type="number" name="traits[<?= $key ?>]" value="0" min="0" class="count">
            </label>
        <?php endforeach; ?>
        </div>
    </fieldset>

    <fieldset><legend>Desvantagens</legend>
        <div class="traits-grid">
        <?php foreach ($disadvantages as $d): $key = "disadvantage:{$d['id']}"; ?>
            <label title="<?= htmlspecialchars($d['effect_text'], ENT_QUOTES) ?>">
            <?= htmlspecialchars($d['name'], ENT_QUOTES) ?>
            <input type="number" name="traits[<?= $key ?>]" value="0" min="0" class="count">
            </label>
        <?php endforeach; ?>
        </div>
    </fieldset>

    <fieldset><legend>Aliados</legend>
        <label>Selecione os aliados deste personagem:</label>
        <select name="allies[]" multiple size="8">
            <?php foreach($allPlayers as $p): $n=htmlspecialchars($p['nome'],ENT_QUOTES); ?>
            <option value="<?=$n?>"><?=$n?></option>
            <?php endforeach; ?>
        </select>
    </fieldset>

    <fieldset><legend>Equipamentos</legend>
        <label>Inventário:<textarea name="inventario"></textarea></label>
        <label>Equipado:<textarea name="equipado"></textarea></label>
    </fieldset>

    <button type="submit" name="submit">Criar Personagem</button>
    <a href="index.php">Voltar</a>
  </form>
</body>
</html>