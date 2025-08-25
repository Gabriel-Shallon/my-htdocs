<?php
include_once './inc/generalFuncs.php';
include_once './inc/battleFuncs.php';

function ataqueMagico($b, $mago, $alvosInfo, $PMs, $atkType){
    $out = '';
    $costPerTgt = round($PMs / count($alvosInfo));
    if (spendPM($mago, $PMs)) {
        foreach ($alvosInfo as $tgt) {
            $tgtName = $tgt['name'];
            $dano = max(defaultReactionTreatment($b, $tgtName, $mago, $tgt['reaction'], $tgt['rollFA'], $tgt['rollFD'], $atkType, 'Magia', $costPerTgt), 0);
            $out .= applyDamage($mago, $tgtName, $dano, 'Magia', $out);
            $out .= "<strong>{$mago}</strong> usou Ataque Mágico ({$atkType}) em <strong>{$tgtName}</strong>. PMs = {$PMs}; Dano = {$dano}<br>";
        }
        return $out;
    } else {
        return "<strong>{$mago}</strong> não tem PMs o suficiente par lançar esse ataque mágico.";
    }
}

function lancaInfalivelDeTalude($mago, $alvosInfo, $PMs, $dFD = 0){
    $out = '';
    if (spendPM($mago, $PMs)) {
        foreach ($alvosInfo as $tgt) {
            $tgtName = $tgt['name'];
            $FA = invulnerabilitieTest($tgtName, 'Magia', $tgt['qtdAtk'] * 2);
            $dano = defaultReactionTreatment($_SESSION['battle'], $tgtName, $mago, $tgt['reaction'], 0, $dFD, 'PdF', 'Magia', 0, $FA);
            $out .= applyDamage($mago, $tgtName, $dano, 'Magia', $out);
            $out .= "<strong>{$mago}</strong> usou " . getMagicSpecialName($mago, 'lanca_infalivel_de_talude') . "(A Lança Infalível de Talude) em <strong>{$tgtName}</strong>. PMs = {$PMs}; Dano = {$dano}<br>";
        }
        return $out;
    } else {
        return "<strong>{$mago}</strong> não tem PMs o suficiente par lançar essa quantidade de Lanças Infalíveis de Talude.";
    }
}

function brilhoExplosivo($mago, $alvo, $dadosFA, $dadoFD, $def = 'indefeso'){
    if (spendPM($mago, 25)) {
        $FA = invulnerabilitieTest($alvo, 'Magia', $dadosFA);
        $dano = defaultReactionTreatment($_SESSION['battle'], $alvo, $mago, $def, 0, $dadoFD, 'PdF', 'Magia', 0, $FA);
        applyDamage($mago, $alvo, $dano, 'Magia');
        return "<strong>{$mago}</strong> usou " . getMagicSpecialName($mago, 'brilho_explosivo') . " (Brilho Explosivo) em <strong>{$alvo}</strong>. PMs = 25; Dano = {$dano}<br>";
    } else {
        return "<strong>{$mago}</strong> não tem PMs o suficiente para lançar Brilho Explisivo.";
    }
}

function morteEstelar($mago, $alvo){
    if (spendPM($mago, 5)) {
        $FA = '992625164071551871061817274843250294784';
        $FD = (string)FDindefeso($alvo, 'Magia');
        $dano = bcsub($FA, $FD, 0);
        $PV = (string)getPlayerStat($alvo, 'PV');
        $resultado = bcsub($PV, $dano, 0);
        if (bccomp($resultado, '0', 0) <= 0) {
            setPlayerStat($mago, 'PM_max', getPlayerStat($mago, 'PM_max') - 5);
            setPlayerStat($alvo, 'PV', 0);
            monitorPVChange($alvo, getPlayerStat($alvo, 'PV_max'));
            return "<strong>{$mago}</strong> aniquilou <strong>{$alvo}</strong> com <strong>" . $dano . "</strong> de dano!";
        } else {
            setPlayerStat($mago, 'PM_max', getPlayerStat($mago, 'PM_max') - 5);
            setPlayerStat($alvo, 'PV', (int)$resultado);
            return "<strong>{$alvo}</strong> sobreviveu a <strong>" . $dano . "</strong> de dano da " . getMagicSpecialName($mago, 'morte_estelar') . " (Morte Estelar) de <strong>{$mago}</strong>, com <strong>{$resultado}</strong> de PVs. WOW!!!";
        }
    } else {
        return "<strong>{$mago}</strong> não tem PMs o suficiente para lançar Morte Estelar.";
    }
}

