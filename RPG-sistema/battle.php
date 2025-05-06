<?php
// battle.php - Sistema de Combate 3D&T
session_start();
include 'inc/func.php';

// Inicializa sessão de batalha
if (!isset($_SESSION['battle'])) {
    $_SESSION['battle'] = [
        'players'    => [],
        'order'      => [],
        'turn_index' => 0,
        'notes'      => [],
    ];
}

// Dispatcher de passos
$step = $_GET['step'] ?? 'select';
switch ($step) {

    // 1) Seleção de lutadores
    case 'select':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $selected = array_map('trim', $_POST['players'] ?? []);
            if (count($selected) < 2) {
                header('Location: battle.php'); exit;
            }
            $_SESSION['battle']['players'] = $selected;
            header('Location: battle.php?step=initiative'); exit;
        }
        $all = getAllPlayers();
        echo '<h1>Iniciar Batalha</h1><form method="post">';
        foreach ($all as $p) {
            $n = htmlspecialchars($p['nome'], ENT_QUOTES);
            echo "<label><input type='checkbox' name='players[]' value='{$n}'> {$n}</label><br>";
        }
        echo '<button type="submit">Confirmar</button></form>';
        break;

    // 2) Iniciativa
    case 'initiative':
        $b = &$_SESSION['battle'];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $dados = array_map('intval', $_POST['rolls'] ?? []);
            $inic  = iniciativa($b['players'], $dados);
            $b['order'] = array_column($inic, 0);
            header('Location: battle.php?step=turn'); exit;
        }
        echo '<h1>Iniciativa</h1><form method="post">';
        foreach ($b['players'] as $i => $nome) {
            echo "<label>{$nome}: <input type='number' name='rolls[{$i}]' required></label><br>";
        }
        echo '<button>Ok</button></form>';
        break;

    // 2.5) Atualizar stats
    case 'update_stats':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $pl = $_GET['player'] ?? '';
            if ($pl && in_array($pl, $_SESSION['battle']['players'], true)) {
                foreach ($_POST['stats'] as $stat => $val) {
                    setPlayerStat($pl, $stat, (int)$val);
                }
            }
        }
        header('Location: battle.php?step=turn'); exit;

    // 3) Turno atual
    case 'turn':
        $b     = &$_SESSION['battle'];
        $order = $b['order'];
        $cur   = $order[$b['turn_index'] % count($order)];
        $stats = getPlayer($cur);
        $notes = $b['notes'][$cur] ?? ['efeito'=>'','posição'=>'','concentrado'=>0];

        // máximo múltiplo
        $maxMulti = 1 + intdiv(max($stats['H'],0), 2);

        // Exibe stats editáveis
        echo "<h1>Turno de <strong>{$cur}</strong></h1>";
        echo "<h2>Stats</h2><form method='post' action='?step=update_stats&player=".urlencode($cur)."'>";
        foreach (['F','H','R','A','PdF','PV','PM','PE'] as $c) {
            $v = htmlspecialchars($stats[$c], ENT_QUOTES);
            echo "{$c}: <input type='number' name='stats[{$c}]' value='{$v}' required><br>";
        }
        echo '<button>Salvar Stats</button></form>';

        // Form de ações
        echo '<h2>Ações</h2><form method="post" action="?step=act">'
            ."<input type='hidden' name='player' value='".htmlspecialchars($cur,ENT_QUOTES)."'>"
            ."Efeito: <input name='efeito' value='".htmlspecialchars($notes['efeito'],ENT_QUOTES)."'><br>"
            ."Posição: <input name='posição' value='".htmlspecialchars($notes['posição'],ENT_QUOTES)."'><br>"
            ."Concentrado: <input type='number' name='concentrado' min=0 value='".htmlspecialchars($notes['concentrado'],ENT_QUOTES)."'><br>"
            ."Ação: <select id='action' name='action' onchange='onAct()'>";
        foreach ([
            'ataque'            => 'Atacar',
            'multiple'          => 'Múltiplo',
            'start_concentrar'  => 'Iniciar Concentração',
            'release_concentrar'=> 'Liberar Concentração',
            'fim'               => 'Terminar Batalha'
        ] as $v => $label) {
            echo "<option value='{$v}'>{$label}</option>";
        }
        echo "</select><br>";

        // Seção: ataque simples
        echo '<div id="atkSimple"><fieldset><legend>Ataque</legend>'
            .'Tipo: <select name="atkType"><option>F</option><option>PdF</option></select><br>'
            .'Roll FA: <input type="number" name="dadoFA"><br>'
            .'Alvo: <select name="target">';
        foreach ($order as $o) if ($o!==$cur) echo "<option>{$o}</option>";
        echo '</select><br>'
            .'Reação: <select id="def" name="defesa" onchange="onDef()">'
            .'<option value="defender">Defender</option>'
            .'<option value="defender_esquiva">Esquivar</option>'
            .'<option value="indefeso">Indefeso</option>'
            .'</select><br>'
            .'<label id="fdLbl">Roll FD/Esq.: <input id="fd" type="number" name="dadoFD"></label><br>'
            .'</fieldset></div>';

        // Seção: ataque múltiplo
        echo "<div id='atkMulti' style='display:none'><fieldset><legend>Múltiplo</legend>"
            ."Tipo: <select name='atkTypeMulti'><option>F</option><option>PdF</option></select><br>"
            ."Quantidade (2-{$maxMulti}): <input id='quant' type='number' name='quant' min=2 max={$maxMulti} value=2 onchange='gen()'><br>"
            ."Alvo: <select name='targetMulti'>";
        foreach ($order as $o) if ($o!==$cur) echo "<option>{$o}</option>";
        echo '</select><br>'
            .'Reação: <select id="defM" name="defesaMulti" onchange="onDefM()">'
            .'<option value="defender">Defender</option>'
            .'<option value="defender_esquiva">Esquivar</option>'
            .'<option value="indefeso">Indefeso</option>'
            .'</select><br>'
            .'<div id="dCont"></div>'
            .'</fieldset></div>';

        echo '<button type="submit">Executar</button> '
            .'<button type="button" onclick="history.back()">Voltar</button>'
            .'</form>';

            // Mini-tela final (resumo parcial) abaixo das ações,
        // **pulando** o player que está agindo
        echo '<h2>Resumo Parcial da Batalha</h2>';
        echo '<form method="post" action="?step=save_partial">';
        foreach ($b['players'] as $pl) {
            // Se for o player atual, pula
            if ($pl === $cur) {
                continue;
            }
            $ps = getPlayer($pl);
            echo "<fieldset style='margin:0.5em 0;padding:0.5em;border:1px solid #ccc'>";
            echo "<legend><strong>" . htmlspecialchars($pl, ENT_QUOTES) . "</strong></legend>";
            echo "<input type='hidden' name='player_names[]' value='" . htmlspecialchars($pl, ENT_QUOTES) . "'>";
            foreach (['F','H','R','A','PdF','PV','PM','PE'] as $c) {
                $val = (int)$ps[$c];
                echo "<label style='display:block'>{$c}: ";
                echo "<input type='number' name='partial_stats[{$pl}][{$c}]' value='{$val}' required>";
                echo "</label>";
            }
            echo "</fieldset>";
        }
        echo '<button type="submit">Salvar Resumo Parcial</button>';
        echo '</form>';


        // Script JavaScript
        echo <<<JS
