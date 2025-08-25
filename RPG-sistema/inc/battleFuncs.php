<?php
include_once 'generalFuncs.php';
include_once 'trait/traitFuncs.php';

function iniciativa(array $lutadores, array $dados): array{
    $inicList = [];
    foreach ($lutadores as $idx => $nome) {
        $H = (int) getPlayerStat($nome, 'H');
        $traits = listPlayerTraits($nome);
        if (in_array('teleporte', $traits, true)) {
            $bonus = 2;
        } elseif (in_array('aceleracao_i', $traits, true)) {
            $bonus = 1;
        } elseif (in_array('aceleracao_ii', $traits, true)) {
            $bonus = 2;
        } else {
            $bonus = 0;  
        }
        $dado = isset($dados[$idx]) ? (int) $dados[$idx] : 0;
        $total = $H + $dado + $bonus;
        $inicList[] = [
            'nome'       => $nome,
            'total'      => $total,
            'habilidade' => $H,
            'indice'     => $idx,
            'dado'       => $dado,
            'bonus'      => $bonus
        ];
    }
    usort($inicList, function ($a, $b) {
        if ($a['total'] !== $b['total']) {
            return $b['total'] <=> $a['total'];
        }
        if ($a['habilidade'] !== $b['habilidade']) {
            return $b['habilidade'] <=> $a['habilidade'];
        }
        return $a['indice'] <=> $b['indice'];
    });
    return $inicList;
}


function getValidTargets($pl, &$b, $type = 'enemies', $isIncorp = false){
    $targets = [];
    if ($type === 'allies') {
        $targets[] = $pl;
    }
    foreach ($b['order'] as $p) {
        if ($p === $pl && $type === 'enemies') continue;

        if ($type === 'enemies' && $p !== $pl) {
            $targets[] = $p;
        } elseif ($type === 'allies' && $p !== $pl) {
            $targets[] = $p;
        }
    }
    $allies = getAllAllies();
    foreach ($allies as $ally) {
        if ($ally === $pl || !in_array(getAlliePlayer($ally), $b['order'])) continue;
        $targetNotes  = $b['notes'][$ally] ?? [];
        $targetIncorp = !empty($targetNotes['incorp_active']);
        if ($isIncorp === $targetIncorp) {
            $validTargets[] = $ally;
        }
    }
    return array_unique($targets);
}

// Tests

function statTest($player, $stat, $diff, $dado){
    $meta = getPlayerStat($player, $stat) - $diff;
    if ($dado > $meta || $dado == 6) {
        return false;
    }
    if ($dado <= $meta) {
        return true;
    }
}

function movimentBuff($pl){
    $bonus = 0;
    if (in_array('aceleracao_i', listPlayerTraits($pl), true)) $bonus = 1;
    if (in_array('aceleracao_ii', listPlayerTraits($pl), true)) $bonus = 2;
    if (in_array('teleporte', listPlayerTraits($pl), true)) $bonus = 3;
    return $bonus;
}

function hDebuff($b, $pl, $tgt, $tipo = 'F'){
    return max(invisivelDebuff( $pl, $tgt, $tipo), cegoDebuff($pl, $tgt, $tipo));
}

function applyCrit($pl, $critType, $dado){
    if ($dado >= 6) {
        return getPlayerStat($pl, $critType);
    } else {
        return 0;
    }
}

function isDefeated($pl){
    if (getPlayerStat($pl, 'PV') <= 0) {
        return true;
    } else {
        return false;
    }
}


// SET volatile stats

