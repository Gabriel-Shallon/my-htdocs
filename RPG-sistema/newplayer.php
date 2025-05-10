<?php
include 'inc/func.php';

// Busca vantagens e desvantagens
$pdo           = conecta();
$advantages    = $pdo->query('SELECT id, name, effect_text FROM RPG.advantages')->fetchAll(PDO::FETCH_ASSOC);
$disadvantages = $pdo->query('SELECT id, name, effect_text FROM RPG.disadvantages')->fetchAll(PDO::FETCH_ASSOC);

// Criação de player
if (isset($_POST['submit'])) {
    newPlayer(
      $_POST['nome'], $_POST['F'], $_POST['H'], $_POST['R'],
      $_POST['A'], $_POST['PdF'], $_POST['PE'],
      $_POST['inventario']  ?? '',
      $_POST['equipado']    ?? ''
    );
    // Associa traits
    $stmtAdv = $pdo->prepare('INSERT INTO RPG.player_advantages (player_name, advantage_id) VALUES (:nome, :id)');
    foreach ($_POST['advantages']    ?? [] as $aid) $stmtAdv->execute([':nome'=>$_POST['nome'],':id'=>$aid]);
    $stmtDis = $pdo->prepare('INSERT INTO RPG.player_disadvantages (player_name, disadvantage_id) VALUES (:nome, :id)');
    foreach ($_POST['disadvantages'] ?? [] as $did) $stmtDis->execute([':nome'=>$_POST['nome'],':id'=>$did]);

    header('Location: newplayer.php?msg=Personagem+'.urlencode($_POST['nome']).'+criado+com+sucesso');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Criar Personagem</title>
  <style>
    body{font-family:sans-serif;max-width:600px;margin:2em auto;}
    form{border:1px solid #ccc;padding:1em;}
    fieldset{margin-bottom:1em;padding:1em;border:1px solid #aaa;}
    legend{font-weight:bold;}
    label{display:block;cursor:pointer;}
    .effect {
      display: none;
      margin: .3em 1.5em;
      padding: .5em;
      background: #f0f8ff;
      border-left: 3px solid #66a;
      font-style: italic;
    }
    textarea{width:100%;min-height:120px;padding:.4em;box-sizing:border-box;}
    button{margin-top:1em;}
    .msg{background:#eef;padding:.8em;}
  </style>
</head>
<body>
  <h1>Criar Novo Personagem</h1>
  <?php if (!empty($_GET['msg'])): ?>
    <div class="msg"><?= htmlspecialchars($_GET['msg']) ?></div>
  <?php endif; ?>

  <form method="post" action="newplayer.php">
    <label>Nome: <input type="text" name="nome" required></label>
    <label>Força (F): <input type="number" name="F" min="0" required></label>
    <label>Habilidade (H): <input type="number" name="H" min="0" required></label>
    <label>Resistência (R): <input type="number" name="R" min="0" required></label>
    <label>Armadura (A): <input type="number" name="A" min="0" required></label>
    <label>Poder de Fogo (PdF): <input type="number" name="PdF" min="0" required></label>
    <label>Experiência (PE): <input type="number" name="PE" min="0" required></label>

    <fieldset>
      <legend>Vantagens</legend>
      <?php foreach ($advantages as $a): ?>
        <label>
          <input type="checkbox"
                 name="advantages[]"
                 value="<?= $a['id'] ?>"
                 data-effect="<?= htmlspecialchars($a['effect_text'], ENT_QUOTES) ?>">
          <?= htmlspecialchars($a['name'], ENT_QUOTES) ?>
        </label>
        <div class="effect"></div>
      <?php endforeach; ?>
    </fieldset>

    <fieldset>
      <legend>Desvantagens</legend>
      <?php foreach ($disadvantages as $d): ?>
        <label>
          <input type="checkbox"
                 name="disadvantages[]"
                 value="<?= $d['id'] ?>"
                 data-effect="<?= htmlspecialchars($d['effect_text'], ENT_QUOTES) ?>">
          <?= htmlspecialchars($d['name'], ENT_QUOTES) ?>
        </label>
        <div class="effect"></div>
      <?php endforeach; ?>
    </fieldset>

    <label>Inventário:<br>
      <textarea name="inventario" placeholder="Ex: Punhal (F+3)…"></textarea>
    </label>

    <label>Equipado:<br>
      <textarea name="equipado" placeholder="Ex: Espada Longa (F+2)…"></textarea>
    </label>

    <button type="submit" name="submit">Criar Personagem</button>
    <a href="index.php">Voltar</a>
  </form>

  <script>
    // Para cada checkbox com data-effect, liga um listener
    document.querySelectorAll('input[type=checkbox][data-effect]').forEach(cb => {
      const box = cb, effectDiv = cb.closest('label').nextElementSibling;
      // Inicializa texto
      effectDiv.textContent = box.dataset.effect;
      box.addEventListener('change', () => {
        effectDiv.style.display = box.checked ? 'block' : 'none';
      });
    });
  </script>
</body>
</html>
