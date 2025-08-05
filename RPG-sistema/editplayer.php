<?php
// editplayer.php - Gerenciador completo de personagens
include 'inc/generalFuncs.php';
include 'inc/traitFuncs.php';

$pdo = conecta();
$allPlayers = getAllPlayers();

// Carrega listas mestre para os formulários
$advantages = $pdo->query('SELECT id, name, effect_text FROM RPG.advantages ORDER BY name')->fetchAll(PDO::FETCH_ASSOC);
$disadvantages = $pdo->query('SELECT id, name, effect_text FROM RPG.disadvantages ORDER BY name')->fetchAll(PDO::FETCH_ASSOC);
$allDamageTypes = getAllDmgTypes();
$allVulnerabilities = getAllVulnerabilities();
$allInvulnerabilities = getAllInvulnerabilities();
$allExtraArmorTypes = getAllExtraArmorTypes();

// Seleção de personagem
$selected = $_GET['player'] ?? null;
$playerData = $selected ? getPlayer($selected) : null;
$msg = $_GET['msg'] ?? '';

// Processa submissão do formulário de edição
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save'])) {
    $nome = $_POST['nome'];
    
    // 1. Atualiza stats básicos e equipamentos
    foreach (['F','H','R','A','PdF','PE','PV','PV_max','PM','PM_max'] as $campo) {
        if (isset($_POST[$campo])) setPlayerStat($nome, $campo, (int)$_POST[$campo]);
    }
    setPlayerStat($nome, 'inventario', $_POST['inventario'] ?? '');
    setPlayerStat($nome, 'equipado',   $_POST['equipado']   ?? '');

    // 2. Gerencia Vantagens e Desvantagens (com contagem)
    $desiredTraits = array_map('intval', $_POST['traits'] ?? []);
    $currentTraits = ['advantage' => [], 'disadvantage' => []];
    
    $stmtAdv = $pdo->prepare("SELECT advantage_id, COUNT(*) as cnt FROM RPG.player_advantages WHERE player_name = ? GROUP BY advantage_id");
    $stmtAdv->execute([$nome]);
    foreach ($stmtAdv->fetchAll(PDO::FETCH_ASSOC) as $r) $currentTraits['advantage'][(int)$r['advantage_id']] = (int)$r['cnt'];
    
    $stmtDis = $pdo->prepare("SELECT disadvantage_id, COUNT(*) as cnt FROM RPG.player_disadvantages WHERE player_name = ? GROUP BY disadvantage_id");
    $stmtDis->execute([$nome]);
    foreach ($stmtDis->fetchAll(PDO::FETCH_ASSOC) as $r) $currentTraits['disadvantage'][(int)$r['disadvantage_id']] = (int)$r['cnt'];

    foreach ($desiredTraits as $key => $want) {
        list($type, $id) = explode(':', $key);
        $have = $currentTraits[$type][(int)$id] ?? 0;
        $diff = $want - $have;
        if ($diff > 0) for ($i=0; $i<$diff; $i++) addPlayerTrait($nome, (int)$id, $type);
        if ($diff < 0) for ($i=0; $i<-$diff; $i++) removePlayerTrait($nome, (int)$id, $type);
    }
    
    // 3. Gerencia Tipos de Dano, Modificadores e Aliados (lógica de "diff" de arrays)
    $managers = [
        'damage_types'      => ['list' => 'listPlayerDmgTypes',      'add' => 'addPlayerDmgType',      'remove' => 'removePlayerDmgType'],
        'vulnerabilities'   => ['list' => 'listPlayerVulnerabilities', 'add' => 'addPlayerVulnerability','remove' => 'removePlayerVulnerability'],
        'invulnerabilities' => ['list' => 'listPlayerInvulnerabilities', 'add' => 'addPlayerInvulnerability','remove' => 'removePlayerInvulnerability'],
        'extra_armors'      => ['list' => 'listPlayerExtraArmor',      'add' => 'addPlayerExtraArmor',   'remove' => 'removePlayerExtraArmor'],
        'allies'            => ['list' => 'getPlayerAllies',           'add' => 'addPlayerAlly',         'remove' => 'removePlayerAlly']
    ];

    foreach ($managers as $postKey => $funcs) {
        $currentItems = call_user_func($funcs['list'], $nome);
        $desiredItems = $_POST[$postKey] ?? [];
        foreach (array_diff($currentItems, $desiredItems) as $toRemove) call_user_func($funcs['remove'], $nome, $toRemove);
        foreach (array_diff($desiredItems, $currentItems) as $toAdd) call_user_func($funcs['add'], $nome, $toAdd);
    }

    $msg = "Personagem $nome atualizado com sucesso.";
    header('Location: editplayer.php?player='.urlencode($nome).'&msg='.urlencode($msg));
    exit;
}