function enxameDeTrovoes($b, $mago, $alvo, $dadoFA1, $dadoFA2, $dadoFD, $def = 'denfender_sem_armadura'){
    $out = '';
    if (spendPM($mago, 4)) {
        $FA = invulnerabilitieTest($alvo, 'Magia', ($dadoFA1+$dadoFA2+getPlayerStat($mago, 'H')-hDebuff($_SESSION, $mago, $alvo, 'PdF')));
        $dano = defaultReactionTreatment($b, $alvo, $mago, $def, 0, $dadoFD, 'PdF', 'Magia', 0, $FA);
        if ($def == 'indefeso' || ($dano != 0 && ($def == 'defender_esquiva' || $def == 'defender_esquiva'))){
            $dano = $FA;
        }
        $out .= applyDamage($mago, $alvo, $dano, 'Magia', $out);
        $out .= "<strong>{$mago}</strong> usou " . getMagicSpecialName($mago, 'enxame_de_trovoes') . " (Enxame de Trovoes) em <strong>{$alvo}</strong>. -4 PMs; Dano = {$dano}<br>";
        return $out;
    } else {
        return "<strong>{$mago}</strong> não tem PMs o suficiente para lançar Enxame de Trovões.";
    }
}

function nulificacaoTotalDeTalude($mago, $alvo, $RTest){
    if (spendPM($mago, 50)) {
        if (statTest($alvo, 'R', getPlayerStat($mago, 'H') - resistenciaMagia($alvo), $RTest)) {
            return "<strong>{$mago}</strong> usou " . getMagicSpecialName($mago, 'nulificacao_total_de_talude') . " (A Nulificação Total de Talude) para apagar <strong>{$alvo}</strong> da existência, mas não teve Habilidade o suficiente para afetar-lo!!! -50 PMs.<br>";
        }
        $pdo = conecta();
        $tabelasFilhas = [
            'player_advantages',
            'player_disadvantages',
            'player_damage_types',
            'player_vulnerabilities',
            'player_invulnerabilities',
            'player_extra_armor',
            'player_magias'
        ];
        $pdo->beginTransaction();
        $sqlAllies = "DELETE FROM RPG.allies WHERE dono = :nome OR aliado = :nome";
        $stmtAllies = $pdo->prepare($sqlAllies);
        $stmtAllies->execute([':nome' => $alvo]);
        foreach ($tabelasFilhas as $tabela) {
            $sql = "DELETE FROM RPG.{$tabela} WHERE player_name = :nome";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':nome' => $alvo]);
        }
        if (statTest($alvo, 'R', 4 - resistenciaMagia($alvo), $RTest)) {
            $pdo->commit();
            return  "<strong>{$mago}</strong> usou " . getMagicSpecialName($mago, 'nulificacao_total_de_talude') . " (A Nulificação Total de Talude) para apagar <strong>{$alvo}</strong> da existência. <strong>{$alvo}</strong> resistiu, mas perdeu seus poderes... -50 PMs.<br>";
        }
        $constraintOff = "SET foreign_key_checks = 0";
        $sqlPlayer = "DELETE FROM RPG.player WHERE nome = :nome";
        $constraintOn = "SET foreign_key_checks = 1";
        $stmtConstraintOff = $pdo->prepare($constraintOff);
        $stmtPlayer = $pdo->prepare($sqlPlayer);
        $stmtConstraintOn = $pdo->prepare($constraintOn);
        $stmtConstraintOff->execute();
        $stmtPlayer->execute([':nome' => $alvo]);
        $stmtConstraintOn->execute();
        $pdo->commit();
        $key = array_search($alvo, $_SESSION['battle']['order']);
        if ($key !== false) {
            unset($_SESSION['battle']['order'][$key]);
            $_SESSION['battle']['order'] = array_values($_SESSION['battle']['order']);
        }
        $key_players = array_search($alvo, $_SESSION['battle']['players']);
        if ($key_players !== false) {
            unset($_SESSION['battle']['players'][$key_players]);
            $_SESSION['battle']['players'] = array_values($_SESSION['battle']['players']);
        }
        if (isset($_SESSION['battle']['notes'][$alvo])) {
            unset($_SESSION['battle']['notes'][$alvo]);
        }
        if (isset($_SESSION['battle']['orig'][$alvo])) unset($_SESSION['battle']['orig'][$alvo]);
        if (isset($_SESSION['battle']['agarrao'][$alvo])) unset($_SESSION['battle']['agarrao'][$alvo]);

        return "<strong>{$mago}</strong> usou " . getMagicSpecialName($mago, 'nulificacao_total_de_talude') . " (A Nulificação Total de Talude) e apagou <strong>{$alvo}</strong> da existência. -50 PMs.<br>";
    } else {
        return "<strong>{$mago}</strong> não tem PMs o suficiente para Nulificar alguém!";
    }
}