function spendPM(string $player, int $cost, $sangue = false, $ignoreDiscount = false): bool{
    $discount = 0;
    if (!$ignoreDiscount) {
        if (!empty($_SESSION['battle']['sustained_effects'][$player]['visExVulnere']['pms'])) {
            $discount += min($_SESSION['battle']['sustained_effects'][$player]['visExVulnere']['pms'], $cost);
            if ($cost < $_SESSION['battle']['sustained_effects'][$player]['visExVulnere']['pms']) {
                $_SESSION['battle']['sustained_effects'][$player]['visExVulnere']['pms'] -= $cost;
            } else {
                $_SESSION['battle']['sustained_effects'][$player]['visExVulnere']['pms'] = 0;
            }
        }
    }
    $cost = max($cost-itemDePoder($player),1);
    $cost -= $discount;
    if ($cost < 0) {
        $cost = 0;
    }
    if ($cost <= 0) {
        return true;
    }
    $pm   = getPlayerStat($player, 'PM');
    $pv   = getPlayerStat($player, 'PV');
    $traits = listPlayerTraits($player);
    $hasEV = in_array('energia_vital', $traits, true);
    $usePv = $_SESSION['battle']['notes'][$player]['use_pv'] ?? false;

    if ($usePv || $sangue) {
        $ratio = in_array('magia_de_sangue', $traits, true) ? 1 : 2;
        $pvNeeded = $cost * $ratio;
        if ($pv >= $pvNeeded) {
            setPlayerStat($player, 'PV', $pv - $pvNeeded);
            monitorPVChange($player, $pvNeeded);
            return true;
        }
        return false;
    }
    if ($pm >= $cost) {
        setPlayerStat($player, 'PM', $pm - $cost);
        return true;
    }
    if (! $hasEV) {
        return false;
    }
    $remaining = $cost - $pm;
    setPlayerStat($player, 'PM', 0);
    $ratio = in_array('magia_de_sangue', $traits, true) ? 1 : 2;
    $pvNeeded = $remaining * $ratio;
    if ($pv >= $pvNeeded) {
        setPlayerStat($player, 'PV', $pv - $pvNeeded);
        monitorPVChange($player, $pvNeeded);
        return true;
    }
    return false;
}

function applyDamage(string $pl, string $tgt, int $dano, string $tipoDmg, string &$out = ''){
    if (!empty($_SESSION['battle']['notes'][$tgt]['incorp_active']) && $tipoDmg != 'Magia' && empty($_SESSION['battle']['notes'][$pl]['incorp_active'])) {
        $dano = 0;
        $out .= " (inútil: alvo incorpóreo)";
    } else {
        $ligacaoNatural = false;
        if (!empty(getAlliePlayer($tgt)) && in_array('ligacao_natural', listPlayerTraits(getAlliePlayer($tgt)), true)) {
            setPlayerStat($tgt, 'PV', max(getPlayerStat($tgt, 'PV') - $dano, 0));
            setPlayerStat(getAlliePlayer($tgt), 'PV', max(getPlayerStat(getAlliePlayer($tgt), 'PV') - $dano, 0));
            $ligacaoNatural = true;
        }
        if (!empty($_SESSION['battle']['playingPartner'][$tgt]) && $ligacaoNatural == false) {
            $distriDano = ceil($dano / 2);
            setPlayerStat($_SESSION['battle']['playingPartner'][$tgt]['owner'], 'PV', max(getPlayerStat($_SESSION['battle']['playingPartner'][$tgt]['owner'], 'PV') - $distriDano, 0));
            setPlayerStat($_SESSION['battle']['playingPartner'][$tgt]['name'], 'PV', max(getPlayerStat($_SESSION['battle']['playingPartner'][$tgt]['name'], 'PV') - $distriDano, 0));
        } else {
            setPlayerStat($tgt, 'PV', max(getPlayerStat($tgt, 'PV') - $dano, 0));
        }
        $ligacaoNatural = false;
        monitorPVChange($tgt, $dano);
    }
    return $out;
}


// Monitoration

function parseBuffs($equipString){
    $pattern = '/\b(F|H|R|A|PdF)([+-])(\d+)\b/';
    $buffs = [];
    if (preg_match_all($pattern, $equipString, $matches, PREG_SET_ORDER)) {
        foreach ($matches as $m) {
            [$full, $stat, $sign, $val] = $m;
            $delta = (int)$val * ($sign === '+' ? +1 : -1);
            $buffs[$stat] = ($buffs[$stat] ?? 0) + $delta;
        }
    }
    return $buffs;
}
function syncEquipBuffs($pl){
    $equipString = getPlayerStat($pl, 'equipado');
    $currentBuffs = parseBuffs($equipString);
    if (! isset($_SESSION['battle']['notes'][$pl]['buffs'])) {
        $_SESSION['battle']['notes'][$pl]['buffs'] = [];
    }
    $oldBuffs = $_SESSION['battle']['notes'][$pl]['buffs'];
    foreach ($currentBuffs as $stat => $newDelta) {
        $oldDelta = $oldBuffs[$stat] ?? 0;
        if ($newDelta !== $oldDelta) {
            $diff = $newDelta - $oldDelta;
            $base = (int) $_SESSION['battle']['orig'][$pl][$stat];
            setPlayerStat($pl, $stat, $base + $diff);
        }
    }
    foreach ($oldBuffs as $stat => $oldDelta) {
        $newDelta = $currentBuffs[$stat] ?? 0;
        if ($oldDelta !== $newDelta) {
            $diff = $newDelta - $oldDelta;
            $base  = (int) getPlayerStat($pl, $stat);
            setPlayerStat($pl, $stat, $base + $diff);
        }
    }
    $_SESSION['battle']['notes'][$pl]['buffs'] = $currentBuffs;
}

