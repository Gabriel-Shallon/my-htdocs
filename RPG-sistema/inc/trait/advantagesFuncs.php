<?php
include_once './inc/generalFuncs.php';
include_once './inc/battleFuncs.php';

//'SET stat' advantages

function PMextra(string $player){
    setPlayerStat($player, 'PM_max', (getPlayerStat($player, 'PM_max') + 10));
    setPlayerStat($player, 'PM', (getPlayerStat($player, 'PM') + 10));
    return $player . ' ganhou +10 PMs';
};
function deapplyPMextra(string $player){
    setPlayerStat($player, 'PM_max', (getPlayerStat($player, 'PM_max') - 10));
    return $player . ' perdeu 10 PMs';
};
function PVextra(string $player){
    setPlayerStat($player, 'PV_max', (getPlayerStat($player, 'PV_max') + 10));
    setPlayerStat($player, 'PV', (getPlayerStat($player, 'PV') + 10));
    return $player . ' ganhou +10 PMs';
};
function deapplyPVextra(string $player){
    setPlayerStat($player, 'PV', (getPlayerStat($player, 'PV') - 10));
    return $player . ' perdeu 10 PMs';
};

function draconificacao(string $player, bool $active){
    if ($active) {
        setPlayerStat($player, 'PdF', getPlayerStat($player, 'PdF') + 1);
        setPlayerStat($player, 'R',   getPlayerStat($player, 'R') + 1);
        setPlayerStat($player, 'H',   getPlayerStat($player, 'H') + 2);
    } else {
        setPlayerStat($player, 'PdF', getPlayerStat($player, 'PdF') - 1);
        setPlayerStat($player, 'R',   getPlayerStat($player, 'R') - 1);
        setPlayerStat($player, 'H',   getPlayerStat($player, 'H') - 2);
    }
}

function fusaoEterna(string $player, int $PdFOriginal, bool $active){
    if ($active == true) {
        if (! spendPM($player, 3)) {
            throw new Exception("PM/PVs insuficientes para Fusão Eterna.");
        }
        addPlayerInvulnerability($player, 'Fogo');
        addPlayerVulnerability($player, 'Elétrico');
        addPlayerVulnerability($player, 'Sônico');
        setPlayerStat($player, 'F', getPlayerStat($player, 'PdF') * 2);
        setPlayerStat($player, 'PdF', 0);
        addPlayerTrait($player, 81, 'advantage');
        addPlayerTrait($player, 30, 'disadvantage');
    } else {
        removePlayerInvulnerability($player, 'Fogo');
        removePlayerVulnerability($player, 'Sônico');
        removePlayerVulnerability($player, 'Elétrico');
        setPlayerStat($player, 'F', (getPlayerStat($player, 'F') - $PdFOriginal * 2));
        setPlayerStat($player, 'PdF', $PdFOriginal);
        removePlayerTrait($player, 81, 'advantage');
        removePlayerTrait($player, 30, 'disadvantage');
    }
}

function energiaExtra($player){
    setPlayerStat($player, 'PV', getPlayerStat($player, 'PV_max'));
}


//'return bool' advantages

function agarrao(string $player, string $alvo, int $dF){
    $result = ($dF + getPlayerStat($player, 'F')) - getPlayerStat($alvo, 'F');
    if ($result <= 0) {
        return false;
    }
    if ($result > 0) {
        return true;
    }
}

function debilitateStat($alvo, $stat, $dado){
    if ($dado > getPlayerStat($alvo, 'A') || $dado == 6) {
        setPlayerStat($alvo, $stat, getPlayerStat($alvo, $stat) - 1);
        return $stat . ' de ' . $alvo . ' -1';
    } {
        return $alvo . ' não foi debilitado.';
    }
}

function magiaExtra($player, $act){
    if ($act == 'spend') {
        $pv   = getPlayerStat($player, 'PV');
        $R    = getPlayerStat($player, 'R');
        if ($pv <= $R) {
            setPlayerStat($player, 'PV', $pv - 2);
            return true;
        }
        return false;
    }
    if ($act == 'apply') {
        setPlayerStat($player, 'PM', getPlayerStat($player, 'PM_max'));
    }
}

function itemDePoder($player){
    $inventario = getPlayerStat($player, 'inventario');
    $equipado = getPlayerStat($player, 'equipado');
    $itens = $equipado . $inventario;
    if (strpos($itens, '(Item de Poder)')) {
        return 2;
    } else {
        return 0;
    }
}