function bolaDeFogoInstavel($mago, $tgts, $PMs, $dadosFA){
    $out = '';
    if (spendPM($mago, $PMs)) {
        $FA = getPlayerStat($mago, 'H');
        foreach ($dadosFA as $dFA) {
            $FA += $dFA;
        }
        foreach ($tgts as $tgt) {
            $tgtName = $tgt['name'];
            $tgtFA = invulnerabilitieTest($tgtName, 'Magia', $FA-hDebuff($_SESSION['battle'], $mago, $tgtName, 'PdF'));
            $dano = defaultReactionTreatment($_SESSION['battle'], $tgtName, $mago, $tgt['reaction'], 0, $tgt['dFD'], 'PdF', 'Magia', 0, $tgtFA);
            applyDamage($mago, $tgtName, $dano, 'Magia', $out);
            $out .= "<strong>{$mago}</strong> usou " . getMagicSpecialName($mago, 'bola_de_fogo_instavel') . " (Bola de Fogo Instável) em <strong>{$tgtName}</strong>. Dano = {$dano}<br>";
        }
        $out .= "PMs = -{$PMs}";
        return $out;
    } else {
        return "<strong>{$mago}</strong> não tem PMs o suficiente para lançar essa Bola de Fogo Instável!";
    }
}

function bolaDeFogo($mago, $tgts, $PMs, $dadoFA){
    $out = '';
    if (spendPM($mago, $PMs)) {
        $FA = getPlayerStat($mago, 'H') + $PMs + $dadoFA;
        foreach ($tgts as $tgt) {
            $tgtName = $tgt['name'];
            $tgtFA = invulnerabilitieTest($tgtName, 'Magia', $FA-hDebuff($_SESSION['battle'], $mago, $tgtName, 'PdF'));
            $dano = defaultReactionTreatment($_SESSION['battle'], $tgtName, $mago, $tgt['reaction'], 0, $tgt['dFD'], 'PdF', 'Magia', 0, $tgtFA);
            applyDamage($mago, $tgtName, $dano, 'Magia', $out);
            $out .= "<strong>{$mago}</strong> usou " . getMagicSpecialName($mago, 'bola_de_fogo') . " (Bola de Fogo) em <strong>{$tgtName}</strong>. Dano = {$dano}<br>";
        }
        $out .= "PMs = -{$PMs}";
        return $out;
    } else {
        return "<strong>{$mago}</strong> não tem PMs o suficiente para lançar essa Bola de Fogo!";
    }
}

