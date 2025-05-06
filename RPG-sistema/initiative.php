<?php
// initiative.php - Calculadora de Iniciativa 3D&T com seleção checkbox e navegação
session_start();
include 'inc/func.php';

// Lista todos os jogadores
$allPlayers = getAllPlayers();

// Participantes escolhidos
$participants = $_POST['participants'] ?? [];
$orderResult = null;

// Processa rolagens
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['step']) && $_POST['step'] === 'roll') {
    $inic = [];
    foreach ($participants as $nome) {
        $H = (int) getPlayerStat($nome, 'H');
        $dado = (int) ($_POST['dado'][$nome] ?? 0);
        $inic[$nome] = ['total' => $H + $dado, 'H' => $H];
    }
    uasort($inic, function($a, $b) {
        if ($b['total'] !== $a['total']) return $b['total'] <=> $a['total'];
        if ($b['H'] !== $a['H']) return $b['H'] <=> $a['H'];
        return rand(-1,1);
    });
    $orderResult = $inic;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Calculadora de Iniciativa</title>
  <style>
    body{font-family:sans-serif;max-width:600px;margin:2em auto;}
    form, table{width:100%;border:1px solid #ccc;padding:1em;margin-bottom:1em;}
    label{display:block;margin:.5em 0;}
    .inline {display:flex;align-items:center;}
    .inline input {margin-right:.5em;}
    input[type=number]{width:60px;}
    th, td{padding:.4em;border:1px solid #ccc;text-align:left;}
    .nav {margin-top:1em;}
  </style>
</head>
<body>
  <h1>Calculadora de Iniciativa</h1>

  <!-- Etapa 1: seleção de participantes via checkboxes -->
  <?php if (empty($participants) || (isset($_POST['step']) && $_POST['step'] !== 'roll')): ?>
  <form method="post" action="initiative.php">
    <input type="hidden" name="step" value="select">
    <p>Selecione participantes:</p>
    <?php foreach ($allPlayers as $p): 
      $n = htmlspecialchars($p['nome'], ENT_QUOTES);
      $checked = in_array($n, $participants) ? ' checked' : '';
    ?>
      <label class="inline">
        <input type="checkbox" name="participants[]" value="<?= $n ?>"<?= $checked ?> required>
        <?= $n ?> (H = <?= getPlayerStat($n,'H') ?>)
      </label>
    <?php endforeach; ?>
    <button type="submit">Continuar</button>
    <div class="nav"><a href="index.php">Voltar ao Início</a></div>
  </form>
  <?php endif; ?>

  <!-- Etapa 2: rolagem de dados -->
  <?php if (!empty($participants) && (!isset($_POST['step']) || $_POST['step'] === 'select')): ?>
  <form method="post" action="initiative.php">
    <input type="hidden" name="step" value="roll">
    <?php foreach ($participants as $nome): ?>
      <?php $H = (int)getPlayerStat($nome,'H'); ?>
      <label class="inline">
        <?= htmlspecialchars($nome,ENT_QUOTES) ?> (H = <?= $H ?>):
        <input type="number" name="dado[<?= htmlspecialchars($nome,ENT_QUOTES) ?>]" required>
      </label>
    <?php endforeach; ?>
    <?php foreach ($participants as $nome): ?>
      <input type="hidden" name="participants[]" value="<?= htmlspecialchars($nome,ENT_QUOTES) ?>">
    <?php endforeach; ?>
    <button type="submit">Calcular Iniciativa</button>
    <div class="nav"><a href="index.php">Voltar ao Início</a></div>
  </form>
  <?php endif; ?>

  <!-- Resultado -->
  <?php if ($orderResult): ?>
    <h2>Ordem de Iniciativa</h2>
    <table>
      <tr><th>Posição</th><th>Nome</th><th>Total (H + Dado)</th><th>H</th></tr>
      <?php $pos = 1; foreach ($orderResult as $nome => $data): ?>
        <tr>
          <td><?= $pos ?></td>
          <td><?= htmlspecialchars($nome,ENT_QUOTES) ?></td>
          <td><?= $data['total'] ?></td>
          <td><?= $data['H'] ?></td>
        </tr>
      <?php $pos++; endforeach; ?>
    </table>
    <div class="nav">
      <a href="initiative.php">Nova Iniciativa</a> |
      <a href="index.php">Voltar ao Início</a>
    </div>
  <?php endif; ?>
</body>
</html>
