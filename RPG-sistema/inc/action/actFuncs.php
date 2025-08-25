<?php
include_once 'atkProcessing.php';

function actPass($postData, &$b, $pl){
    $out = "<strong>{$pl}</strong> passou seu turno.";
    unset($b['playingAlly']);
    $b['init_index']++;
    return $out;
}

function actAtaque($postData, &$b, $pl){
    $dFA  = (int)($postData['dadoFA'] ?? 0);
    $atkType = $postData['atkTypeSimple'] ?? 'F';
    $tgt  = $postData['target']  ?? '';
    $dFD  = (int)($postData['dadoFD'] ?? 0);
    $def  = $postData['defesa']   ?? 'defender';
    $dmgType = $postData['dmgTypeSimple'] ?? '';

    $dano = defaultReactionTreatment($b, $tgt, $pl, $def, $dFA, $dFD, $atkType, $dmgType);

    $out = "<strong>{$pl}</strong> atacou <strong>{$tgt}</strong> ({$atkType}): dano = {$dano}"
           . applyDamage($pl, $tgt, $dano, $dmgType);

    unset($b['playingAlly']);
    $b['init_index']++;
    return $out;
}

function actAtaqueMultiplo($postData, &$b, $pl){
    $atkType  = $postData['atkTypeMulti']   ?? 'F';
    $tgt   = $postData['targetMulti']    ?? '';
    $q     = (int)($postData['quant']    ?? 1);
    $dados = $postData['dadosMulti']      ?? [];
    $dFD   = (int)($postData['dadoFDMulti'] ?? 0);
    $def   = $postData['defesaMulti']     ?? 'defender';
    $dmgType = $postData['dmgTypeMulti'] ?? '';

    $dano = atkMultiReactionTreatment($b, $q, $tgt, $pl, $dados, $dFD, $def, $atkType, $dmgType);

    $out = "<strong>{$pl}</strong> fez ataque múltiplo em <strong>{$tgt}</strong> ({$q}x{$atkType}): dano = {$dano}";
    $out .= applyDamage($pl, $tgt, $dano, $dmgType);

    unset($b['playingAlly']);
    $b['init_index']++;
    return $out;
}

function actTiroMultiplo($postData, &$b, $pl){
    $tgt    = $postData['targetTiroMulti']   ?? '';
    $q      = (int)($postData['quantTiro']   ?? 1);
    $dados  = $postData['dadosTiroMulti']    ?? [];
    $dadoFD = (int)($postData['dadoFDTiro']  ?? 0);
    $def    = $postData['defesaTiroMulti']   ?? 'defender';
    $dmgType = $postData['dmgTypeTiro'] ?? '';

    $dano = tiroMultiReactionTreatment($b, $q, $tgt, $pl, $dados, $dadoFD, $def, $dmgType);

    $out = "<strong>{$pl}</strong> usou <em>Tiro Múltiplo</em> em <strong>{$tgt}</strong>\n({$q}xPdF): dano total = {$dano}";
    applyDamage($pl, $tgt, $dano, $dmgType);

    unset($b['playingAlly']);
    $b['init_index']++;
    return $out;
}

function actStartConcentrar($postData, &$b, $pl){
    if (empty($b['notes'][$pl]['concentrado'])) {
        $b['notes'][$pl]['concentrado'] = 1;
    }
    $out = "<strong>{$pl}</strong> começou a se concentrar (rodada atual: +{$b['notes'][$pl]['concentrado']})";
    unset($b['playingAlly']);
    $b['init_index']++;
    return $out;
}
function actReleaseConcentrar($postData, &$b, $pl){
    $bonus = $b['notes'][$pl]['concentrado'] ?? 0;
    $dFA  = (int)($postData['dadoFA'] ?? 0);
    $atkType = $postData['atkTypeSimple'] ?? 'F';
    $tgt  = $postData['target']  ?? '';
    $dFD  = (int)($postData['dadoFD'] ?? 0);
    $def  = $postData['defesa']   ?? 'defender';
    $dmgType = $postData['dmgTypeSimples'] ?? '';

    $dano = defaultReactionTreatment($b, $tgt, $pl, $def, $dFA, $dFD, $atkType, $dmgType, $bonus);

    $out = "<strong>{$pl}</strong> atacou <strong>{$tgt}</strong> ({$atkType}): dano = {$dano} (Bonus de concentração = {$bonus})"
        . applyDamage($pl, $tgt, $dano, $dmgType);

    $b['notes'][$pl]['concentrado'] = 0;
    unset($b['playingAlly']);
    $b['init_index']++;
    return $out;
}