function monitorPVChange($pl, $dano){
    if (isset($_SESSION['battle']['sustained_effects'][$pl]['visExVulnere']['dmg'])) {
        $_SESSION['battle']['sustained_effects'][$pl]['visExVulnere']['dmg'] += $dano;
    }
    if (isset($_SESSION['battle']['sustained_effects'][$pl]['speculusanguis']['dmg'])) {
        $_SESSION['battle']['sustained_effects'][$pl]['speculusanguis']['dmg'] += $dano;
    }
    if (isset($_SESSION['battle']['sustained_effects'][$pl]['inhaerescorpus']['dmg'])) {
        $_SESSION['battle']['sustained_effects'][$pl]['inhaerescorpus']['dmg'] += $dano;
    }
}


// Selects

function selectTarget($cur, $validTargets){
    foreach ($validTargets as $tgt) if ($tgt !== $cur) {
        $isFuria    = ! empty($_SESSION['battle']['notes'][$tgt]['furia']);
        $isAgarrado = ! empty($_SESSION['battle']['agarrao'][$tgt]['agarrado']);
        $hasDeflexao = in_array('deflexao', listPlayerTraits($tgt), true);
        echo '<option value="' . htmlspecialchars($tgt) . '" '
            . 'data-furia="' . ($isFuria ? '1' : '0') . '" '
            . 'data-agarrao="' . ($isAgarrado ? '1' : '0') . '" '
            . 'data-tem-deflexao="' . ($hasDeflexao ? '1' : '0') . '">'
            . htmlspecialchars($tgt) . '</option>';
    }
}

function selectDmgType($cur){
    $knownTypes = listPlayerDmgTypes($cur);
    $allTypes = getAllDmgTypes();
    $unknownTypes = array_diff($allTypes, $knownTypes);
    if (!empty($knownTypes)) {
        echo '<optgroup label="Conhecidos">';
        foreach ($knownTypes as $type) {
            echo '<option value="' . htmlspecialchars($type) . '">' . htmlspecialchars($type) . '</option>';
        }
        echo '</optgroup>';
    }
    if (!empty($unknownTypes)) {
        echo '<optgroup label="Desconhecidos">';
        foreach ($unknownTypes as $type) {
            echo '<option value="' . htmlspecialchars($type) . '">' . htmlspecialchars($type) . '</option>';
        }
        echo '</optgroup>';
    }
}


// Variable effects

