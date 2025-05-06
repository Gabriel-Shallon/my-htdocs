<?php
// editplayer.php - Selecionar e editar stats de um player existente
session_start();
include 'inc/func.php';

// Se recebeu submissão de edição
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save'])) {
    $nome = $_POST['nome'];
    // campos numéricos
    foreach (['F','H','R','A','PdF','PV','PM','PE'] as $campo) {
        setPlayerStat($nome, $campo, (int)$_POST[$campo]);
    }
    // inventário e equipado
    setPlayerStat($nome, 'inventario', $_POST['inventario'] ?? '');
    setPlayerStat($nome, 'equipado',  $_POST['equipado']  ?? '');
    header("Location: editplayer.php?msg=".urlencode("{$nome} atualizado com sucesso"));
    exit;
}

// Busca lista de players para seleção
$all = getAllPlayers();

// Se recebeu seleção, carregue dados
$selected = $_GET['player'] ?? null;
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
      input, textarea, select{width:100%;padding:.4em;box-sizing:border-box;}
      button{margin-top:1em;}
      .msg{background:#eef;padding:.8em;}
    </style>
</head>
<body>
  <h1>Editar Personagem</h1>
  <?php if (!empty($_GET['msg'])): ?>
    <div class="msg"><?= htmlspecialchars($_GET['msg']) ?></div>
  <?php endif; ?>

  <!-- Form para selecionar player -->
  <form method="get" action="editplayer.php">
    <label>Selecione um player:
      <select name="player" onchange="this.form.submit()">
        <option value="">-- Escolha --</option>
        <?php foreach ($all as $p):
            $n = htmlspecialchars($p['nome'], ENT_QUOTES);
            $sel = ($n === $selected) ? ' selected' : '';
        ?>
          <option value="<?= $n ?>"<?= $sel ?>><?= $n ?></option>
        <?php endforeach; ?>
      </select>
    </label>
  </form>

  <?php if ($playerData): ?>
    <!-- Form para editar stats -->
    <form method="post" action="editplayer.php">
      <input type="hidden" name="nome" value="<?= htmlspecialchars($selected,ENT_QUOTES) ?>">
      <?php foreach (['F'=>'Força','H'=>'Habilidade','R'=>'Resistência','A'=>'Agilidade','PdF'=>'Poder de Fogo','PV'=>'Vida','PM'=>'Magia','PE'=>'Experiência'] as $campo=>$label):
          $v = (int)$playerData[$campo]; ?>
        <label><?= $label ?> (<?= $campo ?>):
          <input type="number" name="<?= $campo ?>" value="<?= $v ?>" required>
        </label>
      <?php endforeach; ?>
      <label>Inventário:<br>
        <textarea name="inventario" rows="4"><?= htmlspecialchars($playerData['inventario'] ?? '', ENT_QUOTES) ?></textarea>
      </label>
      <label>Equipado:<br>
        <textarea name="equipado" rows="2"><?= htmlspecialchars($playerData['equipado'] ?? '', ENT_QUOTES) ?></textarea>
      </label>
      <button type="submit" name="save">Salvar Alterações</button>
      <a href="editplayer.php">Cancelar</a>
    </form>
  <?php endif; ?>
</body>
</html>