function actAgarrao($postData, &$b, $pl){
   $dF   = (int)($postData['rollAgarrao'] ?? 0);
   $tgt  = $postData['targetAgarrao']  ?? '';
   $r = agarrao($pl, $tgt, $dF);
   if ($r) {
       $b['agarrao'][$pl]['agarrando'] = $tgt;
       $b['agarrao'][$tgt]['agarrado'] = $pl;
       $b['agarrao'][$tgt]['flag'] = 0;
       $out = "<strong>{$pl}</strong> agarrou <strong>{$tgt}</strong>!";
   } else {
       $out = "<strong>{$pl}</strong> falhou em agarrar <strong>{$tgt}</strong>!";
   }
   $b['notes'][$tgt]['efeito'] .= "\nAgarrado por um inimigo (Pode apenas tentar se soltar)(Indefeso).";
   $b['notes'][$pl]['efeito'] .= "\nAgarrando um inimigo (Não pode realizar ações por si mesmo).";
   unset($b['playingAlly']);
   if (!empty($b['playingPartner'][$pl])) {
       if (!empty($b['statsBackup'][$pl])) {
           foreach ($b['statsBackup'][$pl] as $campo => $valor) {
               setPlayerStat($pl, $campo, $valor);
           }
           unset($b['statsBackup'][$pl]);
       }
       unset($b['statsBackup'][$pl]);
       unset($b['playingPartner'][$pl]);
   }
   $b['init_index']++;
   return $out;
}
function actSoltarAgarrao($postData, &$b, $pl){
    $tgt = $b['agarrao'][$pl]['agarrando'];
    $itensARemover = [
        'Agarrado por um inimigo (Pode apenas tentar se soltar)(Indefeso).',
        'Agarrando um inimigo (Não pode realizar ações por si mesmo).'
    ];
    $b['notes'][$pl]['efeito'] = removeEffect($b['notes'][$pl]['efeito'], $itensARemover);
    $b['notes'][$tgt]['efeito'] = removeEffect($b['notes'][$tgt]['efeito'], $itensARemover);
    unset($b['agarrao'][$tgt]);
    unset($b['agarrao'][$pl]);
    $out = "<strong>{$pl}</strong> soltou <strong>{$tgt}</strong> de seu agarrão!";
    return $out;
}
function actSeSoltarAgarrao($postData, &$b, $pl){
    $dF   = (int)($postData['rollSoltarAgarrao'] ?? 0) + $b['agarrao'][$pl]['flag'];
    $tgt = $b['agarrao'][$pl]['agarrado'];
    $r = agarrao($pl, $tgt, $dF);
    if ($r) {
        $itensARemover = [
            'Agarrado por um inimigo (Pode apenas tentar se soltar)(Indefeso).',
            'Agarrando um inimigo (Não pode realizar ações por si mesmo).'
        ];
        $b['notes'][$pl]['efeito'] = removeEffect($b['notes'][$pl]['efeito'], $itensARemover);
        $b['notes'][$tgt]['efeito'] = removeEffect($b['notes'][$tgt]['efeito'], $itensARemover);
        unset($b['agarrao'][$pl]);
        unset($b['agarrao'][$tgt]);
        $out = "<strong>{$pl}</strong> se soltou do agarrão de <strong>{$tgt}</strong>!";
    } else {
        $out = "<strong>{$pl}</strong> tentou se soltar do agarrão de <strong>{$tgt}</strong> mas falhou!";
    }
    unset($b['playingAlly']);
    $b['init_index']++;
    return $out;
}

