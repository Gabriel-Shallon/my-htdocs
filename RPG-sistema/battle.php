<?php
// battle.php - Sistema de Combate 3D&T (com Inventário e Equipado persistentes)
session_start();
include 'inc/func.php';
include 'inc/traitList.php';

    if (!isset($_SESSION['battle'])) {
        $_SESSION['battle'] = [];
    }
    if (!array_key_exists('players',    $_SESSION['battle'])) $_SESSION['battle']['players']    = [];
    if (!array_key_exists('order',      $_SESSION['battle'])) $_SESSION['battle']['order']      = [];
    if (!array_key_exists('init_index', $_SESSION['battle'])) $_SESSION['battle']['init_index'] = 0;
    if (!array_key_exists('round',      $_SESSION['battle'])) $_SESSION['battle']['round']      = 1;
    if (!array_key_exists('notes',      $_SESSION['battle'])) $_SESSION['battle']['notes']      = [];


// Inicializa sessão de batalha
if (!isset($_SESSION['battle'])) {
    $_SESSION['battle'] = [
        'players'     => [],
        'order'       => [],
        'init_index'  => 0,
        'round'       => 1,
        'notes'       => [],
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
        echo '<label><input type="checkbox" name="players[]" value="'.$n.'"> '.$n.'</label><br>';
    }
    echo '<button type="submit">Confirmar</button></form>';
    break;

    if (in_array('assombrado', listPlayerTraits($cur))) {
        echo '<label>Roll Assombrado (1–6): <input type="number" name="roll_assombrado" min="1" max="6" required></label><br>';
    }   

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
        echo '<label>'.$nome.': <input type="number" name="rolls['.$i.']" required></label><br>';
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
            if (isset($_POST['inventory'])) setPlayerStat($pl, 'inventario', $_POST['inventory']);
            if (isset($_POST['equipped']))  setPlayerStat($pl, 'equipado', $_POST['equipped']);
        }
    }
    header('Location: battle.php?step=turn'); exit;

