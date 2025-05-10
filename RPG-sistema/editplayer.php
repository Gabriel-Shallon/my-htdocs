<?php
// editplayer.php - Selecionar e editar stats de um player existente
session_start();
include 'inc/func.php';

// Se recebeu submissão de edição
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save'])) {
    $nome = $_POST['nome'];
    // campos numéricos fixos
    foreach (['F','H','R','A','PdF','PE'] as $campo) {
        setPlayerStat($nome, $campo, (int)$_POST[$campo]);
    }
    // Vida atual e máxima
    setPlayerStat($nome, 'PV',     (int)$_POST['PV']);
    setPlayerStat($nome, 'PV_max', (int)$_POST['PV_max']);
    // Magia atual e máxima
    setPlayerStat($nome, 'PM',     (int)$_POST['PM']);
    setPlayerStat($nome, 'PM_max', (int)$_POST['PM_max']);
    // inventário e equipado
    setPlayerStat($nome, 'inventario', $_POST['inventario'] ?? '');
    setPlayerStat($nome, 'equipado',   $_POST['equipado']   ?? '');
    header("Location: editplayer.php?msg=".urlencode("{$nome} atualizado com sucesso"));
    exit;
}

// Lista de players
$all = getAllPlayers();

// Se recebeu seleção, carrega dados
$selected   = $_GET['player'] ?? null;
$playerData = $selected ? getPlayer($selected) : null;
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Editar Personagem</title>
  <style>
    body{font-family:sans-serif;max-width:600px;margin:2em auto;}
    form{border:1px solid #ccc;padding:1em;margin-bottom:1em;}
    label{display:block;margin:.5em 0;}
    .dual-inputs { display: flex; gap: 1em; }
    .dual-inputs input { width: 100%; }
    input, textarea, select{padding:.4em;box-sizing:border-box;}
    button{margin-top:1em;}
    .msg{background:#eef;padding:.8em;}
  </style>
</head>
<body>
  <h1>Editar Personagem</h1>
  <?php if (!empty($_GET['msg'])): ?>
    <div class="msg"><?= htmlspecialchars($_GET['msg']) ?></div>
  <?php endif; ?>

  <!-- Seleção de player -->
  <form method="get" action="editplayer.php">
    <label>Selecione um player:
      <select name="player" onchange="this.form.submit()">
        <option value="">-- Escolha --</option>
        <?php foreach ($all as $p):
          $n   = htmlspecialchars($p['nome'], ENT_QUOTES);
          $sel = ($n === $selected) ? ' selected' : '';
        ?>
          <option value="<?= $n ?>"<?= $sel ?>><?= $n ?></option>
        <?php endforeach; ?>
      </select>
    </label>
  </form>

  <?php if ($playerData): ?>
    <!-- Form de edição -->
    <form method="post" action="editplayer.php">
      <input type="hidden" name="nome" value="<?= htmlspecialchars($selected,ENT_QUOTES) ?>">

      <?php
      // Campos fixos
      $labels = [
        'F'   => 'Força',
        'H'   => 'Habilidade',
        'R'   => 'Resistência',
        'A'   => 'Armadura',
        'PdF' => 'Poder de Fogo',
        'PE'  => 'Experiência'
      ];
      foreach ($labels as $campo => $label):
        $v = (int)$playerData[$campo];
      ?>
        <label><?= $label ?> (<?= $campo ?>):
          <input type="number" name="<?= $campo ?>" value="<?= $v ?>" required>
        </label>
      <?php endforeach; ?>

      <!-- PV atual / máximo -->
      
        <div class="dual-inputs">
        <label>Vida (PV):  
          <input type="number" name="PV"     value="<?= (int)$playerData['PV']     ?>" min="0" required placeholder="Atual">
        </label>
        <label>Vida Máxima  
          <input type="number" name="PV_max" value="<?= (int)$playerData['PV_max'] ?>" min="1" required placeholder="Máx">
        </label>
        </div>
      

      <!-- PM atual / máximo -->
        <div class="dual-inputs">
        <label>Magia (PM):  
          <input type="number" name="PM"     value="<?= (int)$playerData['PM']     ?>" min="0" required placeholder="Atual">
        </label>
        <label>Magia Máxima  
          <input type="number" name="PM_max" value="<?= (int)$playerData['PM_max'] ?>" min="1" required placeholder="Máx">
        </label>
        </div>

      <!-- Inventário e Equipado -->
      <label>Inventário:
        <textarea name="inventario" rows="4" cols='60'><?= htmlspecialchars($playerData['inventario'] ?? '', ENT_QUOTES) ?></textarea>
      </label>
      <label>Equipado:
        <textarea name="equipado" rows="4" cols='60'><?= htmlspecialchars($playerData['equipado'] ?? '', ENT_QUOTES) ?></textarea>
      </label>

      <button type="submit" name="save">Salvar Alterações</button>
      <a href="editplayer.php">Cancelar</a>
    </form>
  <?php endif; ?>
</body>
</html>