function bolaDeLama($mago, $tgt, $dadosFA, $dadoFD, $def){
    if (spendPM($mago, 1)) {
        $FA = getPlayerStat($mago, 'H');
        foreach ($dadosFA as $dadoFA) {
            $FA += $dadoFA;
        }
        $FA = invulnerabilitieTest($tgt, 'Magia', $FA-hDebuff($_SESSION['battle'], $mago, $tgt, 'PdF'));
        $dano = defaultReactionTreatment($_SESSION['battle'], $tgt, $mago, $def, 0, $dadoFD, 'PdF', 'Magia', 0, $FA);
        if ($def == 'indefeso' || ($dano != 0 && ($def == 'defender_esquiva' || $def == 'defender_esquiva'))){
            $dano = $FA;
        }
        if ($dano <= 0) {
            $_SESSION['battle']['notes'][$tgt]['efeito'] .= "\nMonstruoso: Coberto de lama.";
            return "<strong>{$mago}</strong> usou " . getMagicSpecialName($mago, 'bola_de_lama') . " (Bola de Lama) em <strong>{$tgt}</strong>. PMs = -1<br>
            <strong>{$tgt}</strong> ficou monstruoso por estar coberto de lama.";
        }
        return "<strong>{$mago}</strong> usou " . getMagicSpecialName($mago, 'bola_de_lama') . " (Bola de Lama) em <strong>{$tgt}</strong>. PMs = -1<br>
        <strong>{$tgt}</strong> não se sujou de lama.";
    } else {
        return "<strong>{$mago}</strong> não tem PMs o suficiente para lançar Bola de Lama!";
    }
}

function bombaDeLuz($mago, $tgts, $PMs){
    $out = '';
    if (spendPM($mago, $PMs)) {
        $FA = getPlayerStat($mago, 'H') + $PMs;
        foreach ($tgts as $tgt) {
            $tgtName = $tgt['name'];
            $tgtFA = invulnerabilitieTest($tgtName, 'Magia', $FA-hDebuff($_SESSION['battle'], $mago, $tgtName, 'PdF'));
            $dano = defaultReactionTreatment($_SESSION['battle'], $tgtName, $mago, $tgt['reaction'], 0, $tgt['dFD'], 'PdF', 'Magia', 0, $tgtFA);
            applyDamage($mago, $tgtName, $dano, 'Magia', $out);
            $out .= "<strong>{$mago}</strong> usou " . getMagicSpecialName($mago, 'bomba_de_luz') . " (Bomba de Luz) em <strong>{$tgtName}</strong>. Dano = {$dano}<br>";
        }
        $out .= "PMs = -{$PMs}";
        return $out;
    } else {
        return "<strong>{$mago}</strong> não tem PMs o suficiente para lançar Bomba de Luz!";
    }
}

function bombaDeTerra($mago, $tgt, $dFD, $def){
    if (spendPM($mago, 10)) {
        $FA = invulnerabilitieTest($tgt, 'Magia', 
        getPlayerStat($mago, 'H')-hDebuff($_SESSION['battle'], $mago, $tgt, 'PdF') + 15);
        $dano = defaultReactionTreatment($_SESSION['battle'], $tgt, $mago, $def, 0, $dFD, 'PdF', 'Magia', 0, $FA);
        applyDamage($mago, $tgt, $dano, 'Magia');
        return "<strong>{$mago}</strong> usou " . getMagicSpecialName($mago, 'bomba_de_terra') . " (Bomba de Terra) em <strong>{$tgt}</strong>. Dano = {$dano}; PMs = -10<br>";
    } else {
        return "<strong>{$mago}</strong> não tem PMs o suficiente para lançar Bomba de Terra!";
    }
}

function solisanguis($mago, $tgt, $dCusto, $dFA1, $dFA2){
    $custo = floor($dCusto / 2) + 5;
    if (spendPM($mago, $custo, true)) {
        $dano = invulnerabilitieTest($tgt, 'Magia', 
        getPlayerStat($mago, 'H')-hDebuff($_SESSION['battle'], $mago, $tgt, 'PdF') + $dFA1 + $dFA2) - resistenciaMagia($tgt);
        applyDamage($mago, $tgt, max($dano, 0), 'Magia');
        return "<strong>{$mago}</strong> usou " . getMagicSpecialName($mago, 'solisanguis') . " (Solisanguis) em <strong>{$tgt}</strong>. Dano = {$dano}; PVs = -{$custo}<br>";
    } else {
        return "<strong>{$mago}</strong> não tem PVs o suficiente para lançar Solisanguis!";
    }
}

