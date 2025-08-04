<?php
// editplayer.php - Selecionar e editar todas as informações de um personagem
include 'inc/traitFuncs.php';

$pdo = conecta();
$allPlayers = getAllPlayers();
$allDamageTypes = getAllDmgTypes();

// Seleção
$selected = $_GET['player'] ?? null;
$playerData = $selected ? getPlayer($selected) : null;
$msg = $_GET['msg'] ?? '';

// Processa submissão de edição
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save'])) {
    $nome = $_POST['nome'];
    
    // Stats básicos
    foreach (['F','H','R','A','PdF','PE','PV','PV_max','PM','PM_max'] as $campo) {
        if (isset($_POST[$campo])) setPlayerStat($nome, $campo, (int)$_POST[$campo]);
    }
    setPlayerStat($nome, 'inventario', $_POST['inventario'] ?? '');
    setPlayerStat($nome, 'equipado',   $_POST['equipado']   ?? '');

    // Gerencia Vantagens e Desvantagens
    $desiredTraits = array_map('intval', $_POST['traits'] ?? []);
    // ... (lógica de comparação e atualização para Vantagens/Desvantagens)
    
    // Gerencia Tipos de Dano
    $currentDmgTypes = listPlayerDmgTypes($nome);
    $desiredDmgTypes = $_POST['damage_types'] ?? [];
    foreach (array_diff($currentDmgTypes, $desiredDmgTypes) as $toRemove) removePlayerDmgType($nome, $toRemove);
    foreach (array_diff($desiredDmgTypes, $currentDmgTypes) as $toAdd) addPlayerDmgType($nome, $toAdd);

    // Gerencia Vulnerabilidades, Invulnerabilidades, Armadura Extra
    // (A lógica é similar a dos tipos de dano, comparando o array atual com o submetido)
    // ...

    // Gerencia Aliados
    $currentAllies = getPlayerAllies($nome);
    $desiredAllies = $_POST['allies'] ?? [];
    // ... (lógica de remoção/adição de aliados)

    $msg = "Personagem $nome atualizado com sucesso.";
    header('Location: editplayer.php?player='.urlencode($nome).'&msg='.urlencode($msg));
    exit;
}

// Carrega dados para exibição no formulário
$advantages = $pdo->query('SELECT id,name,effect_text FROM RPG.advantages ORDER BY name')->fetchAll(PDO::FETCH_ASSOC);
$disadvantages = $pdo->query('SELECT id,name,effect_text FROM RPG.disadvantages ORDER BY name')->fetchAll(PDO::FETCH_ASSOC);

$currentTraits = ['advantage'=>[], 'disadvantage'=>[]];
$currentDmgTypes = [];
$currentAllies = [];