function manageEffects($cur){
    if (!empty($_SESSION['battle']['notes'][$cur]['use_pv'])) {
        $efeitoUsePV = "\nUsando PVs invés de PMs.";
        if (strpos($_SESSION['battle']['notes'][$cur]['efeito'], trim($efeitoUsePV)) === false) {
            $_SESSION['battle']['notes'][$cur]['efeito'] .= $efeitoUsePV;
        }
    } else {
        $_SESSION['battle']['notes'][$cur]['efeito'] = removeEffect($_SESSION['battle']['notes'][$cur]['efeito'], ['Usando PVs invés de PMs.']);
    }

    if ($_SESSION['battle']['notes'][$cur]['draco_active'] && !empty($_SESSION['battle']['notes'][$cur]['draco_flag'])) {
        unset($_SESSION['battle']['notes'][$cur]['draco_flag']);
        if (!spendPM($cur, 1)) {
            draconificacao($cur, false);
            $_SESSION['battle']['notes'][$cur]['draco_active'] = false;
            $efeitoDraco = "\nDraconificação desativada (PM esgotado) e Instável.";
            if (strpos($_SESSION['battle']['notes'][$cur]['efeito'], trim($efeitoDraco)) === false) {
                $_SESSION['battle']['notes'][$cur]['efeito'] .= $efeitoDraco;
            }
        }
    } else {
        $_SESSION['battle']['notes'][$cur]['efeito'] = removeEffect($_SESSION['battle']['notes'][$cur]['efeito'], ['Draconificação desativada (PM esgotado) e Instável.']);
    }

    if (!empty($notes['fusao_active'])) {
        $curPV = getPlayerStat($cur, 'PV');
        $curR  = getPlayerStat($cur, 'R');
        if ($curPV <= $curR) {
            $_SESSION['battle']['notes'][$cur]['furia'] = true;
            $efeitoFuria = "\nEm Fúria até o fim da batalha (Não pode usar magia nem esquiva).";
            if (strpos($_SESSION['battle']['notes'][$cur]['efeito'], trim($efeitoFuria)) === false) {
                $_SESSION['battle']['notes'][$cur]['efeito'] .= $efeitoFuria;
            }
        }
    }

    if (!empty($notes['extra_energy_next'])) {
        energiaExtra($cur);
        $_SESSION['battle']['notes'][$cur]['efeito'] .= "\nEnergia extra aplicada: PVs restaurados ao máximo.";
        unset($_SESSION['battle']['notes'][$cur]['extra_energy_next']);
    } else {
        $_SESSION['battle']['notes'][$cur]['efeito'] = removeEffect($_SESSION['battle']['notes'][$cur]['efeito'], ['Energia extra aplicada: PVs restaurados ao máximo.']);
    }

    if (!empty($notes['magia_extra_next'])) {
        magiaExtra($cur, 'apply');
        $_SESSION['battle']['notes'][$cur]['efeito'] .= "\nMagia extra aplicada: PMs restaurados ao máximo.";
        unset($_SESSION['battle']['notes'][$cur]['magia_extra_next']);
    } else {
        $_SESSION['battle']['notes'][$cur]['efeito'] = removeEffect($_SESSION['battle']['notes'][$cur]['efeito'], ['Magia extra aplicada: PMs restaurados ao máximo.']);
    }

    if (!empty($_SESSION['battle']['notes'][$cur]['invisivel']) && !empty($_SESSION['battle']['notes'][$cur]['invisivel_flag'])) {
        unset($_SESSION['battle']['notes'][$cur]['invisivel_flag']);
        if (!spendPM($cur, 1)) {
            unset($_SESSION['battle']['notes'][$cur]['invisivel']);
            $efeitoInvisibilidade = "\nInvisibilidade desativada por falta de PMs.";
            if (strpos($_SESSION['battle']['notes'][$cur]['efeito'], trim($efeitoInvisibilidade)) === false) {
                $_SESSION['battle']['notes'][$cur]['efeito'] .= $efeitoInvisibilidade;
            }
            $_SESSION['battle']['notes'][$cur]['efeito'] = removeEffect($_SESSION['battle']['notes'][$cur]['efeito'], ['Você está invisível.']);
        }
    } else if (empty($notes['invisivel'])) {
        $_SESSION['battle']['notes'][$cur]['efeito'] = removeEffect($_SESSION['battle']['notes'][$cur]['efeito'], ['Você está invisível.']);
    }


    if (in_array('furia', listPlayerTraits($cur), true)) {
        $curPV = getPlayerStat($cur, 'PV');
        $curR  = getPlayerStat($cur, 'R');
        if ($curPV <= $curR) {
            $_SESSION['battle']['notes'][$cur]['furia'] = true;
            $efeitoFuria = "\nEm Fúria até o fim da batalha (Não pode usar magia nem esquiva).";
            if (strpos($_SESSION['battle']['notes'][$cur]['efeito'], trim($efeitoFuria)) === false) {
                $_SESSION['battle']['notes'][$cur]['efeito'] .= $efeitoFuria;
            }
        }
    }
}

function removeEffect(string $efeito, array $remover): string{
    $linhas = explode("\n", $efeito);
    $linhasFiltradas = array_filter($linhas, function ($linha) use ($remover) {
        return ! in_array(trim($linha), $remover, true);
    });
    return implode("\n", $linhasFiltradas);
}


?>