function solisanguisRuptura($mago, $tgt, $dCusto, $dFA1, $dFA2, $dFA3, $dFA4){
    $custo = $dCusto + 5;
    if (spendPM($mago, $custo, true)) {
        $dano = invulnerabilitieTest($tgt, 'Magia', 
        getPlayerStat($mago, 'H')-hDebuff($_SESSION['battle'], $mago, $tgt, 'PdF') + $dFA1 + $dFA2 + $dFA3 + $dFA4) - resistenciaMagia($tgt);
        applyDamage($mago, $tgt, max($dano, 0), 'Magia');
        return "<strong>{$mago}</strong> usou " . getMagicSpecialName($mago, 'solisanguis_ruptura') . " (Solisanguis Ruptura) em <strong>{$tgt}</strong>. Dano = {$dano}; PVs = -{$custo}<br>";
    } else {
        return "<strong>{$mago}</strong> não tem PVs o suficiente para lançar Solisanguis Ruptura!";
    }
}

function solisanguisEvisceratio($mago, $tgt, $d){
    $custo = $d + getPlayerStat($mago, 'H');
    if (spendPM($mago, $custo, true)) {
        $dano = invulnerabilitieTest($tgt, 'Magia', 
        (getPlayerStat($mago, 'H')-hDebuff($_SESSION['battle'], $mago, $tgt, 'PdF')) * $d) - resistenciaMagia($tgt);
        applyDamage($mago, $tgt, max($dano, 0), 'Magia');
        return "<strong>{$mago}</strong> usou " . getMagicSpecialName($mago, 'solisanguis_ruptura') . " (Solisanguis Ruptura) em <strong>{$tgt}</strong>. Dano = {$dano}; PVs = -{$custo}<br>";
    } else {
        return "<strong>{$mago}</strong> não tem PVs o suficiente para lançar Solisanguis Ruptura!";
    }
}

function sortilegium($mago, $tgt, $dC, $dPV1, $dPV2){
    if (spendPM($mago, $dC, true)) {
        setPlayerStat($tgt, 'PV', getPlayerStat($tgt, 'PV') + ($dPV1 + $dPV2));
        return "<strong>{$mago}</strong> usou " . getMagicSpecialName($mago, 'sortilegium') . " (Sortilegium) em <strong>{$tgt}</strong>. Cura = +" . $dPV1 + $dPV2 . "; PVs = -{$dC}<br>";
    } else {
        return "<strong>{$mago}</strong> não tem PVs o suficiente para lançar Sortilegium!";
    }
}

function sanctiSanguis($mago, $tgt, $qtd){;
    if (spendPM($mago, $qtd, true)) {
        setPlayerStat($tgt, 'PV', getPlayerStat($tgt, 'PV') + $qtd);
        monitorPVChange($tgt, getPlayerStat($tgt, 'PV') + $qtd);
        return "<strong>{$mago}</strong> usou " . getMagicSpecialName($mago, 'sancti_sanguis') . " (Sancti Sanguis) em <strong>{$tgt}</strong>. PVs Tranferidos: {$qtd}<br>";
    } else {
        return "<strong>{$mago}</strong> não tem essa quantidade de PVs para transferir!";
    }
}