// Carrega dados atuais para exibir no formulário
$currentTraits = ['advantage'=>[], 'disadvantage'=>[]];
$ownedAdvantages = $availableAdvantages = $ownedDisadvantages = $availableDisadvantages = [];
$currentDmgTypes = $currentVulnerabilities = $currentInvulnerabilities = $currentExtraArmors = $currentAllies = [];

if ($selected) {
    // Carrega traits e os separa em "possuídos" e "disponíveis"
    $stmtAdv = $pdo->prepare("SELECT advantage_id, COUNT(*) AS cnt FROM RPG.player_advantages WHERE player_name=? GROUP BY advantage_id");
    $stmtAdv->execute([$selected]);
    foreach($stmtAdv->fetchAll(PDO::FETCH_ASSOC) as $r) $currentTraits['advantage'][(int)$r['advantage_id']] = (int)$r['cnt'];
    
    $stmtDis = $pdo->prepare("SELECT disadvantage_id, COUNT(*) AS cnt FROM RPG.player_disadvantages WHERE player_name=? GROUP BY disadvantage_id");
    $stmtDis->execute([$selected]);
    foreach($stmtDis->fetchAll(PDO::FETCH_ASSOC) as $r) $currentTraits['disadvantage'][(int)$r['disadvantage_id']] = (int)$r['cnt'];

    foreach ($advantages as $a) {
        if (isset($currentTraits['advantage'][$a['id']])) $ownedAdvantages[] = $a;
        else $availableAdvantages[] = $a;
    }
    foreach ($disadvantages as $d) {
        if (isset($currentTraits['disadvantage'][$d['id']])) $ownedDisadvantages[] = $d;
        else $availableDisadvantages[] = $d;
    }

    $currentDmgTypes = listPlayerDmgTypes($selected);
    $currentVulnerabilities = listPlayerVulnerabilities($selected);
    $currentInvulnerabilities = listPlayerInvulnerabilities($selected);
    $currentExtraArmors = listPlayerExtraArmor($selected);
    $currentAllies = getPlayerAllies($selected);
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Editar Personagem</title>
  <style>
    body{font-family:sans-serif;max-width:800px;margin:2em auto;} form{border:1px solid #ccc;padding:1em;margin-bottom:1em;} fieldset{margin:1.5em 0;padding:1em;border:1px solid #aaa;} legend{font-weight:bold;font-size:1.2em;} label{display:block;margin:.5em 0;} .dual{display:flex;gap:1em;} .dual > * {flex: 1;} .traits-grid{display:grid;grid-template-columns:repeat(auto-fit, minmax(250px, 1fr));gap:1em;} input,textarea,select,button{padding:.4em;box-sizing:border-box;width:100%;} button{width:auto;padding:0.5em 1.5em;} .msg{background:#eef;padding:.8em;margin-bottom:1em;border-left:4px solid #44d;} .count{width:4em;margin-left:.5em;} h4{margin-top:1.5em;margin-bottom:0.5em;border-bottom:1px solid #ccc;padding-bottom:0.3em;} hr{margin: 1.5em 0; border: 0; border-top: 1px dashed #ccc;}
  </style>
</head>
<body>
  <h1>Editar Personagem</h1>
  <?php if (!empty($msg)): ?><div class="msg"><?=htmlspecialchars($msg)?></div><?php endif;?>

  <form method="get">
    <label>Selecione o Personagem:
      <select name="player" onchange="this.form.submit()">
        <option value="">-- Escolha --</option>
        <?php foreach($allPlayers as $p): $n=$p['nome']; $sel=($n===$selected?' selected':''); ?>
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
            <?php foreach ($allDamageTypes as $type): $safe_type=htmlspecialchars($type,ENT_QUOTES); $checked=in_array($type,$currentDmgTypes)?'checked':''; ?>
            <label><input type="checkbox" name="damage_types[]" value="<?=$safe_type?>" <?=$checked?>> <?=$safe_type?></label>
            <?php endforeach; ?>
        </div>
    </fieldset>

    <fieldset><legend>Modificadores de Dano</legend>
        <div class="traits-grid" style="grid-template-columns: 1fr 1fr 1fr;">
            <div><strong>Vulnerabilidades</strong><?php foreach($allVulnerabilities as $type): $safe_type=htmlspecialchars($type,ENT_QUOTES); $checked=in_array($type,$currentVulnerabilities)?'checked':''; ?><label><input type="checkbox" name="vulnerabilities[]" value="<?=$safe_type?>" <?=$checked?>> <?=$safe_type?></label><?php endforeach; ?></div>
            <div><strong>Invulnerabilidades</strong><?php foreach($allInvulnerabilities as $type): $safe_type=htmlspecialchars($type,ENT_QUOTES); $checked=in_array($type,$currentInvulnerabilities)?'checked':''; ?><label><input type="checkbox" name="invulnerabilities[]" value="<?=$safe_type?>" <?=$checked?>> <?=$safe_type?></label><?php endforeach; ?></div>
            <div><strong>Armadura Extra</strong><?php foreach($allExtraArmorTypes as $type): $safe_type=htmlspecialchars($type,ENT_QUOTES); $checked=in_array($type,$currentExtraArmors)?'checked':''; ?><label><input type="checkbox" name="extra_armors[]" value="<?=$safe_type?>" <?=$checked?>> <?=$safe_type?></label><?php endforeach; ?></div>
        </div>
    </fieldset>

    <fieldset><legend>Vantagens</legend>
        <h4>Vantagens Atuais</h4>
        <div class="traits-grid">
        <?php foreach($ownedAdvantages as $a): $cnt=$currentTraits['advantage'][$a['id']]??0; $key="advantage:{$a['id']}";?>
            <label title="<?=htmlspecialchars($a['effect_text'],ENT_QUOTES)?>"><?=htmlspecialchars($a['name'])?><input type="number" name="traits[<?=$key?>]" value="<?=$cnt?>" min="0" class="count"></label>
        <?php endforeach;?>
        </div><hr>
        <h4>Vantagens Disponíveis</h4>
        <div class="traits-grid">
        <?php foreach($availableAdvantages as $a): $key="advantage:{$a['id']}";?>
            <label title="<?=htmlspecialchars($a['effect_text'],ENT_QUOTES)?>"><?=htmlspecialchars($a['name'])?><input type="number" name="traits[<?=$key?>]" value="0" min="0" class="count"></label>
        <?php endforeach;?>
        </div>
    </fieldset>
    
    <fieldset><legend>Desvantagens</legend>
        <h4>Desvantagens Atuais</h4>
        <div class="traits-grid">
        <?php foreach($ownedDisadvantages as $d): $cnt=$currentTraits['disadvantage'][$d['id']]??0; $key="disadvantage:{$d['id']}";?>
            <label title="<?=htmlspecialchars($d['effect_text'],ENT_QUOTES)?>"><?=htmlspecialchars($d['name'])?><input type="number" name="traits[<?=$key?>]" value="<?=$cnt?>" min="0" class="count"></label>
        <?php endforeach;?>
        </div><hr>
        <h4>Desvantagens Disponíveis</h4>
        <div class="traits-grid">
        <?php foreach($availableDisadvantages as $d): $key="disadvantage:{$d['id']}";?>
            <label title="<?=htmlspecialchars($d['effect_text'],ENT_QUOTES)?>"><?=htmlspecialchars($d['name'])?><input type="number" name="traits[<?=$key?>]" value="0" min="0" class="count"></label>
        <?php endforeach;?>
        </div>
    </fieldset>

    <fieldset><legend>Aliados</legend>
        <label>Selecione os aliados deste personagem (use Ctrl/Cmd para selecionar múltiplos):</label>
        <select name="allies[]" multiple size="8">
            <?php foreach($allPlayers as $p): if($p['nome']===$selected) continue; $n=htmlspecialchars($p['nome'],ENT_QUOTES); $sel=in_array($p['nome'],$currentAllies)?' selected':''; ?>
            <option value="<?=$n?>"<?=$sel?>><?=$n?></option>
            <?php endforeach; ?>
        </select>
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