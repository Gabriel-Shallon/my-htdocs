
<?php
include_once './inc/generalFuncs.php';
include_once './inc/battleFuncs.php';
include_once './inc/trait/traitFuncs.php';


// Managers

function defaultReactionTreatment($b, $tgt, $pl, $def, $dFA, $dFD, $atkType, $dmgType, $bonusDmg = 0, $FA = 0){
    if ($FA == 0){
        $FA = FA($pl, $tgt, $atkType, $dmgType, $dFA, $bonusDmg);
    }   
    if (strpos($tgt, 'reflexo de ') !== false){
        $realName = str_replace("reflexo de ", "", $tgt);
        return max($FA - getPlayerStat($realName, 'H'), 0);
    }
    if (!empty($b['agarrao'][$tgt]['agarrado'])) {
        $def = 'indefeso';
    }
    if ($def === 'indefeso')  
        {return max($FA - FDindefeso($tgt, $dmgType), 0);}
    if ($def === 'defender_esquiva') 
        {return max($FA - FDesquiva($pl, $tgt, $dFD, $atkType, $dmgType, $FA), 0);}
    if ($def === 'defender_esquiva_deflexao') 
        {return max($FA - FDesquiva($pl, $tgt, $dFD, $atkType, $dmgType, $FA, true), 0);}
    if ($def === 'defender_sem_armadura') 
        {return max($FA - FDarmorless($tgt, $dFD, $dmgType), 0);}
    return max($FA - FD($tgt, $dFD, $dmgType), 0);
}
function atkMultiReactionTreatment($b, $q, $tgt, $pl, $dados, $dFD, $def, $tipo, $dmgType){
    $faTot = invulnerabilitieTest($pl, $dmgType, FAmulti($pl, $q, $tipo, $dados, hDebuff($b, $pl, $tgt, $tipo)));
    if (!empty($b['agarrao'][$tgt]['agarrado'])) {
        $def = 'indefeso';
    }
    if ($def === 'indefeso') {
        return max( $faTot - FD($tgt, $dFD, $dmgType), 0);
    } else if ($def === 'defender_esquiva' || $def === 'defender_esquiva_deflexao') {
        if ($def == 'defender_esquiva_deflexao') {
            $resEsq = esquivaMulti($tgt, $dFD, $pl, $tipo, true);
        } else {
            $resEsq = esquivaMulti($tgt, $dFD, $pl, $tipo);
        }
        if ($resEsq === 'defender_esquiva_success') {
            return 0;
        } else {
            return max($faTot - FDindefeso($tgt, $dmgType), 0);
        }
    } else {
        return max( $faTot - FD($tgt, $dFD, $dmgType), 0);
    }
}


function FDesquiva(string $atacante, string $defensor, int $dadoFD, string $atkType, $dmgType, $FA, $deflex = false){
    $bonus = movimentBuff($defensor);
    $defH = getPlayerStat($defensor, 'H');
    $atkH = getPlayerStat($atacante, 'H') - hDebuff($_SESSION['battle'], $atacante, $defensor, $atkType);
    if ($deflex) {
        if (spendPM($defensor, 2)) {
            $defH *= 2;
        }
    }
    $meta = ($defH + $bonus) - $atkH;
    if ($meta <= 0) {
        return FDindefeso($defensor, $dmgType);
    }
    if ($meta > 0 && $meta < 6) {
        if ($dadoFD <= $meta) {
            return $FA;
        } else {
            return FDindefeso($defensor, $dmgType);
        }
    }
    if ($meta >= 6) {
        return $FA;
    }
}

function esquivaMulti(string $defensor, int $dado, $atacante, $tipo, $deflex = false){
    $bonus = movimentBuff($defensor);
    $defH = getPlayerStat($defensor, 'H');
    $atkH = hDebuff($_SESSION['battle'], $atacante, $defensor, $tipo);
    if ($deflex) {
        if (spendPM($defensor, 2)) {
            $defH *= 2;
        }
    }
    $meta = ($defH + $bonus) - $atkH;
    if ($meta <= 0) {
        return 'defender_esquiva_fail';
    }
    if ($dado <= $meta) {
        return 'defender_esquiva_success';
    } else {
        return 'defender_esquiva_fail';
    }
}


// Employees

function FA(string $atacante, $tgt, string $atkType, $dmgType, int $dado, $bonusDmg = 0){
    return invulnerabilitieTest($tgt, $dmgType, 
    getPlayerStat($atacante, $atkType) + (getPlayerStat($atacante, 'H') - hDebuff($_SESSION['battle'], $atacante, $tgt, $atkType)) + 
    $dado + applyCrit($atacante, $atkType, $dado) + $bonusDmg);
}
function FD(string $defensor, int $dado, $dmgType){
    return vulnerabilitieExtraArmorTest($defensor, $dmgType) + getPlayerStat($defensor, 'H') + $dado + 
    applyCrit($defensor, 'A', $dado) + resistenciaMagia($defensor, $dmgType);
}
function FDindefeso(string $defensor, $dmgType){
    return vulnerabilitieExtraArmorTest($defensor, $dmgType) + resistenciaMagia($defensor, $dmgType);
}

function FDarmorless(string $defensor, int $dado, $dmgType){
    return getPlayerStat($defensor, 'H') + $dado + resistenciaMagia($defensor, $dmgType) + 
    max(vulnerabilitieExtraArmorTest($defensor, $dmgType)-getPlayerStat($defensor, 'A'), 0);
}

function FAmulti(string $atacante, int $quant, string $atkType, array $dados, $H){
    $maxExtras  = intdiv(max($H, 0), 2);
    $quant = min($quant, $maxExtras + 1);
    $effH = max($H - ($quant * 2) + 2, 0);
    $baseFA = getPlayerStat($atacante, $atkType) + $effH;
    $totalFA = 0;
    $count = min($quant, count($dados));
    for ($i = 0; $i < $count; $i++) {
        $totalFA += ($baseFA + (int)$dados[$i]) + applyCrit($atacante, $atkType, $dados[$i]);
    }
    return $totalFA;
}