function luxcruentha($mago){
    if (spendPM($mago, 4, true)) {
        $addLuxcru = getPlayerStat($mago, 'equipado') . "\nEspada Luxcruentha (FA = H + 2d)(FD/2);";
        setPlayerStat($mago, 'equipado', $addLuxcru);
        return "<strong>{$mago}</strong> usou a magia " . getMagicSpecialName($mago, 'luxcruentha') . " (Luxcruentha) para invocar uma Espada de Sangue. PVs: -4<br>";
    } else {
        return "<strong>{$mago}</strong> não tem PVs o suficiente para invocar a Luxcruentha!";
    }
}
function atkLuxcruentha($b, $mago, $tgt, $def, $dFD, $dFA1, $dFA2){
    $out = '';
    $FA = invulnerabilitieTest($tgt, 'Magia', 
    getPlayerStat($mago, 'H')-hDebuff($_SESSION['battle'], $mago, $tgt, 'F') + $dFA1 + $dFA2);

    if (!empty($b['agarrao'][$tgt]['agarrado'])) {
        $def = 'indefeso';
    }
    $FD = FD($tgt, $dFD, 'Magia');
    if ($def === 'indefeso')  
        {$FD = FDindefeso($tgt, 'Magia');}
    if ($def === 'defender_esquiva') 
        {$FD = FDesquiva($mago, $tgt, $dFD, 'F', 'Magia', $FA);}
    if ($def === 'defender_esquiva_deflexao') 
        {$FD = FDesquiva($mago, $tgt, $dFD, 'F', 'Magia', $FA, true);}

    $dano = max($FA - floor($FD / 2), 0);
    $out .= " Dano = " . $dano;
    applyDamage($mago, $tgt, $dano, 'Magia', $out);
    return $out;
}

function artifanguis($mago, $obj, $cost){
    if (spendPM($mago, $cost, true)) {
        $addObj = getPlayerStat($mago, 'equipado') . "\n" . $obj . ";";
        setPlayerStat($mago, 'equipado', $addObj);
        return "<strong>{$mago}</strong> usou a magia " . getMagicSpecialName($mago, 'artifanguis') . " (Artifanguis) para invocar um objeto ({$obj}). PVs: -{$cost}<br>";
    } else {
        return "<strong>{$mago}</strong> não tem PVs o suficiente ou não há sangue para invocar um objeto!";
    }
}

function excruentio($mago, $tgt, $dFD, $dFA1, $dFA2, $def){
    if (spendPM($mago, 2, true)) {
        $FA = invulnerabilitieTest($tgt, 'Magia', 
        getPlayerStat($mago, 'H')-hDebuff($_SESSION['battle'], $mago, $tgt, 'PdF') + $dFA1 + $dFA2);
        $dano = defaultReactionTreatment($_SESSION, $tgt, $mago, $def, 0, $dFD, 'PdF', 'Magia', 0, $FA);
        if ($dano > 0) {
            if (empty($_SESSION['battle']['notes'][$mago]['sustained_spells'])) {
                $_SESSION['battle']['notes'][$mago]['sustained_spells'] = "Excruentio(" . $tgt . ");\n";
            } elseif (!empty($_SESSION['battle']['notes'][$mago]['sustained_spells'])) {
                $_SESSION['battle']['notes'][$mago]['sustained_spells'] .= "Excruentio(" . $tgt . ");\n";
            }
        }
        applyDamage($mago, $tgt, $dano, 'Magia');
        return "<strong>{$mago}</strong> usou " . getMagicSpecialName($mago, 'excruentio') . " (Excruentio) em <strong>{$tgt}</strong>. Dano = {$dano}; PMs = -2<br>";
    } else {
        return "<strong>{$mago}</strong> não tem PMs o suficiente para lançar Excruentio!";
    }
}

function speculusanguis($mago, $tgt){
    if (empty($_SESSION['battle']['notes'][$mago]['sustained_spells'])) {
        $_SESSION['battle']['notes'][$mago]['sustained_spells'] = "Speculusanguis(" . $tgt . ");\n";
    } elseif (!empty($_SESSION['battle']['notes'][$mago]['sustained_spells'])) {
        $_SESSION['battle']['notes'][$mago]['sustained_spells'] .= "Speculusanguis(" . $tgt . ");\n";
    }
    $_SESSION['battle']['sustained_effects'][$mago]['speculusanguis']['dmg'] = 0;
    return "<strong>{$mago}</strong> usou " . getMagicSpecialName($mago, 'speculusanguis') . " (Speculusanguis) em <strong>{$tgt}</strong>.<br>";
}