<script>
document.addEventListener('DOMContentLoaded', () => {
  const actionSel = document.getElementById('action');
  const atkSimple = document.getElementById('atkSimple');
  const atkMulti  = document.getElementById('atkMulti');
  const dCont     = document.getElementById('dCont');
  const defM      = document.getElementById('defM');
  const def       = document.getElementById('def');
  const fdLbl     = document.getElementById('fdLbl');

  actionSel.addEventListener('change', onAct);
  def.addEventListener('change', onDef);
  defM.addEventListener('change', onDefM);

  function onAct() {
    if (actionSel.value === 'multiple') {
      atkSimple.style.display = 'none';
      atkMulti.style.display  = 'block';
      if (defM.value !== 'indefeso') gen();
      else clearMultiInputs();
    } else {
      atkSimple.style.display = 'block';
      atkMulti.style.display  = 'none';
      clearMultiInputs();
    }
    onDef();
  }

  function onDef() {
    fdLbl.style.display = def.value === 'indefeso' ? 'none' : 'block';
  }

  function onDefM() {
    if (actionSel.value !== 'multiple') return;
    if (defM.value === 'indefeso') clearMultiInputs();
    else gen();
  }

  function clearMultiInputs() {
    dCont.innerHTML = '';
  }

  function gen() {
    const cnt = parseInt(document.getElementById('quant').value,10) || 0;
    let html = '';
    for (let i = 1; i <= cnt; i++) {
      html += '<label>Roll FA ' + i + ': <input type="number" name="dadosMulti[]" required></label><br>';
    }
    html += '<label>Roll FD/Esq.: <input type="number" name="dadoFDMulti" required></label><br>';
    dCont.innerHTML = html;
  }

  onAct();
});
</script>
JS;
        break;

    // 4) Processar ação
    case 'act':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $b  = &$_SESSION['battle'];
            $pl = $_POST['player'] ?? '';
            $out = '';

            // Salva notas
            if ($pl && in_array($pl, $b['players'], true)) {
                $b['notes'][$pl] = [
                    'efeito'     => $_POST['efeito'] ?? '',
                    'posição'    => $_POST['posição'] ?? '',
                    'concentrado'=> (int)($_POST['concentrado'] ?? 0),
                ];
            }

            switch ($_POST['action'] ?? '') {
                case 'ataque':
                    $dFA  = (int)($_POST['dadoFA'] ?? 0);
                    $tipo = $_POST['atkType'] ?? 'F';
                    $tgt  = $_POST['target']  ?? '';
                    $dFD  = (int)($_POST['dadoFD'] ?? 0);
                    $def  = $_POST['defesa']   ?? 'defender';

                    if ($def === 'indefeso') {
                        $dano = FAFDindefeso($pl, $tgt, $dFA, $tipo);
                    } elseif ($def === 'defender_esquiva') {
                        $dano = FAFDesquiva($pl, $tgt, $dFD, $dFA, $tipo);
                    } else {
                        $dano = FAFDresult($pl, $tgt, $dFA, $dFD, $tipo);
                    }

                    setPlayerStat($tgt, 'PV', max(getPlayerStat($tgt,'PV') - $dano, 0));
                    $out = "<strong>{$pl}</strong> atacou <strong>{$tgt}</strong> ({$tipo}): dano = {$dano}";
                    break;

                case 'multiple':
                    $tipo = $_POST['atkTypeMulti']   ?? 'F';
                    $tgt  = $_POST['targetMulti']    ?? '';
                    $q    = (int)($_POST['quant']    ?? 1);
                    $dados= $_POST['dadosMulti']      ?? [];
                    $dFD  = (int)($_POST['dadoFDMulti'] ?? 0);
                    $def  = $_POST['defesaMulti']     ?? 'defender';

                    $faTot = FAmulti($pl, $q, $tipo, $dados);

                    if ($def === 'indefeso') {
                        $dano = max($faTot - FDindefeso($tgt), 0);
                    } elseif ($def === 'defender_esquiva') {
                        $dano = FAFDesquiva($pl, $tgt, $dFD, $faTot, $tipo);
                    } else {
                        $dano = max($faTot - FD($tgt, $dFD), 0);
                    }

                    setPlayerStat($tgt, 'PV', max(getPlayerStat($tgt,'PV') - $dano, 0));
                    $out = "<strong>{$pl}</strong> fez ataque múltiplo em <strong>{$tgt}</strong> ({$q}x{$tipo}): FA total = {$faTot}, dano = {$dano}";
                    break;

                case 'start_concentrar':
                    $b['notes'][$pl]['concentrado'] = 1;
                    $out = "<strong>{$pl}</strong> iniciou concentração (+1 de FA no próximo release)";
                    break;

                case 'release_concentrar':
                    $bonus = $b['notes'][$pl]['concentrado'] ?? 0;
                    $dFA   = (int)($_POST['dadoFA'] ?? 0);
                    $tipo  = $_POST['atkType']  ?? 'F';
                    $tgt   = $_POST['target']   ?? '';

                    $dano = FAFDresult($pl, $tgt, $dFA + $bonus, 0, $tipo);
                    setPlayerStat($tgt, 'PV', max(getPlayerStat($tgt,'PV') - $dano, 0));
                    $b['notes'][$pl]['concentrado'] = 0;
                    $out = "<strong>{$pl}</strong> liberou ataque concentrado em <strong>{$tgt}</strong> ({$tipo} +{$bonus}): dano = {$dano}";
                    break;

                case 'fim':
                    header('Location: battle.php?step=final');
                    exit;

                default:
                    $out = 'Ação inválida ou não reconhecida.';
                    break;
            }

            $b['turn_index'] ++;
            echo "<p>{$out}</p>";
            echo '<p><a href="battle.php?step=turn">Próximo Turno</a></p>';
        } else {
            echo '<p>Nenhum dado recebido para processar a ação.</p>';
            echo '<p><a href="battle.php?step=turn">Voltar ao Turno</a></p>';
        }
        exit;

    // 4.5) Salvar Resumo Parcial
    case 'save_partial':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $names        = $_POST['player_names']    ?? [];
            $partialStats = $_POST['partial_stats']   ?? [];
            foreach ($names as $pl) {
                if (isset($partialStats[$pl])) {
                    foreach ($partialStats[$pl] as $campo => $valor) {
                        setPlayerStat($pl, $campo, (int)$valor);
                    }
                }
            }
        }
        header('Location: battle.php?step=turn'); exit;

    // 5) Tela Final
    case 'final':
        $b       = &$_SESSION['battle'];
        $players = $b['players'];
        echo '<h1>Resumo da Batalha</h1>';
        echo '<form method="post" action="battle.php?step=save_final">';
        foreach ($players as $pl) {
            $stats = getPlayer($pl);
            echo "<fieldset style='margin-bottom:1em;padding:1em;border:1px solid #ccc'>";
            echo "<legend><strong>" . htmlspecialchars($pl, ENT_QUOTES) . "</strong></legend>";
            echo "<input type='hidden' name='player_names[]' value='" . htmlspecialchars($pl, ENT_QUOTES) . "'>";
            foreach (['F','H','R','A','PdF','PV','PM','PE'] as $c) {
                $v = (int)$stats[$c];
                echo "<label style='display:block;margin:0.5em 0'>{$c}: ";
                echo "<input type='number' name='stats[{$pl}][{$c}]' value='{$v}' required>";
                echo "</label>";
            }
            echo "</fieldset>";
        }
        echo '<button type="submit">Salvar Alterações</button>';
        echo ' <a href="index.php">Nova Batalha</a>';
        echo '</form>';
        exit;

    // 6) Salvar Final
    case 'save_final':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $names    = $_POST['player_names'] ?? [];
            $allStats = $_POST['stats']        ?? [];
            foreach ($names as $pl) {
                if (isset($allStats[$pl])) {
                    foreach ($allStats[$pl] as $campo => $valor) {
                        setPlayerStat($pl, $campo, (int)$valor);
                    }
                }
            }
            session_destroy();
            echo '<p>Alterações salvas com sucesso!</p>';
            echo '<p><a href="index.php">Nova Batalha</a></p>';
        }
        exit;

    default:
        header('Location: battle.php');
        exit;
}