// 3) initiative atual
case 'turn':
    $b     = &$_SESSION['battle'];
    $order = $b['order'];
    $cur = $order[$b['init_index'] % count($order)];
    $stats = getPlayer($cur);
    $notes = array_merge(
    ['efeito' => '', 'posição' => '', 'concentrado' => 0],
            $b['notes'][$cur] ?? []
    );
    $maxMulti = 1 + intdiv(max($stats['H'],0), 2);

    echo '<h1>Iniciativa de <strong>'.$cur.'</strong> <small>(Rodada '.$b['round'].')</small></h1>';
    // Stats e inventário
    echo '<h2>Stats & Inventário</h2>';
    echo '<form method="post" action="?step=update_stats&player='.urlencode($cur).'">';
    foreach (['F','H','R','A','PdF','PV','PM','PE'] as $c) {
        $v = htmlspecialchars($stats[$c] ?? '', ENT_QUOTES);
        echo $c.': <input type="number" name="stats['.$c.']" value="'.$v.'" required><br>';
    }
    echo 'Inventário:<br><textarea name="inventory" rows="4" cols="60">'.htmlspecialchars($stats['inventario'] ?? '', ENT_QUOTES).'</textarea><br>';
    echo 'Equipado:<br><textarea name="equipped" rows="2" cols="60">'.htmlspecialchars($stats['equipado'] ?? '', ENT_QUOTES).'</textarea><br>';
    echo '<button>Salvar Stats</button></form>';

           // Form de ações
           echo '<h2>Ações</h2><form method="post" action="?step=act" id="actionForm">'
           .'<input type="hidden" name="player" value="'.htmlspecialchars($cur,ENT_QUOTES).'">'
           .'Efeito:<br><textarea name="efeito" rows="4" cols="60">'.htmlspecialchars($notes['efeito'],ENT_QUOTES).'</textarea><br>'
           .'Posição:<br><textarea name="posição" rows="2" cols="30">'.htmlspecialchars($notes['posição'],ENT_QUOTES).'</textarea><br>'
           .'Concentrado: <input type="number" name="concentrado" min=0 value="'.htmlspecialchars($notes['concentrado'],ENT_QUOTES).'"><br>'
           .'<select id="action" name="action" onchange="onAct()">';
            echo '<option value="pass">Passar Iniciativa</option>';
            echo '<option value="fim">Terminar Batalha</option>';

            if ($notes['concentrado'] == 0) {
                echo '<option value="start_concentrar">Iniciar Concentração</option>';
            } else {
                echo '<option value="start_concentrar">Continuar Concentração (+1)</option>';
                echo '<option value="release_concentrar" data-bonus="'.$notes['concentrado'].'">'
                   .'Liberar Concentração (bônus: +'.$notes['concentrado'].')</option>';
            }

            echo '<option value="ataque">Atacar</option>';
            echo '<option value="multiple">Múltiplo</option>';
            echo '</select><br>';
    
            // Ataque simples
            echo '<div id="atkSimple" style="display: none;"><fieldset><legend>Ataque</legend>'
            .'Tipo: <select name="atkType"><option>F</option><option>PdF</option></select><br>'
            .'Roll FA: <input id="dadoFA" type="number" name="dadoFA" required><br>'
            .'Alvo: <select name="target">';
            foreach ($order as $o) if ($o !== $cur) echo '<option>'.$o.'</option>';
            echo '</select><br>'
            .'Reação: <select id="def" name="defesa" onchange="onDef()">'
            .'<option value="defender">Defender</option>'
            .'<option value="defender_esquiva">Esquivar</option>'
            .'<option value="indefeso">Indefeso</option>'
            .'</select><br>'
            .'<label id="fdLbl">Roll FD/Esq.: <input id="dadoFD" type="number" name="dadoFD" required></label><br>'
            .'</fieldset></div>';
    
            // Ataque múltiplo
            echo '<div id="atkMulti" style="display:none"><fieldset><legend>Múltiplo</legend>'
            .'Tipo: <select name="atkTypeMulti"><option>F</option><option>PdF</option></select><br>'
            .'Quantidade (2-'.$maxMulti.'): <input id="quant" type="number" name="quant" '
            .'min="2" max="'.$maxMulti.'" value="2" onchange="gen()"><br>'
            .'Alvo: <select name="targetMulti">';
            foreach ($order as $o) if ($o !== $cur) echo '<option>'.$o.'</option>';
            echo '</select><br>'
            .'Reação: <select id="defM" name="defesaMulti" onchange="onDefM()">'
            .'<option value="defender">Defender</option>'
            .'<option value="defender_esquiva">Esquivar</option>'
            .'<option value="indefeso">Indefeso</option>'
            .'</select><br>'
            .'<div id="dCont"></div>'
            .'</fieldset></div>';

    echo '<button type="submit">Executar</button> <button type="button" onclick="history.back()">Voltar</button></form>';
    

            // Resumo Parcial
            echo '<h2>Resumo Parcial da Batalha</h2>';
            echo '<form method="post" action="?step=save_partial">';
            foreach ($b['players'] as $pl) {
                if ($pl === $cur) continue;
                $ps   = getPlayer($pl);
                $note = $b['notes'][$pl] ?? ['efeito'=>'','posição'=>''];
                $id   = preg_replace('/\W+/', '_', $pl); // id seguro
            
                echo '<fieldset style="margin:0.5em 0;padding:0.5em;border:1px solid #ccc">';
                echo '<legend><strong>'.$pl.'</strong></legend>';
                echo '<input type="hidden" name="player_names[]" value="'.$pl.'">';
            
                foreach (['F','H','R','A','PdF','PV','PM','PE'] as $c) {
                    $val = (int)($ps[$c] ?? 0);
                    echo '<label style="display:block">'.$c.': '
                       .'<input type="number" name="partial_stats['.$pl.']['.$c.']" '
                       .'value="'.$val.'" required></label>';
                }
            
                echo '<button type="button" onclick="toggleSection(\'sec_'.$id.'\')">'
                   .'Mostrar/Ocultar Detalhes</button>';
            
                echo '<div id="sec_'.$id.'" style="display:none;margin-top:.5em;">';
            
                echo 'Inventário:<br>'
                   .'<textarea name="partial_stats['.$pl.'][inventario]" rows="4" cols="60">'
                   .htmlspecialchars($ps['inventario'] ?? '', ENT_QUOTES)
                   .'</textarea><br>';
            
                echo 'Equipado:<br>'
                   .'<textarea name="partial_stats['.$pl.'][equipado]" rows="2" cols="60">'
                   .htmlspecialchars($ps['equipado'] ?? '', ENT_QUOTES)
                   .'</textarea><br>';
            
                echo 'Efeito:<br>'
                   .'<textarea name="partial_stats['.$pl.'][efeito]" rows="4" cols="60">'
                   .htmlspecialchars($note['efeito'], ENT_QUOTES)
                   .'</textarea><br>';
            
                echo 'Posição:<br>'
                   .'<textarea name="partial_stats['.$pl.'][posição]" rows="2" cols="30">'
                   .htmlspecialchars($note['posição'], ENT_QUOTES)
                   .'</textarea><br>';
            
                echo '</div>';
                echo '</fieldset>';
            }
            echo '<button type="submit">Salvar Resumo Parcial</button></form>';
    