function visExVulnere($mago){
    if (empty($_SESSION['battle']['notes'][$mago]['sustained_spells'])) {
        $_SESSION['battle']['notes'][$mago]['sustained_spells'] = "Vis Ex Vulnere;\n";
    } elseif (!empty($_SESSION['battle']['notes'][$mago]['sustained_spells'])) {
        $_SESSION['battle']['notes'][$mago]['sustained_spells'] .= "Vis Ex Vulnere;\n";
    }
    $_SESSION['battle']['sustained_effects'][$mago]['visExVulnere']['dmg'] = 0;
    $_SESSION['battle']['sustained_effects'][$mago]['visExVulnere']['pms'] = 0;
    return "<strong>{$mago}</strong> usou " . getMagicSpecialName($mago, 'vis_ex_vulnere') . " (Vis Ex Vulnere).<br>";
}

function solcruoris($mago, $cost){
    if (spendPM($mago, $cost, true)) {
        if (empty($_SESSION['battle']['notes'][$mago]['sustained_spells'])) {
            $_SESSION['battle']['notes'][$mago]['sustained_spells'] = "Solcruoris(" . $cost . ");\n";
        } elseif (!empty($_SESSION['battle']['notes'][$mago]['sustained_spells'])) {
            $_SESSION['battle']['notes'][$mago]['sustained_spells'] .= "Solcruoris(" . $cost . ");\n";
        }
        $extraFD = floor($cost / 3);
        $_SESSION['battle']['sustained_effects'][$mago]['solcruoris']['extraA'] = $extraFD;
        setPlayerStat($mago, 'A', getPlayerStat($mago, 'A') + $extraFD);
        return "<strong>{$mago}</strong> usou " . getMagicSpecialName($mago, 'solcruoris') . " (Solcruoris). Armadura +{$extraFD}<br>";
    } else {
        return "<strong>{$mago}</strong> não tem PVs o suficiente para usar Solcruoris!";
    }
}

function spectraematum($mago, $debuff, $tgt){
    if (spendPM($mago, 2 + $debuff, true)) {
        if (empty($_SESSION['battle']['notes'][$mago]['sustained_spells'])) {
            $_SESSION['battle']['notes'][$mago]['sustained_spells'] = "Spectraematum(" . $tgt . ");\n";
        } elseif (!empty($_SESSION['battle']['notes'][$mago]['sustained_spells'])) {
            $_SESSION['battle']['notes'][$mago]['sustained_spells'] .= "Spectraematum(" . $tgt . ");\n";
        }
        $debuffH = floor($debuff / 2);
        $_SESSION['battle']['sustained_effects'][$mago]['spectraematum'][$tgt]['origH'] = getPlayerStat($tgt, 'H');
        setPlayerStat($tgt, 'H', max(getPlayerStat($tgt, 'H') - $debuffH, 0));
        applyDamage($mago, $tgt, 1, 'Magia');
        return "<strong>{$mago}</strong> usou " . getMagicSpecialName($mago, 'spectraematum') . " (Spectraematum) em <strong>{$tgt}</strong>. Habilidade do Alvo -{$debuffH}<br>";
    } else {
        return "<strong>{$mago}</strong> não tem PVs o suficiente para lançar Spectraematum!";
    }
}

function aeternumTribuo($mago, $tgt){
    spendPM($mago, getPlayerStat($mago, 'PV'), true);
    setPlayerStat($tgt, 'PV', getPlayerStat($tgt, 'PV_max'));
    setPlayerStat($tgt, 'PM', getPlayerStat($tgt, 'PM_max'));
    $_SESSION['battle']['notes'][$tgt]['aeternum_tribuo']['origH'] = getPlayerStat($tgt, 'H');
    setPlayerStat($tgt, 'H', getPlayerStat($mago, 'H') + getPlayerStat($tgt, 'H'));
    return "<strong>{$mago}</strong> usou " . getMagicSpecialName($mago, 'aeternum_tribuo') . " (Aeternum Tribuo) em <strong>{$tgt}</strong>. Custo: Morte; Alvo: Mais vivo do que nunca;<br>";
}

