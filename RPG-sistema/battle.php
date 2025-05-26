<?php
// battle.php - Sistema de Combate 3D&T
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
        $_SESSION['battle'] = [
            'players'     => $selected,
            'order'       => [],
            'init_index'  => 0,
            'round'       => 1,
            'notes'       => [],
        ];
        $_SESSION['battle']['hasAlly'] = []; 
  
        header('Location: battle.php?step=initiative'); exit;
    }     
    $all = getAllPlayers();
    $exclude = getAllAllies(); 
    echo '<h1>Iniciar Batalha</h1><form method="post">';

    foreach ($all as $p) {  
            $n = $p['nome'];
            if (in_array($n, $exclude, true)) {
                continue;
            }   
            $safe = htmlspecialchars($n, ENT_QUOTES);         
            echo '<label><input type="checkbox" name="players[]" value="'.$n.'"> '.$n.'</label><br>';
    }  
    echo '<button type="submit">Confirmar</button></form>';
    break;
 

// 2) Iniciativa
case 'initiative':
    $b     = &$_SESSION['battle'];
    $rolls = $_POST['rolls'] ?? [];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // 1) Se veio teste de Assombrado, processe-o primeiro
        if (!empty($_POST['roll_assombrado'])) {
            foreach ($b['players'] as $i => $pl) {
                if (isset($_POST['roll_assombrado'][$i])) {
                    // Guarda stats originais na primeira vez
                    if (!isset($b['orig'][$pl])) {
                        $b['orig'][$pl] = [];
                        foreach (['F','H','R','A','PdF'] as $s) {
                            $b['orig'][$pl][$s] = getPlayerStat($pl, $s);
                        }
                    }
                    // Aplica Desvantagem Assombrado
                    $msg = assombrado($pl, [
                        'roll_assombrado' => $_POST['roll_assombrado'][$i]
                    ]);
                    if ($msg !== null) {
                        $b['notes'][$pl]['efeito']     = $msg;
                        $b['notes'][$pl]['assombrado'] = true;
                    }
                }
            }
        }

        $inicList     = iniciativa($b['players'], $rolls);
        $b['order']   = array_column($inicList, 'nome');

        header('Location: battle.php?step=turn');
        exit;
    }

    echo '<h1>Iniciativa</h1>
          <form method="post" action="battle.php?step=initiative">';
    foreach ($b['players'] as $i => $nome) {
        echo '<label>'.htmlspecialchars($nome, ENT_QUOTES).':
                <input type="number" name="rolls['.$i.']" required>
              </label><br>';
        if (in_array('assombrado', listPlayerTraits($nome), true)) {
            echo '<label>'.htmlspecialchars($nome, ENT_QUOTES).' Assombrado (1–6), dado:
                    <input type="number" name="roll_assombrado['.$i.']" min="1" max="6" required>
                  </label><br>';
        }
    }
    echo '<button type="submit">Ok</button>
          </form>';
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
    $b = &$_SESSION['battle'];
    if (!empty($b['needs_reload'])) {
        $b['needs_reload'] = false;
        echo '<script>window.location.reload();</script>';
        exit;
    }

    $order = $b['order'];
    if (count($order) === 0) {
        echo "<p>Nenhum lutador na batalha.</p>";
        exit;
    }

    if (! empty($_SESSION['battle']['playingAlly'])) {
        $cur = $_SESSION['battle']['playingAlly'];
    } else {
        $cur = $order[$b['init_index'] % count($order)];
    }



    $stats = getPlayer($cur);
    $notes = array_merge(
        ['efeito'=>'','posição'=>'','concentrado'=>0,'draco_active'=>false,'incorp_active'=>false],
        $b['notes'][$cur] ?? []
    );
    $b['notes'][$cur] = $notes;
    $maxMulti = 1 + intdiv(max($stats['H'],0), 2);
    $isIncorp = ! empty($notes['incorp_active']);  



    if ($notes['draco_active']) {
        if (spendPM($cur, 1)) {} else {
            draconificacao($cur, false);
            $b['notes'][$cur]['draco_active'] = false;
            $b['notes'][$cur]['efeito'] .= "\nDraconificação desativada (PM esgotado) e Instável.";
        }
    }

    if (!empty($notes['fusao_active'])) {
        $curPV = getPlayerStat($cur, 'PV');
        $curR  = getPlayerStat($cur, 'R');
        if ($curPV <= $curR) {            
            $itensARemover = [
                'Em Fúria até o fim da batalha (Não pode usar magia nem esquiva).'
            ];
            $b['notes'][$cur]['efeito'] = removeEffect($b['notes'][$cur]['efeito'], $itensARemover);              
            $b['notes'][$cur]['furia'] = true;
            $b['notes'][$cur]['efeito'] .= "\nEm Fúria até o fim da batalha (Não pode usar magia nem esquiva).";
        }
    }

    if (! empty($notes['extra_energy_next'])) {
        energiaExtra($cur);
        $b['notes'][$cur]['efeito'] .= "\nEnergia extra aplicada: PVs restaurados ao máximo.";
        unset($b['notes'][$cur]['extra_energy_next']);
    } else {
        $itensARemover = [
            'Energia extra aplicada: PVs restaurados ao máximo.'
        ];
        $b['notes'][$cur]['efeito'] = removeEffect($b['notes'][$cur]['efeito'], $itensARemover); 
    }

    if (in_array('furia', listPlayerTraits($cur), true)) {
        $curPV = getPlayerStat($cur, 'PV');
        $curR  = getPlayerStat($cur, 'R');
        if ($curPV <= $curR) {
            $b['notes'][$cur]['furia'] = true;
            $itensARemover = [
                'Em Fúria até o fim da batalha (Não pode usar magia nem esquiva).'
            ];
            $b['notes'][$cur]['efeito'] = removeEffect($b['notes'][$cur]['efeito'], $itensARemover);             
            $b['notes'][$cur]['efeito'] .= "\nEm Fúria até o fim da batalha (Não pode usar magia nem esquiva).";
        }
    };

    if (in_array('invulnerabilidade_fogo', listPlayerTraits($cur), true)) {
        $itensARemover = [
            'Invulnerabilidade a fogo (dano por fogo dividido por 10).'
        ];
        $b['notes'][$cur]['efeito'] = removeEffect($b['notes'][$cur]['efeito'], $itensARemover);           
        $b['notes'][$cur]['efeito'] .= "\nInvulnerabilidade a fogo (dano por fogo dividido por 10).";
    };

    if (in_array('codigo_da_derrota', listPlayerTraits($cur), true)) {
        $itensARemover = [
            'Lute até a morte.'
        ];
        $b['notes'][$cur]['efeito'] = removeEffect($b['notes'][$cur]['efeito'], $itensARemover);          
        $b['notes'][$cur]['efeito'] .= "\nLute até a morte.";
    };
    


    if (!empty($_SESSION['battle']['playingPartner'][$cur])){
        echo '<h1>Iniciativa da dupla <strong>'.$cur.' & '.$_SESSION['battle']['playingPartner'][$cur]['name'].'</strong> <small>(Rodada '.$b['round'].')</small></h1>';
    } else {
    echo '<h1>Iniciativa de <strong>'.$cur.'</strong> <small>(Rodada '.$b['round'].')</small></h1>';
    }
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
        echo '<h2>Ações</h2>';
        // if (!empty($_SESSION['battle']['agarrao'][$cur])){

            echo '<form method="post" action="battle.php?step=act" id="actionForm">'
           .'<input type="hidden" name="player" value="'.htmlspecialchars($cur,ENT_QUOTES).'">'
           .'Efeito:<br><textarea name="efeito" rows="4" cols="60">'.htmlspecialchars($notes['efeito'],ENT_QUOTES).'</textarea><br>'
           .'Posição:<br><textarea name="posição" rows="2" cols="30">'.htmlspecialchars($notes['posição'],ENT_QUOTES).'</textarea><br>'
           .'Concentrado: <input type="number" name="concentrado" min=0 value="'.htmlspecialchars($notes['concentrado'],ENT_QUOTES).'"><br>'
           .'<select id="actionSelect" name="action">';
            echo '<option value="pass">Passar Iniciativa</option>';
            echo '<option value="fim">Terminar Batalha</option>';


            if (in_array('energia_vital', listPlayerTraits($cur), true)) {
                if (empty($notes['use_pv'])) {
                    echo '<option value="enable_use_pv">Usar PV em vez de PM</option>';
                } else {
                    echo '<option value="disable_use_pv">Voltar a usar PM normalmente</option>';
                }
            }            

            if (in_array('incorporeo', listPlayerTraits($cur), true)) {
                if (!$notes['incorp_active']) {
                    echo '<option value="activate_incorp">Tornar‑se Incorpóreo (2 PM)</option>';
                } else {
                    echo '<option value="deactivate_incorp">Reverter Incorporeo</option>';
                }
            }            
            
            if (in_array('draconificacao', listPlayerTraits($cur), true)) {
                if (!$notes['draco_active']) {
                    echo '<option value="activate_draco">Ativar Draconificação (1PM/turno)</option>';
                } else {
                    echo '<option value="deactivate_draco">Desativar Draconificação (ação extra)</option>';
                }
            }

            if (in_array('fusao_eterna', listPlayerTraits($cur), true)) {
                if (empty($notes['fusao_active'])) {
                    echo '<option value="activate_fusao">Ativar Fusão Eterna (3PM)</option>';
                } else {
                    echo '<option value="deactivate_fusao">Desativar Fusão Eterna</option>';
                }
            }

            if ($notes['concentrado'] == 0) {
                echo '<option value="start_concentrar">Iniciar Concentração</option>';
            } else {
                echo '<option value="start_concentrar">Continuar Concentração (+1)</option>';
                echo '<option value="release_concentrar" data-bonus="'.$notes['concentrado'].'">'
                   .'Liberar Concentração (bônus: +'.$notes['concentrado'].')</option>';
            }

            if (in_array('energia_extra', listPlayerTraits($cur), true)) {
                echo '<option value="extra_energy">Usar Energia Extra</option>';
            } 

            if (in_array('aliado', listPlayerTraits($cur), true) && empty($_SESSION['battle']['playingAlly']) && empty($_SESSION['battle']['playingPartner'][$cur])){
                echo '<option value="use_ally">Jogar com Aliado</option>';
            }            

            if ($cur == $_SESSION['battle']['playingAlly']) {
                echo '<option value="back_to_owner">Voltar ao Dono</option>';   
            }  
            
            if (in_array('parceiro', listPlayerTraits($cur), true) && empty($_SESSION['battle']['playingPartner'][$cur]) && in_array('aliado', listPlayerTraits($cur), true)) {
                echo '<option value="start_partner">Formar Dupla com Parceiro</option>';
            }

            if(!empty($_SESSION['battle']['playingPartner'][$cur]) && in_array('parceiro', listPlayerTraits($cur), true)){
                echo '<option value="end_partner">Separar Dupla</option>';
            }


            if (!empty($_SESSION['battle']['agarrao'][$cur]['agarrando'])){
                 echo '<option value="soltar_agarrao">Soltar agarrão ('.$_SESSION['battle']['agarrao'][$cur]['agarrando'].')</option>';
            }

            if (!empty($_SESSION['battle']['agarrao'][$cur]['agarrado'])){
                echo '<option value="se_soltar_agarrao">Se soltar do agarrão ('.$_SESSION['battle']['agarrao'][$cur]['agarrado'].')</option>';
            }


            echo '<option value="ataque">Atacar</option>';
            echo '<option value="multiple">Múltiplo</option>';
            if (in_array('tiro_multiplo', listPlayerTraits($cur)) || 
            (in_array('tiro_multiplo', listPlayerTraits(getAlliePlayer($cur))) && 
            in_array('ligacao_natural', listPlayerTraits(getAlliePlayer($cur)))) ||
            (!empty($_SESSION['battle']['playingPartner'][$cur]) &&
            in_array('tiro_multiplo', listPlayerTraits($_SESSION['battle']['playingPartner'][$cur]['name']))))
            {
                echo '<option value="tiro_multiplo">Tiro Múltiplo</option>';
            }
            
            if (in_array('agarrao', listPlayerTraits($cur)) || 
            (in_array('agarrao', listPlayerTraits(getAlliePlayer($cur))) && 
            in_array('ligacao_natural', listPlayerTraits(getAlliePlayer($cur)))) ||
            (!empty($_SESSION['battle']['playingPartner'][$cur]) &&
            in_array('agarrao', listPlayerTraits($_SESSION['battle']['playingPartner'][$cur]['name']))))
            {
                echo '<option value="agarrao">Agarrão</option>';
            }

            if (in_array('ataque_debilitante', listPlayerTraits($cur)) || 
            (in_array('ataque_debilitante', listPlayerTraits(getAlliePlayer($cur))) && 
            in_array('ligacao_natural', listPlayerTraits(getAlliePlayer($cur)))) ||
            (!empty($_SESSION['battle']['playingPartner'][$cur]) &&
            in_array('ataque_debilitante', listPlayerTraits($_SESSION['battle']['playingPartner'][$cur]['name']))))
            {
                echo '<option value="ataque_debilitante">Ataque Debilitante</option>';
            }

            echo '</select><br>';


            $validTargets = [];
            foreach ($order as $o) {
                if ($o === $cur) continue;
                $targetNotes   = $b['notes'][$o] ?? [];
                $targetIncorp  = !empty($targetNotes['incorp_active']);
                if ($isIncorp === $targetIncorp) {
                    $validTargets[] = $o;
                }
            }

            $allies = getAllAllies();
            foreach ($allies as $ally) {
                if ($ally === $cur) continue;
                $targetNotes  = $b['notes'][$ally] ?? [];
                $targetIncorp = !empty($targetNotes['incorp_active']);
                if ($isIncorp === $targetIncorp) {
                    $validTargets[] = $ally;
                }
            }

            $hasTargets = count($validTargets) > 0;            


            // Ataque simples
            if($hasTargets){
                echo '<div id="atkSimple" style="display: none;"><fieldset><legend>Ataque</legend>'
                .'Tipo: <select name="atkType"><option>F</option><option>PdF</option></select><br>'
                .'Roll FA: <input id="dadoFA" type="number" name="dadoFA" required><br>'
                .'Alvo: <select name="target">';
                foreach ($validTargets as $tgt) if ($tgt !== $cur) {
                    $isFuria = ! empty($b['notes'][$tgt]['furia']);
                    echo '<option value="'.htmlspecialchars($tgt).'" data-furia="'.($isFuria ? '1' : '0').'">'.htmlspecialchars($tgt).'</option>';                               
                }
                echo '</select><br>'
                .'Reação: <select id="def" name="defesa">'
                .'<option value="defender">Defender</option>'
                .'<option id="opt-esquiva-simples" value="defender_esquiva">Esquivar</option>'
                .'<option value="indefeso">Indefeso</option>'
                .'</select><br>'
                .'<label id="fdLbl">Roll FD/Esq.: <input id="dadoFD" type="number" name="dadoFD" required></label><br>'
                .'</fieldset></div>';
            }
    
            // Ataque múltiplo
            if($hasTargets){
                echo '<div id="atkMulti" style="display:none"><fieldset><legend>Múltiplo</legend>'
                .'Tipo: <select name="atkTypeMulti"><option>F</option><option>PdF</option></select><br>'
                .'Quantidade (2-'.$maxMulti.'): <input id="quant" type="number" name="quant" '
                .'min="2" max="'.$maxMulti.'" value="2"><br>'
                .'Alvo: <select name="targetMulti">';
                foreach ($validTargets as $tgt) if ($tgt !== $cur) {
                    $isFuria = ! empty($b['notes'][$tgt]['furia']);
                    echo '<option value="'.htmlspecialchars($tgt).'" data-furia="'.($isFuria ? '1' : '0').'">'.htmlspecialchars($tgt).'</option>';                               
                }
                echo '</select><br>'
                .'Reação: <select id="defM" name="defesaMulti">'
                .'<option value="defender">Defender</option>'
                .'<option id="opt-esquiva-multi" value="defender_esquiva">Esquivar</option>'
                .'<option value="indefeso">Indefeso</option>'
                .'</select><br>'
                .'<div id="dCont"></div>'
                .'</fieldset></div>';
            }

            //Tiro múltiplo
            if($hasTargets){
                echo '<div id="atkTiroMulti" style="display:none"><fieldset><legend>Tiro Múltiplo</legend>'
                .'Tipo: <select name="atkTypeTiroMulti"><option>PdF</option></select><br>'
                .'Quantidade (1-'.$stats['H'].'): <input id="quantTiro" type="number" name="quantTiro" '
                .'min="1" max="'.$stats['H'].'" value="1"><br>'
                .'Alvo: <select name="targetTiroMulti">';
                foreach ($validTargets as $tgt) if ($tgt !== $cur) {
                    $isFuria = ! empty($b['notes'][$tgt]['furia']);
                    echo '<option value="'.htmlspecialchars($tgt).'" data-furia="'.($isFuria ? '1' : '0').'">'.htmlspecialchars($tgt).'</option>';                               
                }
                echo '</select><br>'
                .'Reação: <select id="defTiro" name="defesaTiroMulti">'
                .'<option value="defender">Defender</option>'
                .'<option id="opt-esquiva-tiro" value="defender_esquiva">Esquivar</option>'
                .'<option value="indefeso">Indefeso</option>'
                .'</select><br>'
                .'<div id="dContTiro"></div>'
                .'<label id="fdTiroLbl">Roll FD/Esq.: '
                .'<input id="dadoFDTiro" type="number" name="dadoFDTiro" required>'
                .'</label><br>'
                .'</fieldset></div>';
            }

            //Aliado
            echo '<div id="allySelect" style="display:none;margin-top:0.5em;">'
            . '<fieldset><legend>Selecione o Parceiro</legend>'
            . '<select name="ally">';
            foreach (getPlayerAllies($cur) as $p) {
                echo '<option value="'.htmlspecialchars($p, ENT_QUOTES).'">'
                .htmlspecialchars($p, ENT_QUOTES)
                .'</option>';
            }
            echo '</select>'
            . '</fieldset>'
            . '</div>';


            //Parceiro
            echo '<div id="partnerSelect" style="display:none;margin-top:0.5em;">'
            . '<fieldset><legend>Selecione o Parceiro</legend>'
            . '<select name="partner">';
            foreach (getPlayerPartner($cur) as $p) {
                echo '<option value="'.htmlspecialchars($p, ENT_QUOTES).'">'
                .htmlspecialchars($p, ENT_QUOTES)
                .'</option>';
            }
            echo '</select>'
            . '</fieldset>'
            . '</div>';


            //Agarrão
            if($hasTargets){
                echo '<div id="atkAgarrao" style="display:none;"><fieldset><legend>Agarrão</legend>'
                .'Alvo: <select name="targetAgarrao">';
                foreach ($validTargets as $tgt) if ($tgt !== $cur) {
                    echo '<option value="'.htmlspecialchars($tgt).'">'
                    .htmlspecialchars($tgt)
                    .'</option>';
                }
                echo '</select><br>'
                .'Roll Teste de Força: <input id="rollAgarrao" type="number" name="rollAgarrao" required><br>'
                .'</fieldset></div>';
            }           


            //Se soltar de um agarrão
            echo '<div id="soltarAgarrao" style="display:none;"><fieldset><legend>Teste para se Soltar do Agarrão</legend>'
            .'Roll Teste de Força: <input id="rollSoltarAgarrao" type="number" name="rollSoltarAgarrao" required><br>'
            .'</fieldset></div>';



            echo '<button type="submit">Executar</button> ';
            echo '<button type="button" onclick="history.back()">Voltar</button>';
            echo '</form>';
    



            
            // Resumo Parcial
            echo '<h2>Resumo Parcial da Batalha</h2>';
            echo '<form method="post" action="?step=save_partial">';
            $lutadores = array_merge($b['players'], getAllAllies());
            foreach ($lutadores as $pl) {
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
            echo '<button type="submit">Salvar Resumo Parcial</button>';
            echo '</form>';
    

echo <<<JS
<script>
document.addEventListener('DOMContentLoaded', () => {
  const actionSel   = document.getElementById('actionSelect');
  
  const atkSimple   = document.getElementById('atkSimple');
  const atkMulti    = document.getElementById('atkMulti');
  const atkTiro     = document.getElementById('atkTiroMulti');
  const atkAgarrao  = document.getElementById('atkAgarrao');
  const soltarAgarr = document.getElementById('soltarAgarrao');

  const defSimple   = document.getElementById('def');
  const defMulti    = document.getElementById('defM');
  const defTiro     = document.getElementById('defTiro');
  
  const fdLblSimple = document.getElementById('fdLbl');
  const fdLblMulti  = document.getElementById('dadoFDMulti')?.parentNode;
  const fdLblTiro   = document.getElementById('fdTiroLbl');
 
  const faInput     = document.getElementById('dadoFA');
  const fdInput     = document.getElementById('dadoFD');

  const quant       = document.getElementById('quant');
  const quantTiro   = document.getElementById('quantTiro');
  
  const dCont       = document.getElementById('dCont');
  const dContTiro   = document.getElementById('dContTiro');
  const dadoFDTiro  = document.getElementById('dadoFDTiro');
  const dadoAgarrao = document.getElementById('rollAgarrao');
  const dSoltAgarra = document.getElementById('rollSoltarAgarrao');

  const selAlvoSi   = document.querySelector('select[name="target"]');
  const selAlvoMu   = document.querySelector('select[name="targetMulti"]');
  const selAlvoTi   = document.querySelector('select[name="targetTiroMulti"]');
  const selAlvoAg   = document.querySelector('select[name="targetAgarrao"]');

  const esqSi    = document.getElementById('opt-esquiva-simples');
  const esqMu    = document.getElementById('opt-esquiva-multi');
  const esqTi    = document.getElementById('opt-esquiva-tiro');
  

  function atualizaEsquiva(sel, esq) {
    if (!sel || !esq) return;
    const furia = sel.selectedOptions[0].dataset.furia;
    esq.style.display = (furia === '1') ? 'none' : '';
  }
  
  function genMulti() {
    let cnt = parseInt(quant.value, 10) || 0;
    let html = '';
    for (let i = 1; i <= cnt; i++) {
      html += '<label>Roll FA ' + i + ': <input type="number" name="dadosMulti[]" required></label><br>';
    }
    html += '<label>Roll FD/Esq.: <input type="number" name="dadoFDMulti" id="dadoFDMulti" required></label><br>';
    dCont.innerHTML = html;
  }

  function genTiro() {
    let cnt = parseInt(quantTiro.value, 10) || 0;
    let html = '';
    for (let i = 1; i <= cnt; i++) {
      html += '<label>Roll PdF ' + i + ': <input type="number" name="dadosTiroMulti[]" required></label><br>';
    }
    dContTiro.innerHTML = html;
  }

  window.toggleSection = function(id) {
    const el = document.getElementById(id);
    if (!el) return;
    el.style.display = (el.style.display === 'none') ? 'block' : 'none';
  };

  function onAct() {
    const act = actionSel.value;
    const tiroDiv   = document.getElementById('atkTiroMulti');
    const tiroInput = document.getElementById('dadoFDTiro');

    atkSimple.style.display = (act === 'ataque' || act === 'release_concentrar') ? 'block' : 'none';
    atkMulti.style.display  = (act === 'multiple')   ? 'block' : 'none';
    atkAgarrao.style.display = (act === 'agarrao') ? 'block' : 'none';
    soltarAgarr.style.display = (act === 'se_soltar_agarrao') ? 'block' : 'none';

    if (act === 'tiro_multiplo') {
        tiroDiv.style.display  = 'block';
        tiroInput.disabled     = false;
    } else {
        tiroDiv.style.display  = 'none';
        tiroInput.disabled     = true;
    }

    if (act === 'tiro_multiplo') {
      const modo = defTiro.value;
      if (modo === 'indefeso') {
        fdLblTiro.style.display  = 'none';
        dadoFDTiro.required      = false;
        dadoFDTiro.readOnly      = true;
      }
      else {
        fdLblTiro.style.display  = 'block';
        dadoFDTiro.required      = true;
        dadoFDTiro.readOnly      = false;
      }
    }


    rollAgarrao.required = false;  // garante que não esteja marcado por engano
    if (act === 'agarrao') {
      dadoAgarrao.required = true;
      dadoAgarrao.disabled = false;
    } else {
      dadoAgarrao.required = false;
      dadoAgarrao.disabled = true;
    }

    // Soltar agarrão
    if (act === 'se_soltar_agarrao') {
      dSoltAgarra.required = true;
      dSoltAgarra.disabled = false;
    } else {
      dSoltAgarra.required = false;
      dSoltAgarra.disabled = true;
    }

    

    rollAgarrao.required     = (act === 'agarrao');
    dSoltAgarra.required     = (act === 'agarrao');
    faInput.required = (act === 'ataque' || act === 'release_concentrar');
    const needFDSimple = (['ataque', 'release_concentrar'].includes(act) && defSimple.value !== 'indefeso');
    fdInput.required           = needFDSimple;
    fdLblSimple.style.display  = needFDSimple ? 'block' : 'none';

    if (act === 'multiple') { genMulti(); } else { dCont.innerHTML = ''; }
    if (fdLblMulti) {
      fdLblMulti.style.display = (act === 'multiple' && defMulti.value !== 'indefeso') ? 'block' : 'none';
    }

    if (act === 'tiro_multiplo') { genTiro(); } else { dContTiro.innerHTML = ''; }
        fdLblTiro.style.display = (act === 'tiro_multiplo' && defTiro.value !== 'indefeso') ? 'block' : 'none';
  }

  function onDef() {
      const modo = defSimple.value;
      const needFD = (modo !== 'indefeso');
      fdLblSimple.style.display = needFD ? 'block' : 'none';
      fdInput.required         = needFD;
  }
  function onDefM() {
      const modo = defMulti.value;
      if (!fdLblMulti) return;
      fdLblMulti.style.display = (modo !== 'indefeso') ? 'block' : 'none';
  }


  actionSel.addEventListener('change', e => {
    document.getElementById('partnerSelect').style.display = (e.target.value === 'start_partner') ? 'block' : 'none';
    document.getElementById('allySelect').style.display = (e.target.value === 'use_ally') ? 'block' : 'none';
    onAct();
  });



  actionSel.addEventListener('change', onAct);

  selAlvoSi.addEventListener('change', () => atualizaEsquiva(selAlvoSi, esqSi));
  selAlvoMu.addEventListener('change', () => atualizaEsquiva(selAlvoMu, esqMu));
  selAlvoTi.addEventListener('change', () => atualizaEsquiva(selAlvoTi, esqTi));
  
  defSimple.addEventListener('change', onDef);
  defMulti.addEventListener('change', onDefM);
  defTiro.addEventListener('change', onAct);

  quant.addEventListener('input', () => { genMulti(); onAct(); });
  quantTiro.addEventListener('input', () => { genTiro(); onAct(); });

    
  atualizaEsquiva(selAlvoSi, esqSi);
  atualizaEsquiva(selAlvoMu, esqMu);
  atualizaEsquiva(selAlvoTi, esqTi);

  onDef();
  onDefM();
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
            if ($pl && in_array($pl, $b['players'], true)) {
                if (!isset($b['notes'][$pl])) {
                    $b['notes'][$pl] = [];
                }
                $b['notes'][$pl]['efeito']      = $_POST['efeito']     ?? '';
                $b['notes'][$pl]['posição']     = $_POST['posição']    ?? '';
                $b['notes'][$pl]['concentrado'] = (int)($_POST['concentrado'] ?? 0);
            }

            switch ($_POST['action'] ?? '') {


                case 'pass':
                    $out = "<strong>{$pl}</strong> passou seu turno.";
                    $b['init_index']++;
                    unset($_SESSION['battle']['playingAlly']);
                    $_SESSION['battle']['needs_reload'] = true;
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

                    $out = "<strong>{$pl}</strong> atacou <strong>{$tgt}</strong> ({$tipo}): dano = {$dano}";
                    $out .= applyDamage($pl, $tgt, $dano, $tipo, $out);

                    $b['init_index']++;
                    unset($_SESSION['battle']['playingAlly']);
                    $_SESSION['battle']['needs_reload'] = true;
                break;


                case 'multiple':
                    $tipo  = $_POST['atkTypeMulti']   ?? 'F';
                    $tgt   = $_POST['targetMulti']    ?? '';
                    $q     = (int)($_POST['quant']    ?? 1);
                    $dados = $_POST['dadosMulti']      ?? [];
                    $dFD   = (int)($_POST['dadoFDMulti'] ?? 0);
                    $def   = $_POST['defesaMulti']     ?? 'defender';
                    
                    $faTot = FAmulti($pl, $q, $tipo, $dados);

                    if ($def === 'indefeso') {
                        $dano = max($faTot - FDindefeso($tgt), 0);
                    }
                    else if ($def === 'defender_esquiva') {
                        $resEsq = esquivaMulti($pl, $tgt, $dFD);
                        if ($resEsq === 'defender_esquiva_success') {
                            $dano = 0;
                        } else {
                            $dano = max($faTot - FD($tgt, $dFD), 0);
                        }
                    } else {
                        $dano = max($faTot - FD($tgt, $dFD), 0);
                    }

                    $out = "<strong>{$pl}</strong> fez ataque múltiplo em <strong>{$tgt}</strong> ({$q}x{$tipo}): FA total = {$faTot}, dano = {$dano}";
                    $out .= applyDamage($pl, $tgt, $dano, $tipo, $out);

                    $b['init_index']++;
                    unset($_SESSION['battle']['playingAlly']);
                    $_SESSION['battle']['needs_reload'] = true;
                break;


                case 'start_concentrar':
                    if (empty($b['notes'][$pl]['concentrado'])) {
                        $b['notes'][$pl]['concentrado'] = 1;
                    }
                    $out = "<strong>{$pl}</strong> iniciou/concentra (rodada atual: +{$b['notes'][$pl]['concentrado']})";
                    $b['init_index']++;
                    unset($_SESSION['battle']['playingAlly']);
                    $_SESSION['battle']['needs_reload'] = true;
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


                    $out = "<strong>{$pl}</strong> liberou ataque (FA={$fa_normal} + bônus {$bonus} = {$fa_total}): dano = {$dano}";
                    $out .= applyDamage($pl, $tgt, $dano, $tipo, $out);

                    $b['notes'][$pl]['concentrado'] = 0;
                    $b['init_index']++;
                    unset($_SESSION['battle']['playingAlly']);
                    $_SESSION['battle']['needs_reload'] = true;
                break;


                case 'tiro_multiplo':
                    $tgt    = $_POST['targetTiroMulti']   ?? '';
                    $q      = (int)($_POST['quantTiro']   ?? 1);
                    $dados  = $_POST['dadosTiroMulti']    ?? [];
                    $dadoFD = (int)($_POST['dadoFDTiro']  ?? 0);
                    $def    = $_POST['defesaTiroMulti']   ?? 'defender'; 
                
                    if ($def === 'defender_esquiva') {
                        $resultadoEsq = esquivaMulti($pl, $tgt, $dadoFD);
                        if ($resultadoEsq === 'defender_esquiva_success') {
                            $dano = 0;
                        } else {
                            $dano = FAtiroMultiplo($pl, $q, $dados, $tgt, 'indefeso', $dadoFD);
                        }
                    } else {
                        $tipoDef = $def === 'indefeso' ? 'indefeso' : 'defender';
                        $dano    = FAtiroMultiplo($pl, $q, $dados, $tgt, $tipoDef, $dadoFD);
                    }
                    
                    $out = "<strong>{$pl}</strong> usou <em>Tiro Múltiplo</em> em <strong>{$tgt}</strong>\n({$q}xPdF): dano total = {$dano}";
                    applyDamage($pl, $tgt, $dano, $tipo, $out);
                    
                    $b['init_index']++;
                    unset($_SESSION['battle']['playingAlly']);
                    $_SESSION['battle']['needs_reload'] = true;
                break;


                case 'agarrao':
                    $dF   = (int)($_POST['rollAgarrao'] ?? 0);
                    $tgt  = $_POST['targetAgarrao']  ?? '';

                    $r = agarrao($pl, $tgt, $dF);
                    if ($r){
                        $_SESSION['battle']['agarrao'][$pl]['agarrando'] = $tgt;
                        $_SESSION['battle']['agarrao'][$tgt]['agarrado'] = $pl;
                        $_SESSION['battle']['agarrao'][$tgt]['flag'] = 0;
                        $out = "<strong>{$pl}</strong> agarrou <strong>{$tgt}</strong>!";  
                    } else {
                        $out = "<strong>{$pl}</strong> falhou em agarrar <strong>{$tgt}</strong>!";                         
                    }
                    $b['notes'][$tgt]['efeito'] .= "\nAgarrado por um inimigo (Pode apenas tentar se soltar)(Indefeso).";
                    $b['notes'][$pl]['efeito'] .= "\nAgarrando um inimigo (Não pode realizar ações por si mesmo).";
                    $b['init_index']++;
                    unset($_SESSION['battle']['playingAlly']);
                    $_SESSION['battle']['needs_reload'] = true;
                break;
                case 'soltar_agarrao':
                    $tgt = $_SESSION['battle']['agarrao'][$pl]['agarrando'];
                    $itensARemover = [
                        'Agarrado por um inimigo (Pode apenas tentar se soltar)(Indefeso).',
                        'Agarrando um inimigo (Não pode realizar ações por si mesmo).'
                    ];
                    $b['notes'][$pl]['efeito'] = removeEffect($b['notes'][$pl]['efeito'], $itensARemover);
                    $b['notes'][$tgt]['efeito'] = removeEffect($b['notes'][$tgt]['efeito'], $itensARemover);
                    unset($_SESSION['battle']['agarrao'][$tgt]);
                    unset( $_SESSION['battle']['agarrao'][$pl]);
                    $out = "<strong>{$pl}</strong> soltou <strong>{$tgt}</strong> de seu agarrão!";                                     
                break;
                case 'se_soltar_agarrao':
                    $dF   = (int)($_POST['rollSoltarAgarrao'] ?? 0) + $_SESSION['battle']['agarrao'][$pl]['flag'];
                    $tgt = $_SESSION['battle']['agarrao'][$pl]['agarrado'];
                    $r = agarrao($pl, $tgt, $dF);

                    if ($r){
                        $itensARemover = [
                            'Agarrado por um inimigo (Pode apenas tentar se soltar)(Indefeso).',
                            'Agarrando um inimigo (Não pode realizar ações por si mesmo).'
                        ];
                        $b['notes'][$pl]['efeito'] = removeEffect($b['notes'][$pl]['efeito'], $itensARemover);
                        $b['notes'][$tgt]['efeito'] = removeEffect($b['notes'][$tgt]['efeito'], $itensARemover); 
                        unset($_SESSION['battle']['agarrao'][$pl]);
                        unset( $_SESSION['battle']['agarrao'][$tgt]);
                        $out = "<strong>{$pl}</strong> se soltou do agarrão de <strong>{$tgt}</strong>!";  
                    } else {
                        $out = "<strong>{$pl}</strong> tentou se soltar do agarrão de <strong>{$tgt}</strong> mas falhou!";                         
                    }
                    $b['init_index']++;
                    unset($_SESSION['battle']['playingAlly']);
                    $_SESSION['battle']['needs_reload'] = true;
                break;



                case 'activate_draco':
                    $pm = getPlayerStat($pl,'PM');
                    if ($pm >= 1) {
                        draconificacao($pl, true);
                        $b['notes'][$pl]['draco_active'] = true;
                        $voo = getPlayerStat($pl, 'H')*20;
                        $out = "<strong>{$pl}</strong> ativou draconificação (+1PdF,+1R,+2H; Voo={$voo}m/s)";
                        $b['notes'][$pl]['efeito'] .= "\nDracônico: (+1PdF,+1R,+2H; Voo={$voo}m/s)";
                    } else {
                        $out = "<strong>{$pl}</strong> não tem PM suficientes para a draconificação.";
                    }
                break;
                case 'deactivate_draco':
                    draconificacao($pl, false);
                    $b['notes'][$pl]['draco_active'] = false;
                    $out = "<strong>{$pl}</strong> desativou Draconificação e ficou instável.";
                    $linhas = explode("\n", $b['notes'][$pl]['efeito']);
                    $linhas_filtradas = array_filter($linhas, function($linha) {
                        return ! in_array(trim($linha), [
                            'Dracônico: (+1PdF,+1R,+2H; Voo={$voo}m/s)'
                        ], true);
                    });
                    $b['notes'][$pl]['efeito'] = implode("\n", $linhas_filtradas);                                     
                    $b['init_index']++;
                    unset($_SESSION['battle']['playingAlly']);
                    $_SESSION['battle']['needs_reload'] = true;
                break;


                case 'activate_fusao':
                    if (!isset($b['notes'][$pl]['orig_PdF'])) {
                        $b['notes'][$pl]['orig_PdF'] = getPlayerStat($pl, 'PdF');
                    }                    
                    $pm = getPlayerStat($pl,'PM');
                    if ($pm >= 3) {
                        fusaoEterna($pl, $b['notes'][$pl]['orig_PdF'], true);
                        $b['notes'][$pl]['fusao_active'] = true;
                        $out = "<strong>{$pl}</strong> ativou Fusão Eterna (F = PdFx2; PdF = 0; -3 PMs.)";
                        $b['notes'][$pl]['efeito'] .= "\nForma Demoníaca:";
                        $b['notes'][$pl]['efeito'] .= "\nInvulnerável a fogo.(dano de fogo dividido por 10)";
                        $b['notes'][$pl]['efeito'] .= "\nVulnerável a Sônico e Elétrico.(Ignora sua armadura na FD)";
                    } else {
                        $out = "<strong>{$pl}</strong> não tem PM suficientes para ser possuído.";
                    }
                break;
                case 'deactivate_fusao':
                    $origPdF = $b['notes'][$pl]['orig_PdF'] ?? 0;
                    fusaoEterna($pl, $origPdF, false);
                    $b['notes'][$pl]['fusao_active'] = false;
                    unset($b['notes'][$pl]['orig_PdF']);
                    $linhas = explode("\n", $b['notes'][$pl]['efeito']);
                    $linhas_filtradas = array_filter($linhas, function($linha) {
                        return ! in_array(trim($linha), [
                            'Forma Demoníaca:',
                            'Invulnerável a fogo.(dano de fogo dividido por 10)',
                            'Vulnerável a Sônico e Elétrico.(Ignora sua armadura na FD)'
                        ], true);
                    });
                    $b['notes'][$pl]['efeito'] = implode("\n", $linhas_filtradas);
                    $out = "<strong>{$pl}</strong> desativou Fusão Eterna e voltou à forma normal.";
                break;


                case 'activate_incorp':
                    $pm = getPlayerStat($pl,'PM');
                    if (spendPM($pl, 2)) {
                        $b['notes'][$pl]['incorp_active'] = true;
                        $b['notes'][$pl]['efeito'] .= "\nIncorpóreo: imune a dano F/PdF; não pode usar F ou PdF.";
                        $out = "<strong>{$pl}</strong> tornou‑se Incorpóreo (−2 PM).";
                    } else {
                        $out = "<strong>{$pl}</strong> tentou ficar incorpóreo, mas não tem PM suficientes.";
                    }
                break;
                case 'deactivate_incorp':
                    $b['notes'][$pl]['incorp_active'] = false;
                    // remove apenas a linha de efeito relativa
                    $linhas = explode("\n",$b['notes'][$pl]['efeito']);
                    $linhas = array_filter($linhas, function($l){
                        return strpos($l,'Incorpóreo:') === false;
                    });
                    $b['notes'][$pl]['efeito'] = implode("\n",$linhas);
                    $out = "<strong>{$pl}</strong> retornou ao corpo físico.";
                break;


                case 'enable_use_pv':
                    $b['notes'][$pl]['use_pv'] = true;
                    $out = "<strong>{$pl}</strong> ativou uso de PV no lugar de PM.";
                break;
                case 'disable_use_pv':
                    unset($b['notes'][$pl]['use_pv']);
                    $out = "<strong>{$pl}</strong> voltou a usar PM normalmente.";
                break;                
                

                case 'extra_energy':
                    if (spendPM($pl, 2)){
                        $b['notes'][$pl]['extra_energy_next'] = true;
                        $out = "<strong>{$pl}</strong> irá recuperar todos seus PVs até o próximo turno.";
                        $b['init_index']++;
                        unset($_SESSION['battle']['playingAlly']);
                        $_SESSION['battle']['needs_reload'] = true;
                    } else {
                        $out = "<strong>{$pl}</strong> não tem PMs o suficiente.";
                    }
                break;   
                
                
                case 'use_ally':
                    $_SESSION['battle']['playingAlly'] = $_POST['ally'];
                    header('Location: battle.php?step=turn');
                exit;
                case 'back_to_owner':
                    unset($_SESSION['battle']['playingAlly']);
                    header('Location: battle.php?step=turn');
                exit;  


                case 'start_partner':
                    $chosen = $_POST['partner'] ?? '';
                    $_SESSION['battle']['statsBackup'][$pl] = [
                        'F'=>getPlayerStat($pl,'F'),
                        'H'=>getPlayerStat($pl,'H'),
                        'R'=>getPlayerStat($pl,'R'),
                        'A'=>getPlayerStat($pl,'A'),
                        'PdF'=>getPlayerStat($pl,'PdF'),
                    ];
                    $_SESSION['battle']['playingPartner'][$pl] = [
                        'name'  => $chosen,
                        'owner' => $pl,
                        'stats' => [
                            'F'   => max(getPlayerStat($chosen, 'F'), getPlayerStat($pl, 'F')),
                            'H'   => max(getPlayerStat($chosen, 'H'), getPlayerStat($pl, 'H')),
                            'R'   => max(getPlayerStat($chosen, 'R'), getPlayerStat($pl, 'R')),
                            'A'   => max(getPlayerStat($chosen, 'A'), getPlayerStat($pl, 'A')),
                            'PdF' => max(getPlayerStat($chosen, 'PdF'), getPlayerStat($pl, 'PdF'))
                        ]
                    ];
                    foreach ($_SESSION['battle']['playingPartner'][$pl]['stats'] as $campo=>$valor) {
                        setPlayerStat($pl, $campo, $valor);
                    }
                    header('Location: battle.php?step=turn');
                exit;
                case 'end_partner':
                    if (!empty($_SESSION['battle']['statsBackup'][$pl])) {
                      foreach ($_SESSION['battle']['statsBackup'][$pl] as $campo => $valor) {
                        setPlayerStat($pl, $campo, $valor);
                      }
                      unset($_SESSION['battle']['statsBackup'][$pl]);
                    }
                    unset($_SESSION['battle']['statsBackup'][$pl]);
                    unset($_SESSION['battle']['playingPartner'][$pl]);
                    header('Location: battle.php?step=turn');
                exit;


                case 'fim':
                    header('Location: battle.php?step=final');
                    $b['init_index']++;
                    unset($_SESSION['battle']['playingAlly']);
                    $_SESSION['battle']['needs_reload'] = true;
                exit;


                default:
                    $out = 'Ação inválida ou não reconhecida.';
                break;


            }

            
            $total   = count($b['order']);

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

        if (!empty($b['orig'])) {
            foreach ($b['orig'] as $pl => $origStats) {
                foreach ($origStats as $stat => $valor) {
                    setPlayerStat($pl, $stat, $valor);
                }
            }
            $b['orig'] = [];
        }

        // 2) Exibe o formulário de resumo final
        echo '<h1>Resumo da Batalha</h1>';
        echo '<form method="post" action="battle.php?step=save_final">';
        foreach ($players as $pl) {
            $stats = getPlayer($pl);
            echo '<fieldset style="margin-bottom:1em;padding:1em;border:1px solid #ccc">';
            echo '<legend><strong>' . htmlspecialchars($pl, ENT_QUOTES) . '</strong></legend>';
            echo '<input type="hidden" name="player_names[]" value="' . htmlspecialchars($pl, ENT_QUOTES) . '">';
            foreach (['F','H','R','A','PdF','PV','PM','PE'] as $c) {
                echo '<label style="display:block;margin:0.5em 0">'
                   . $c . ': <input type="number" '
                   . 'name="stats[' . htmlspecialchars($pl, ENT_QUOTES) . '][' . $c . ']" '
                   . 'value="' . (int)$stats[$c] . '" required>'
                   . '</label>';
            }
            // Inventário e Equipado
            $inv = htmlspecialchars($stats['inventario'] ?? '', ENT_QUOTES);
            $eq  = htmlspecialchars($stats['equipado']   ?? '', ENT_QUOTES);
            echo 'Inventário:<br>'
               . '<textarea name="stats[' . htmlspecialchars($pl, ENT_QUOTES) . '][inventario]" rows="4" cols="60">'
               . $inv . '</textarea><br>';
            echo 'Equipado:<br>'
               . '<textarea name="stats[' . htmlspecialchars($pl, ENT_QUOTES) . '][equipado]" rows="2" cols="60">'
               . $eq . '</textarea><br>';
            echo '</fieldset>';
        }
        // Reseta sessão de batalha
        $_SESSION['battle']['init_index'] = 0;
        $_SESSION['battle']['round']      = 1;
        $_SESSION['battle']['notes']      = [];
        echo '<button type="submit">Salvar Alterações</button> '
           . '<a href="index.php">Nova Batalha</a></form>';
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