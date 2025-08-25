<?php
include_once './inc/generalFuncs.php';
include_once './inc/battleFuncs.php';

//'return bool' disadvantages
function muggles($player){
    if (in_array('muggles', listPlayerTraits($player))) {
        return true;
    } else {
        return false;
    }
}

function fetiche($player){
    if (in_array('fetiche', listPlayerTraits($player)) &&
        !strpos(getPlayerStat($player, 'inventario') . getPlayerStat($player, 'equipado'), '(Fetiche)')) {
        return true;
    } else {
        return false;
    }
}

function assombrado(string $player, array $input){
    $d = intval($input['roll_assombrado'] ?? 0);
    if ($d >= 4 && $d <= 6) {
        foreach (['F', 'H', 'R', 'A', 'PdF'] as $s) {
            setPlayerStat($player, $s, max(0, getPlayerStat($player, $s) - 1));
        }
        return 'Assombrado(-1 F,H,R,A,PdF)';
    }
    return null;
}


//'return value' disadvantages

function cegoDebuff($pl, $tgt, $tipo){
    if ((!in_array('cego', listPlayerTraits($pl)) && strpos($_SESSION['battle']['notes'][$pl]['efeito'] ?? '', 'Cegueira') == false) || 
    visibleWithHemeopsia($pl, $tgt)) {
        return 0;
    }
    if (in_array('radar', listPlayerTraits($pl))){
        return 1;        
    }
    if ((in_array('faro_augucado', listPlayerTraits($pl)) || in_array('audicao_agucada', listPlayerTraits($pl))) && $tipo == 'PdF'){
        return 2;
    }
    if ($tipo == 'F'){
        return 1;
    }
    if ($tipo == 'PdF'){
        return 3;
    }
    return 0;
}