function actAtaqueDebilitante($postData, &$b, $pl){
    $dFA  = (int)($postData['dadoFA'] ?? 0);
    $tgt  = $postData['target']  ?? '';
    $dFD  = (int)($postData['dadoFD'] ?? 0);
    $def  = $postData['defesa']   ?? 'defender';
    $stat = $postData['stat'] ?? 0;
    $dmgType = $postData['dmgTypeSimple'] ?? '';
    $atkType = $postData['atkTypeSimple'] ?? 'F';

    $dano = defaultReactionTreatment($b, $tgt, $pl, $def, $dFA, $dFD, $atkType, $dmgType);

    $result = '';
    if ($dano > 0) {
        $result = debilitateStat($tgt, $stat, $dFD);
    }
    $out = "<strong>{$pl}</strong> atacou <strong>{$tgt}</strong> (F): dano = {$dano} <br>"
        . applyDamage($pl, $tgt, $dano, $dmgType) . "<br>"
        . $result;

    unset($b['playingAlly']);
    $b['init_index']++;
    return $out;
}

function actActivateDraco($postData, &$b, $pl){
    $pm = getPlayerStat($pl, 'PM');
    if ($pm >= 1) {
        draconificacao($pl, true);
        $b['notes'][$pl]['draco_active'] = true;
        $voo = getPlayerStat($pl, 'H') * 20;
        $out = "<strong>{$pl}</strong> ativou draconificação (+1PdF,+1R,+2H; Voo={$voo}m/s)";
        $b['notes'][$pl]['efeito'] .= "\nDracônico: (+1PdF,+1R,+2H; Voo={$voo}m/s)";
    } else {
        $out = "<strong>{$pl}</strong> não tem PM suficientes para a draconificação.";
    }
    return $out;
}
function actDeactivateDraco($postData, &$b, $pl){
    draconificacao($pl, false);
    $b['notes'][$pl]['draco_active'] = false;
    $out = "<strong>{$pl}</strong> desativou draconificação.";
    unset($b['playingAlly']);
    $b['init_index']++;
    return $out;
}

function actActivateFusao($postData, &$b, $pl){
    $pm = getPlayerStat($pl, 'PM');
    if ($pm >= 3) {
        fusaoEterna($pl, $b['orig'][$pl]['PdF'], true);
        $b['notes'][$pl]['fusao_active'] = true;
        $out = "<strong>{$pl}</strong> ativou Fusão Eterna (F = " . ($b['orig'][$pl]['PdF'] * 2) . "; PdF = 0; -3 PMs.)";
        $b['notes'][$pl]['efeito'] .= "\nForma Demoníaca:";
        $b['notes'][$pl]['efeito'] .= "\nInvulnerável a fogo.(dano de fogo dividido por 10)";
        $b['notes'][$pl]['efeito'] .= "\nVulnerável a Sônico e Elétrico.(Ignora sua armadura na FD)";
    } else {
        $out = "<strong>{$pl}</strong> não tem PM suficientes para ser possuído.";
    }
    return $out;
}
function actDeactivateFusao($postData, &$b, $pl){
    $origPdF = $b['orig'][$pl]['PdF'] ?? 0;
    fusaoEterna($pl, $origPdF, false);
    $b['notes'][$pl]['fusao_active'] = false;
    $linhas = explode("\n", $b['notes'][$pl]['efeito']);
    $linhas_filtradas = array_filter($linhas, function ($linha) {
        return ! in_array(trim($linha), [
            'Forma Demoníaca:',
            'Invulnerável a fogo.(dano de fogo dividido por 10)',
            'Vulnerável a Sônico e Elétrico.(Ignora sua armadura na FD)'
        ], true);
    });
    $b['notes'][$pl]['efeito'] = implode("\n", $linhas_filtradas);
    $out = "<strong>{$pl}</strong> desativou Fusão Eterna e voltou à forma normal.";
    return $out;
}