function inhaerescorpus($mago, $tgt, $testR){
    if (!statTest($tgt, 'R', getPlayerStat($mago, 'H'), $testR)) {
        if (spendPM($mago, 10, true)) {
            if (empty($_SESSION['battle']['notes'][$mago]['sustained_spells'])) {
                $_SESSION['battle']['notes'][$mago]['sustained_spells'] = "Inhaerescorpus(" . $tgt . ");\n";
            } elseif (!empty($_SESSION['battle']['notes'][$mago]['sustained_spells'])) {
                $_SESSION['battle']['notes'][$mago]['sustained_spells'] .= "Inhaerescorpus(" . $tgt . ");\n";
            }
            $_SESSION['battle']['sustained_effects'][$mago]['inhaerescorpus']['dmg'] = 0;
            return "<strong>{$mago}</strong> usou " . getMagicSpecialName($mago, 'inhaerescorpus') . " (Inhaerescorpus) em <strong>{$tgt}</strong>.<br>";
        } else {
            return "<strong>{$mago}</strong> não tem PVs o suficiente para lançar Inhaerescorpus.<br>";
        }
    } else {
        return "<strong>{$tgt}</strong> passou no teste de resistência e não caiu na magia Inhaerescorpus de <strong>{$mago}</strong>.";
    }
}

function hemeopsia($mago){
    if (spendPM($mago, 2, true)) {
        if (empty($_SESSION['battle']['notes'][$mago]['sustained_spells'])) {
            $_SESSION['battle']['notes'][$mago]['sustained_spells'] = "Hemeópsia;\n";
        } elseif (!empty($_SESSION['battle']['notes'][$mago]['sustained_spells'])) {
            $_SESSION['battle']['notes'][$mago]['sustained_spells'] .= "Hemeópsia;\n";
        }
        return "<strong>{$mago}</strong> usou " . getMagicSpecialName($mago, 'hemeopsia') . " (Hemeópsia).<br>";
    } else {
        return "<strong>{$mago}</strong> não tem PVs o suficiente para usar " . getMagicSpecialName($mago, 'hemeopsia') . " (Hemeópsia).<br>";
    }
}
function visibleWithHemeopsia($mago, $alvo){
    if (strpos($_SESSION['battle']['notes'][$mago]['sustained_spells'] ?? '', 'Hemeópsia') === false){
        return false;
    }
    foreach ([
        'construto', 'mecha', 'golem', 'androide', 'nanomorfo', 'robo-positronico',
        'esqueleto', 'fantasma', 'mumia', 'zumbi',
        'elemental' 
        ] as $bloodlessTrait){
        if (in_array($bloodlessTrait, listPlayerTraits($alvo))) {
            return false;
        }
    }
    if (isset($_SESSION['battle']['notes'][$alvo]['incorp_active']) && $_SESSION['battle']['notes'][$alvo]['incorp_active'] == true){
        return false;
    }
    return true;
}

function cegueira($mago, $alvo, $testR){
    if (!statTest($alvo, 'R', 0, $testR)) {
        if (spendPM($mago, 3)) {
            $_SESSION['battle']['notes'][$alvo]['efeito'] = "Cegueira;\n";
            addPlayerTrait($alvo, 46, 'disadvantage');
            return "<strong>{$mago}</strong> usou " . getMagicSpecialName($mago, 'cegueira') . " (Cegueira) em <strong>{$alvo}</strong>.<br>";
        } else {
            return "<strong>{$mago}</strong> não tem PVs o suficiente para lançar Cegueira.<br>";
        }
    } else {
        return "<strong>{$alvo}</strong> passou no teste de resistência e não caiu na magia Cegueira de <strong>{$mago}</strong>.";
    }
}

?>