echo <<<JS
    <script>
    function toggleSection(id) {
    const el = document.getElementById(id);
        if (!el) return;
       el.style.display = (el.style.display === 'none') ? 'block' : 'none';
    }
    document.addEventListener('DOMContentLoaded', () => {
      
      const actionSel = document.getElementById('action');
      const atkSimple = document.getElementById('atkSimple');
      const atkMulti  = document.getElementById('atkMulti');
      const dCont     = document.getElementById('dCont');
      const defM      = document.getElementById('defM');
      const def       = document.getElementById('def');
      const fdLbl     = document.getElementById('fdLbl');
      const faInput   = document.querySelector('input[name="dadoFA"]');
      const fdInput   = document.querySelector('input[name="dadoFD"]');
      
      const quantInput = document.getElementById('quant');
      quantInput.addEventListener('input', () => {
        gen();
        document.querySelectorAll('#dCont input').forEach(i => i.required = true);
      });
    
      function onAct() {
        const act = actionSel.value;
        const bonus = parseInt(
            actionSel.options[actionSel.selectedIndex].dataset.bonus || '0',
            10
        );
    
        const showSimple = (act === 'ataque' || act === 'release_concentrar');
        const showMulti  = (act === 'multiple');

        // mostra/oculta blocos
        atkSimple.style.display = showSimple ? 'block' : 'none';
        atkMulti.style.display  = showMulti  ? 'block' : 'none';
    
        // configura required apenas quando visível
        faInput.required = showSimple;
        fdInput.required = showSimple;

        if (act === 'release_concentrar') {
            faInput.placeholder = '+' + bonus + ' de atk concentrado';;
        } else {
            faInput.value = '';
            faInput.placeholder = '';
        }
    
        if (showMulti) {
          gen();
          document.querySelectorAll('#dCont input').forEach(i => i.required = true);
        } else {
          clearMultiInputs();
        }
    
        // mostra FD label apenas para ataque simples não indefeso
        fdLbl.style.display = (showSimple && def.value !== 'indefeso') ? 'block' : 'none';
      }
    
      function clearMultiInputs() {
        dCont.innerHTML = '';
      }

      function gen() {
        const cnt = parseInt(document.getElementById('quant').value, 10) || 0;
        let html = '';
        for (let i = 1; i <= cnt; i++) {
            html += '<label>Roll FA ' + i + ': <input type="number" name="dadosMulti[]" required></label><br>';
        }
        html += '<label>Roll FD/Esq.: <input type="number" name="dadoFDMulti" required></label><br>';
        dCont.innerHTML = html;
      }   

      actionSel.addEventListener('change', onAct);
      def.addEventListener('change', onAct);
      defM.addEventListener('change', onAct);
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


        foreach (listPlayerTraits($pl) as $traitKey) {
            $fn = "apply_{$traitKey}";
            if (function_exists($fn)) {
                $fn($pl, $_POST);
            }
        }
            switch ($_POST['action'] ?? '') {
                
                case 'pass':
                    $out = "<strong>{$pl}</strong> passou seu turno.";
                break;
                
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
                    $tipo  = $_POST['atkTypeMulti']   ?? 'F';
                    $tgt   = $_POST['targetMulti']    ?? '';
                    $q     = (int)($_POST['quant']    ?? 1);
                    $dados = $_POST['dadosMulti']      ?? [];
                    $dFD   = (int)($_POST['dadoFDMulti'] ?? 0);
                    $def   = $_POST['defesaMulti']     ?? 'defender';
                    
                    $faTot = FAmulti($pl, $q, $tipo, $dados);
                    
                    if ($def === 'indefeso' || $def === 'defender_esquiva') {
                        $dano = max($faTot - FDindefeso($tgt), 0);
                    } else {
                        $dano = max($faTot - FD($tgt, $dFD), 0);
                    }
                    
                    setPlayerStat($tgt, 'PV', max(getPlayerStat($tgt,'PV') - $dano, 0));
                    $out = "<strong>{$pl}</strong> fez ataque múltiplo em <strong>{$tgt}</strong> ({$q}x{$tipo}): FA total = {$faTot}, dano = {$dano}";
                    break;

                    case 'start_concentrar':
                        if (empty($b['notes'][$pl]['concentrado'])) {
                            $b['notes'][$pl]['concentrado'] = 1;
                        }
                        $out = "<strong>{$pl}</strong> iniciou/concentra (rodada atual: +{$b['notes'][$pl]['concentrado']})";
                break;         

                case 'release_concentrar':
                    $bonus = $b['notes'][$pl]['concentrado'] ?? 0;
                    $dado  = (int)($_POST['dadoFA'] ?? 0);
                    $tipo  = $_POST['atkType']   ?? 'F';
                    $tgt   = $_POST['target']    ?? '';

                    $fa_normal = FA($pl, $tipo, $dado);

                    $fa_total = $fa_normal + $bonus;
                                    
                    $defesa = (int)($_POST['dadoFD'] ?? 0);
                    $dano   = max($fa_total - FD($tgt, $defesa), 0);

                    setPlayerStat($tgt, 'PV', max(getPlayerStat($tgt,'PV') - $dano, 0));
                    $b['notes'][$pl]['concentrado'] = 0;
                    $out = "<strong>{$pl}</strong> liberou ataque (FA={$fa_normal} + bônus {$bonus} = {$fa_total}): dano = {$dano}";
                    break;

                case 'fim':
                    header('Location: battle.php?step=final'); exit;
                default:
                    $out = 'Ação inválida ou não reconhecida.';
                    break;
            }
            $total   = count($b['order']);
           
            $b['init_index']++;

            if ($b['init_index'] % $total === 0) {
                $b['round']++;
                foreach ($b['notes'] as $player => &$note) {
                    if (!empty($note['concentrado'])) {
                        $note['concentrado']++;
                    }
                }
                unset($note);
            }

            echo '<p>'.$out.'</p><p><a href="battle.php?step=turn">Próximo Turno</a></p>';
        } else {
            echo '<p>Nenhum dado recebido.</p><p><a href="battle.php?step=turn">Voltar</a></p>';
        }
        exit;

        // 4.5) Salvar Resumo Parcial
        case 'save_partial':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $names        = $_POST['player_names']   ?? [];
                $partialStats = $_POST['partial_stats']  ?? [];
                $partialNotes = $_POST['partial_stats']  ?? [];
            
                foreach ($names as $pl) {
                    if (isset($partialStats[$pl])) {
                        foreach ($partialStats[$pl] as $campo => $valor) {
                            if (in_array($campo, ['F','H','R','A','PdF','PV','PM','PE'], true)) {
                                setPlayerStat($pl, $campo, (int)$valor);
                            }
                            elseif (in_array($campo, ['inventario','equipado'], true)) {
                                setPlayerStat($pl, $campo, $valor);
                            }
                        }
                    }
                    if (isset($partialStats[$pl]['efeito'])) {
                        $_SESSION['battle']['notes'][$pl]['efeito'] = $partialStats[$pl]['efeito'];
                    }
                    if (isset($partialStats[$pl]['posição'])) {
                        $_SESSION['battle']['notes'][$pl]['posição'] = $partialStats[$pl]['posição'];
                    }
                }
            }
            header('Location: battle.php?step=turn');
            exit;

    // 5) Tela Final
    case 'final':
        $b       = &$_SESSION['battle'];
        $players = $b['players'];
        echo '<h1>Resumo da Batalha</h1><form method="post" action="battle.php?step=save_final">';
        foreach ($players as $pl) {
            $stats = getPlayer($pl);
            echo '<fieldset style="margin-bottom:1em;padding:1em;border:1px solid #ccc">';
            echo '<legend><strong>'.$pl.'</strong></legend>';
            echo '<input type="hidden" name="player_names[]" value="'.$pl.'">';
            foreach (['F','H','R','A','PdF','PV','PM','PE'] as $c) {
                    echo '<label style="display:block;margin:0.5em 0">'.$c.': <input type="number" name="stats['.$pl.']['.$c.']" value="'.(int)$stats[$c].'" required></label>';
                }
                // Inventário e Equipado na Tela Final
                $invF = htmlspecialchars($stats['inventario'] ?? '', ENT_QUOTES);
                $eqF  = htmlspecialchars($stats['equipado']   ?? '', ENT_QUOTES);
                echo 'Inventário:<br><textarea name="stats['.$pl.'][inventario]" rows="4" cols="60">'.$invF.'</textarea><br>';
                // exemplo para Turno
                echo 'Equipado:<br><textarea name="equipped" rows="2" cols="60">'.$eqF.'</textarea><br>';
                echo '</fieldset>';
            }
            $_SESSION['battle']['init_index'] = 0;
            $_SESSION['battle']['round']      = 1;
            $_SESSION['battle']['notes']      = [];
            echo '<button type="submit">Salvar Alterações</button> <a href="index.php">Nova Batalha</a></form>';
            exit;

    // 6) Salvar Final
    case 'save_final':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $names    = $_POST['player_names'] ?? [];
            $allStats = $_POST['stats']        ?? [];
            foreach ($names as $pl) {
                if (isset($allStats[$pl])) {
                    foreach ($allStats[$pl] as $campo => $valor) {
                        setPlayerStat($pl, $campo, ($campo === 'inventario' || $campo === 'equipado') ? $valor : (int)$valor);
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