function actActivateIncorp($postData, &$b, $pl){
    $pm = getPlayerStat($pl, 'PM');
    if (spendPM($pl, 2)) {
        $b['notes'][$pl]['incorp_active'] = true;
        $b['notes'][$pl]['efeito'] .= "\nIncorpóreo: imune a dano F/PdF; não pode usar F ou PdF. Não pode usar ou carregar itens físicos.";
        if (getPlayerStat($pl, 'equipado') != '' || getPlayerStat($pl, 'inventario') != '') {
            $itens = "Itens que " . $pl . " dropou ao tornar-se incorpóreo: " . getPlayerStat($pl, 'equipado') . "; " . getPlayerStat($pl, 'inventario');
            $b['arena'] .= "\n" . $itens;
            setPlayerStat($pl, 'equipado', '');
            setPlayerStat($pl, 'inventario', '');
        }
        $out = "<strong>{$pl}</strong> tornou‑se Incorpóreo (−2 PM).";
    } else {
        $out = "<strong>{$pl}</strong> tentou ficar incorpóreo, mas não tem PM suficientes.";
    }
    return $out;
}
function actDeactivateIncorp($postData, &$b, $pl){
    $b['notes'][$pl]['incorp_active'] = false;
    $linhas = explode("\n", $b['notes'][$pl]['efeito']);
    $linhas = array_filter($linhas, function ($l) {
        return strpos($l, 'Incorpóreo:') === false;
    });
    $b['notes'][$pl]['efeito'] = implode("\n", $linhas);
    $out = "<strong>{$pl}</strong> retornou ao corpo físico.";
    return $out;
}

function actEnableUsePV($postData, &$b, $pl){
    $b['notes'][$pl]['use_pv'] = true;
    $out = "<strong>{$pl}</strong> ativou uso de PV no lugar de PM.";
    return $out;
}
function actDisableUsePV($postData, &$b, $pl){
    unset($b['notes'][$pl]['use_pv']);
    $out = "<strong>{$pl}</strong> voltou a usar PM normalmente.";
    return $out;
}

function actExtraEnergy($postData, &$b, $pl){
    if (spendPM($pl, 2)) {
        $b['notes'][$pl]['extra_energy_next'] = true;
        $out = "<strong>{$pl}</strong> irá recuperar todos seus PVs até o próximo turno.";
        unset($b['playingAlly']);
        $b['init_index']++;
    } else {
        $out = "<strong>{$pl}</strong> não tem PMs o suficiente.";
    }
    return $out;
}

function actMagiaExtra($postData, &$b, $pl){
    if (magiaExtra($pl, 'spend')) {
        $b['notes'][$pl]['magia_extra_next'] = true;
        $out = "<strong>{$pl}</strong> irá recuperar todos seus PMs até o próximo turno.";
        unset($b['playingAlly']);
        $b['init_index']++;
    } else {
        $out = "<strong>{$pl}</strong> não está perto da morte para usar Magia Extra.";
    }
    return $out;
}

function actUseAlly($postData, &$b, $pl){
    $b['playingAlly'] = $postData['ally'];
    header('Location: battle.php?step=turn');
}

function actBackToOwner($postData, &$b, $pl){
    unset($b['playingAlly']);
    header('Location: battle.php?step=turn');
}