function resistenciaMagia($player, $dmgType = 'Magia'){
    if (in_array('resistencia_a_magia', listPlayerTraits($player), true) && $dmgType == 'Magia') {
        return 2;
    } else {
        return 0;
    }
}

function verOInvisivel($pl){
    return in_array('ver_o_invisivel', listPlayerTraits($pl)) || in_array('xama', listPlayerTraits($pl));
}

function visibleWithInfravision($mago, $alvo){
    if (!in_array('infravisao', listPlayerTraits($mago))){
        return false;
    }
    if (!isHot($alvo)){
        return false;
    }
    if (isset($_SESSION['battle']['notes'][$alvo]['incorp_active']) && $_SESSION['battle']['notes'][$alvo]['incorp_active'] == true){
        return false;
    }
    return true;
}


//'return value' advantages

function invisivelDebuff($pl, $tgt, $tipo){
    if (empty($_SESSION['battle']['notes'][$tgt]['invisivel']) || verOInvisivel($pl) || visibleWithHemeopsia($pl, $tgt)) {
        return 0;
    }
    if ((in_array('faro_augucado', listPlayerTraits($pl)) || in_array('audicao_agucada', listPlayerTraits($pl))) && $tipo == 'PdF'){
        return 2;
    }
    if ($tipo == 'F' || in_array('radar', listPlayerTraits($pl))){
        return 1;
    }
    if ($tipo == 'PdF'){
        return 3;
    }
    return 0;
}
// QUANDO HOUVER DETECÇÃO DE MAGIA, ADICIONAR NAS FUNÇÕES ABAIXO E ACIMA, E NA DE CEGO
function reflexosQtd($pl, $tgt){
    $reflexQtd = 0;
    if (!empty($_SESSION['battle']['sustained_effects'][$tgt]['reflexos'])){
        $reflexQtd = $_SESSION['battle']['sustained_effects'][$tgt]['reflexos'];
        if (in_array('faro_augucado', listPlayerTraits($pl))){
            $reflexQtd -= 2;
        }
        if (visibleWithHemeopsia($pl, $tgt) || 
            in_array('radar', listPlayerTraits($pl)) || 
            verOInvisivel($pl) ||
            strpos($_SESSION['battle']['notes'][$pl]['sustained_spells'], "Deteccao De Magia") !== false){
            $reflexQtd = 0;
        }
    }
    return max($reflexQtd, 0);
}

function FAtiroMultiplo(string $atacante, int $quant, array $dados, string $tgt, string $defesa, int $dadoFD, $dmgType): int{
    $H = hDebuff($_SESSION['battle'], $atacante, $tgt, 'PdF');
    $PdF    = (int) getPlayerStat($atacante, 'PdF');
    $maxT   = max($H, 0);
    $q      = min($quant, $maxT);
    $danoTotal = 0;
    for ($i = 0; $i < $q; $i++) {
        $rollFA = isset($dados[$i]) ? (int)$dados[$i] : 0;
        $FA     = $PdF + $H + $rollFA;
        if ($defesa === 'indefeso') {
            $FDval = FDindefeso($tgt, $dmgType);
        } if ($defesa === 'defender_sem_armadura') {
            $FDval = FDarmorless($tgt, $dadoFD, $dmgType);
        } else {
            $FDval = FD($tgt, $dadoFD, $dmgType);
        }
        $danoTotal +=max(invulnerabilitieTest($tgt, $dmgType, $FA) - $FDval, 0);
    }
    return $danoTotal;
}

function tiroMultiReactionTreatment($b, $q, $tgt, $pl, $dados, $dadoFD, $def, $dmgType){
    if (!spendPM($pl, $q)) {
        throw new Exception("PM/PVs insuficientes para {$q} tiros.");
    }
    if (!empty($b['agarrao'][$tgt]['agarrado'])) {
        $def = 'indefeso';
    }
    if ($def === 'defender_esquiva' || $def === 'defender_esquiva_deflexao') {
        if ($def == 'defender_esquiva_deflexao') {
            $resEsq = esquivaMulti($tgt, $dadoFD, $pl, 'PdF', true);
        } else {
            $resEsq = esquivaMulti($tgt, $dadoFD, $pl, 'PdF');
        }
        if ($resEsq === 'defender_esquiva_success') {
            return 0;
        } else {
            return FAtiroMultiplo($pl, $q, $dados, $tgt, 'indefeso', $dadoFD, $dmgType);
        }
    } else {
        $tipoDef = $def === 'indefeso' ? 'indefeso' : 'defender';
        return FAtiroMultiplo($pl, $q, $dados, $tgt, $tipoDef, $dadoFD, $dmgType);
    }
}
?>