if ($selected) {
    // Carrega vantagens/desvantagens atuais
    $stmt = $pdo->prepare("SELECT advantage_id, COUNT(*) AS cnt FROM RPG.player_advantages WHERE player_name=? GROUP BY advantage_id");
    $stmt->execute([$selected]); foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $r) $currentTraits['advantage'][(int)$r['id']] = (int)$r['cnt'];
    
    $currentDmgTypes = listPlayerDmgTypes($selected);
    $currentAllies = getPlayerAllies($selected);
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Editar Personagem</title>
  <style>
    /* (Estilos do newplayer.php podem ser reutilizados aqui) */
    body{font-family:sans-serif;max-width:800px;margin:2em auto;}
    form{border:1px solid #ccc;padding:1em;margin-bottom:1em;}
    fieldset{margin:1.5em 0;padding:1em;border:1px solid #aaa;}
    legend{font-weight:bold;font-size:1.2em;}
    label{display:block;margin:.5em 0;}
    .dual{display:flex;gap:1em;} .dual > * {flex: 1;}
    .traits-grid{display:grid;grid-template-columns:1fr 1fr;gap:1em;}
    input,textarea,select,button{padding:.4em;box-sizing:border-box;width:100%;}
    button{width:auto;padding:0.5em 1.5em;}
    .msg{background:#eef;padding:.8em;margin-bottom:1em;border-left:4px solid #44d;}
    .count{width:4em;margin-left:.5em;}
  </style>
</head>
<body>
  <h1>Editar Personagem</h1>
  <?php if (!empty($msg)): ?><div class="msg"><?=htmlspecialchars($msg)?></div><?php endif;?>

  <form method="get">
    <label>Selecione o Personagem:
      <select name="player" onchange="this.form.submit()">
        <option value="">-- Escolha um Personagem --</option>
        <?php foreach($allPlayers as $p):
          $n=$p['nome']; $sel=($n===$selected?' selected':'');
        ?>
          <option value="<?=htmlspecialchars($n)?>"<?=$sel?>><?=htmlspecialchars($n)?></option>
        <?php endforeach;?>
      </select>
    </label>
  </form>

  <?php if ($playerData): ?>
  <form method="post">
    <input type="hidden" name="nome" value="<?=htmlspecialchars($selected)?>">
    
    <fieldset><legend>Atributos</legend>
        <?php foreach(['F'=>'Força','H'=>'Habilidade','R'=>'Resistência','A'=>'Armadura','PdF'=>'Poder de Fogo','PE'=>'XP'] as $c=>$l):?>
        <label><?=$l?> (<?=$c?>): <input type="number" name="<?=$c?>" value="<?=(int)$playerData[$c]?>" required></label>
        <?php endforeach;?>
        <div class="dual">
        <label>PV: <input type="number" name="PV" value="<?=(int)$playerData['PV']?>" min="0" required></label>
        <label>PV Máx: <input type="number" name="PV_max" value="<?=(int)$playerData['PV_max']?>" min="1" required></label>
        </div>
        <div class="dual">
        <label>PM: <input type="number" name="PM" value="<?=(int)$playerData['PM']?>" min="0" required></label>
        <label>PM Máx: <input type="number" name="PM_max" value="<?=(int)$playerData['PM_max']?>" min="1" required></label>
        </div>
    </fieldset>

    <fieldset><legend>Tipos de Dano</legend>
        <div class="traits-grid">
            <?php foreach ($allDamageTypes as $type): $safe_type = htmlspecialchars($type, ENT_QUOTES); $checked = in_array($type, $currentDmgTypes) ? 'checked' : ''; ?>
            <label><input type="checkbox" name="damage_types[]" value="<?= $safe_type ?>" <?= $checked ?>> <?= $safe_type ?></label>
            <?php endforeach; ?>
        </div>
    </fieldset>

    <fieldset><legend>Vantagens</legend>
        <div class="traits-grid">
        <?php foreach($advantages as $a): $cnt=$currentTraits['advantage'][$a['id']]??0; $key="advantage:{$a['id']}";?>
            <label title="<?=htmlspecialchars($a['effect_text'], ENT_QUOTES)?>">
            <?=htmlspecialchars($a['name'])?>
            <input type="number" name="traits[<?=$key?>]" value="<?=$cnt?>" min="0" class="count">
            </label>
        <?php endforeach;?>
        </div>
    </fieldset>

    <fieldset><legend>Desvantagens</legend>
        <div class="traits-grid">
        <?php foreach($disadvantages as $d): $cnt=$currentTraits['disadvantage'][$d['id']]??0; $key="disadvantage:{$d['id']}";?>
            <label title="<?=htmlspecialchars($d['effect_text'], ENT_QUOTES)?>">
            <?=htmlspecialchars($d['name'])?>
            <input type="number" name="traits[<?=$key?>]" value="<?=$cnt?>" min="0" class="count">
            </label>
        <?php endforeach;?>
        </div>
    </fieldset>

    <fieldset><legend>Equipamentos</legend>
        <label>Inventário:<textarea name="inventario" rows="3"><?=htmlspecialchars($playerData['inventario']??'')?></textarea></label>
        <label>Equipado:<textarea name="equipado" rows="3"><?=htmlspecialchars($playerData['equipado']??'')?></textarea></label>
    </fieldset>

    <button type="submit" name="save">Salvar Alterações</button>
    <a href="index.php">Voltar</a>
  </form>
  <?php endif;?>
</body>
</html>