function actStartPartner($postData, &$b, $pl){
    $chosen = $postData['partner'] ?? '';
    $b['statsBackup'][$pl] = [
        'F' => getPlayerStat($pl, 'F'),
        'H' => getPlayerStat($pl, 'H'),
        'R' => getPlayerStat($pl, 'R'),
        'A' => getPlayerStat($pl, 'A'),
        'PdF' => getPlayerStat($pl, 'PdF'),
    ];
    $b['playingPartner'][$pl] = [
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
    foreach ($b['playingPartner'][$pl]['stats'] as $campo => $valor) {
        setPlayerStat($pl, $campo, $valor);
    }
    header('Location: battle.php?step=turn');
}
function actEndPartner($postData, &$b, $pl){
    if (!empty($b['statsBackup'][$pl])) {
        foreach ($b['statsBackup'][$pl] as $campo => $valor) {
            setPlayerStat($pl, $campo, $valor);
        }
        unset($b['statsBackup'][$pl]);
    }
    unset($b['statsBackup'][$pl]);
    unset($b['playingPartner'][$pl]);
    header('Location: battle.php?step=turn');
}

function actAceleracaoII($postData, &$b, $pl){
    if (spendPM($pl, 2)) {
        $b['notes'][$pl]['aceleracao_ii_active'] = true;
        $out = "<strong>{$pl}</strong> gasta 2 PM e usa Aceleração II, ganhando uma ação extra!";
    } else {
        $out = "<strong>{$pl}</strong> não tem PMs suficientes para usar Aceleração II.";
    }
    return $out;
}
function actAceleracaoI($postData, &$b, $pl){
    if (spendPM($pl, 1)) {
        $out = "<strong>{$pl}</strong> gasta 1 PM e usa Aceleração I, ganhando um movimento extra!";
    } else {
        $out = "<strong>{$pl}</strong> não tem PMs suficientes para usar Aceleração I.";
    }
    return $out;
}

function actActivateInvisibilidade($postData, &$b, $pl){
    if (spendPM($pl, 1)) {
        setPlayerStat($pl, 'PM', getPlayerStat($pl, 'PM') + 1);
        $b['notes'][$pl]['invisivel'] = true;
        $efeitoInvisibilidade = "\nVocê está invisível.";
        if (strpos($b['notes'][$pl]['efeito'], trim($efeitoInvisibilidade)) === false) {
            $b['notes'][$pl]['efeito'] .= $efeitoInvisibilidade;
        }
        $out = "<strong>{$pl}</strong> está invisível (1PM/turno)";
    } else {
        $out = "<strong>{$pl}</strong> não tem PMs suficientes para usar Invisibilidade.";
    }
    return $out;
}
function actDeactivateInvisibilidade($postData, &$b, $pl){
    unset($b['notes'][$pl]['invisivel']);
    $out = "<strong>{$pl}</strong> está visível novamente.";
    return $out;
}

function actSwordLuxcruentha($postData, &$b, $pl){
    $dFA1     = (int)($postData['luxDadoFA1'] ?? 0);
    $dFA2     = (int)($postData['luxDadoFA2'] ?? 0);
    $target  = $postData['luxTarget'] ?? '';
    $defesa  = $postData['luxDefesa'] ?? 'defender';
    $dFD     = (int)($postData['luxDadoFD'] ?? 0);

    $out = atkLuxcruentha($b, $pl, $target, $defesa, $dFD, $dFA1, $dFA2);

    unset($b['playingAlly']);
    $b['init_index']++;
    return $out;
}

function actSustain($postData, &$b, $pl){
    $logMessages = processSustainedSpells($pl, $postData, $b);
    $b['sustained_log'] = $logMessages;
    $b['sustained_processed_this_turn'][$pl] = true;
    header('Location: battle.php?step=turn');
}

function actFim($postData, &$b, $pl){
    header('Location: battle.php?step=final');
    unset($b['playingAlly']);
    $b['init_index']++;
}

function actMagic($postData, &$b, $pl){
    $magic_slug = $postData['magic'] ?? '';
    $target = $postData['magic_target'] ?? $pl; // Alvo padrão é o próprio conjurador

    if (empty($magic_slug)) {
        $out = 'Nenhuma magia foi selecionada.';
        return $out;
    }
    $out = magicSwitch($postData, $b, $pl, $magic_slug, $target);
    return $out;
}
?>