<?php
// initiative.php - Calculadora de Iniciativa 3D&T

include 'inc/traitFuncs.php';

// Lista todos os jogadores
$allPlayers = getAllPlayers();

// Participantes escolhidos
$participants = $_POST['participants'] ?? [];
$orderResult = null;
$step = 'select'; // Etapa inicial

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($participants)) {
        if (isset($_POST['roll_dice'])) {
            $dados = $_POST['dado'] ?? [];
            $orderResult = iniciativa($participants, $dados);
            $step = 'result';
        } else {
            $step = 'roll';
        }
    }
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
    .inline input[type=checkbox] {margin-right:.5em;}
    input[type=number]{width:60px;}
    th, td{padding:.4em;border:1px solid #ccc;text-align:left;}
    .nav {margin-top:1em;}
    .formula {font-size: 0.8em; color: #555;}
  </style>
</head>
<body>
  <h1>Calculadora de Iniciativa</h1>

  <!-- Etapa 1: Seleção -->
  <?php if ($step === 'select'): ?>
  <form method="post">
    <p>Selecione os participantes:</p>
    <?php foreach ($allPlayers as $p):
      $n = htmlspecialchars($p['nome'], ENT_QUOTES);
    ?>
      <label class="inline">
        <input type="checkbox" name="participants[]" value="<?= $n ?>">
        <?= $n ?> (H = <?= (int)getPlayerStat($n,'H') ?>)
      </label>
    <?php endforeach; ?>
    <button type="submit">Continuar</button>
    <div class="nav"><a href="index.php">Voltar</a></div>
  </form>
  <?php endif; ?>

  <!-- Etapa 2: Rolagem -->
  <?php if ($step === 'roll'): ?>
  <form method="post">
    <p>Insira os dados da rolagem:</p>
    <?php foreach ($participants as $nome):
        $safe_name = htmlspecialchars($nome, ENT_QUOTES);
        $H = (int)getPlayerStat($nome,'H');
    ?>
      <input type="hidden" name="participants[]" value="<?= $safe_name ?>">
      <label class="inline">
        <?= $safe_name ?> (H = <?= $H ?>):
        <input type="number" name="dado[]" min="1" max="6" required>
      </label>
    <?php endforeach; ?>
    <button type="submit" name="roll_dice">Calcular Iniciativa</button>
    <div class="nav"><a href="initiative.php">Voltar</a></div>
  </form>
  <?php endif; ?>

  <!-- Resultado -->
  <?php if ($step === 'result' && $orderResult): ?>
    <h2>Ordem de Iniciativa</h2>
    <table>
      <thead><tr><th>Pos.</th><th>Nome</th><th>Total</th><th>Cálculo</th></tr></thead>
      <tbody>
      <?php $pos = 1; foreach ($orderResult as $data): ?>
        <tr>
          <td><?= $pos++ ?></td>
          <td><?= htmlspecialchars($data['nome'], ENT_QUOTES) ?></td>
          <td><strong><?= $data['total'] ?></strong></td>
          <td class="formula">(H: <?= $data['habilidade'] ?> + Dado: <?= $data['dado'] ?> + Bônus: <?= $data['bonus'] ?>)</td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
    <div class="nav">
      <a href="initiative.php">Nova Iniciativa</a> |
      <a href="index.php">Voltar ao Início</a>
    </div>
  <?php endif; ?>
</body>
</html>