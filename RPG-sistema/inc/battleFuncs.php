<?php

include_once 'generalFuncs.php';
include_once 'traitFuncs.php';

    function iniciativa(array $lutadores, array $dados): array {
        $inicList = [];
        foreach ($lutadores as $idx => $nome) {
            $H = (int) getPlayerStat($nome, 'H');
            $traits = listPlayerTraits($nome);
            if (in_array('teleporte', $traits, true)) {
                $bonus = 2;
            } elseif (in_array('aceleracao', $traits, true)) {
                $bonus = 1;
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
            ];
        }
        usort($inicList, function($a, $b) {
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





    
    function FA (string $atacante, string $atkType, int $dado){
        return getPlayerStat($atacante, $atkType) + getPlayerStat($atacante, 'H') + $dado + applyCrit($atacante, $atkType, $dado);
    }


    function FD (string $defensor, int $dado, $dmgType){
        return vulnerabilitieExtraArmorTest($defensor, $dmgType) + getPlayerStat($defensor, 'H') + $dado + applyCrit($defensor, 'A', $dado);
    }


    function FDindefeso (string $defensor, $dmgType){
        return vulnerabilitieExtraArmorTest($defensor, $dmgType);
    }
    
    function FAmulti(string $atacante, int $quant, string $atkType, array $dados) {
        $H = (int) getPlayerStat($atacante, 'H');
    
        $maxExtras  = intdiv(max($H, 0), 2);
        $quant = min($quant, $maxExtras + 1);
    
        $effH = max($H - ($quant * 2)+2, 0);
    
        $baseFA = getPlayerStat($atacante, $atkType) + $effH;
    
        $totalFA = 0;
        $count = min($quant, count($dados));
        for ($i = 0; $i < $count; $i++) {
            $totalFA += ($baseFA + (int)$dados[$i]) + applyCrit($atacante, $atkType, $dados[$i]);
        }
    
        return $totalFA;
    }
    




    function FAFDresult(string $atacante, string $defensor, int $dadoFA, int $dadoFD, string $atkType, string $dmgType){
        return max(invulnerabilitieTest($defensor, $dmgType,FA($atacante, $atkType, $dadoFA)) - FD($defensor,$dadoFD, $dmgType),0);
    }


    
    function FAFDindefeso(string $atacante, string $defensor, int $dadoFA, string $atkType, string $dmgType){
        return max(invulnerabilitieTest($defensor, $dmgType,FA($atacante, $atkType, $dadoFA)) - FDindefeso($defensor, $dmgType),0);
    }




    function FAFDesquiva(string $atacante, string $defensor,int $dadoFD, int $dadoFA, string $atkType, $dmgType){
        $bonus = 0;
        if (in_array('aceleracao_i', listPlayerTraits($defensor), true)) {$bonus = 1;};
        if (in_array('teleporte', listPlayerTraits($defensor), true)) {$bonus = 2;};
        $meta = (getPlayerStat($defensor, 'H') + $bonus) - getPlayerStat($atacante, 'H');
        if ($meta <= 0){
            return FAFDindefeso($atacante, $defensor, $dadoFA, $atkType, $dmgType);
        }
        if ($meta > 0 && $meta < 6){
            if($dadoFD <= $meta){
                return 0;
            }else{
                return FAFDindefeso($atacante, $defensor, $dadoFA, $atkType, $dmgType);
            }
        }
        if ($meta>6){
            return 0;
        }
    }

    function esquivaMulti(string $atacante, string $defensor, int $dado): string {
        $bonus = 0;
        if (in_array('aceleracao_i', listPlayerTraits($defensor), true)) {$bonus = 1;};
        if (in_array('teleporte', listPlayerTraits($defensor), true)) {$bonus = 2;};
        $meta = (getPlayerStat($defensor, 'H') + $bonus) - getPlayerStat($atacante, 'H');
        if ($meta <= 0) {
            return 'defender_esquiva_fail';
        }
        if ($dado <= $meta) {
            return 'defender_esquiva_success';
        } else {
        return 'defender_esquiva_fail';
        }
    }








    function defaultReactionTreatment($b, $tgt, $pl, $def, $dFA, $dFD, $tipo, $dmgType){
        if (!empty($b['agarrao'][$tgt]['agarrado'])) {
            $def = 'indefeso';
        }
        if ($def === 'indefeso') {
            return FAFDindefeso($pl, $tgt, $dFA, $tipo, $dmgType);
        } elseif ($def === 'defender_esquiva') {
            return FAFDesquiva($pl, $tgt, $dFD, $dFA, $tipo, $dmgType);
        } else {
            return FAFDresult($pl, $tgt, $dFA, $dFD, $tipo, $dmgType);
        }
    }

    function atkMultiReactionTreatment($b, $q, $tgt, $pl, $dados, $dFD, $def, $tipo, $dmgType){
        $faTot = FAmulti($pl, $q, $tipo, $dados);
        if (!empty($b['agarrao'][$tgt]['agarrado'])) {
            $def = 'indefeso';
        }
        if ($def === 'indefeso') {
            return max(invulnerabilitieTest($tgt, $dmgType, $faTot) - FD($tgt, $dFD, $dmgType), 0);
        } else if ($def === 'defender_esquiva') {
            $resEsq = esquivaMulti($pl, $tgt, $dFD);
            if ($resEsq === 'defender_esquiva_success') {
                return 0;
            } else {
                return max(invulnerabilitieTest($tgt, $dmgType, $faTot) - FD($tgt, $dFD, $dmgType), 0);
            }
        } else {
            return max(invulnerabilitieTest($tgt, $dmgType, $faTot) - FD($tgt, $dFD, $dmgType), 0);
        }
    }








    function extraArmorTest($player, $dmgType){
        if (in_array($dmgType, listPlayerExtraArmor($player))){
            return getPlayerStat($player, 'A')*2;
        } else {
            return getPlayerStat($player, 'A');
        }
    }

    function invulnerabilitieTest($player, $dmgType, $FA){
        if (in_array($dmgType, listPlayerInvulnerabilities($player))){
            $FA = $FA/10;
            return floor($FA);
        } else {
            return $FA;
        }
    }

    function vulnerabilitieTest($player, $dmgType){
        if (in_array($dmgType, listPlayerVulnerabilities($player))){
            return 0;
        } else {
            return getPlayerStat($player, 'A');
        }
    }


    function vulnerabilitieExtraArmorTest($player, $dmgType){
        $hasVulnerabilitie = false;
        $hasExtraArmor = false;
        foreach (listPlayerVulnerabilities($player) as $type){
            if ($type == $dmgType){
                $hasVulnerabilitie = true;
            }
        }
        foreach (listPlayerExtraArmor($player) as $type){
            if ($type == $dmgType){
                $hasExtraArmor = true;
            }
        }

        if (($hasExtraArmor && $hasVulnerabilitie) or (!$hasExtraArmor && !$hasVulnerabilitie)){
            return getPlayerStat($player, 'A');
        } 
        if ($hasExtraArmor && !$hasVulnerabilitie) {
            return getPlayerStat($player, 'A')*2;
        } 
        if (!$hasExtraArmor && $hasVulnerabilitie){
            return 0; 
        }
    }







    function spendPM(string $player, int $cost): bool {
        $pm   = getPlayerStat($player, 'PM');
        $pv   = getPlayerStat($player, 'PV');
        $traits = listPlayerTraits($player);
        $hasEV = in_array('energia_vital', $traits, true);
        $usePv = $_SESSION['battle']['notes'][$player]['use_pv'] ?? false;
        if ($usePv) {
            $ratio = in_array('magia_de_sangue', $traits, true) ? 1 : 2;
            $pvNeeded = $cost * $ratio;
            if ($pv >= $pvNeeded) {
                setPlayerStat($player, 'PV', $pv - $pvNeeded);
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
            return true;
        }
        setPlayerStat($player, 'PV', $pv);
        setPlayerStat($player, 'PM', $pm);
        return false;
    }


    function applyCrit($pl, $critType, $dado){
        if($dado == 6){
            return getPlayerStat($pl, $critType);
        } else {
            return 0;
        }
    }

    function applyDamage(string $pl, string $tgt, int $dano, string $tipo, string &$out){
        if (!empty($_SESSION['battle']['notes'][$tgt]['incorp_active']) && in_array($tipo, ['F','PdF'], true) && empty($_SESSION['battle']['notes'][$pl]['incorp_active'])) {
            $dano = 0;
            $out .= " (inútil: alvo incorpóreo)";
        } else {
            $ligacaoNatural = false;
            if (!empty(getAlliePlayer($tgt)) && in_array('ligacao_natural', listPlayerTraits(getAlliePlayer($tgt)), true)){       
                setPlayerStat($tgt, 'PV', max(getPlayerStat($tgt,'PV') - $dano, 0));
                setPlayerStat(getAlliePlayer($tgt), 'PV', max(getPlayerStat(getAlliePlayer($tgt),'PV') - $dano, 0));
                $ligacaoNatural = true;
            } 
            if (!empty($_SESSION['battle']['playingPartner'][$tgt]) && $ligacaoNatural == false){ 
                $distriDano = ceil($dano/2);
                setPlayerStat($_SESSION['battle']['playingPartner'][$tgt]['owner'], 'PV', max(getPlayerStat($_SESSION['battle']['playingPartner'][$tgt]['owner'],'PV') - $distriDano, 0));
                setPlayerStat($_SESSION['battle']['playingPartner'][$tgt]['name'], 'PV', max(getPlayerStat($_SESSION['battle']['playingPartner'][$tgt]['name'],'PV') - $distriDano, 0));
            } else {
                setPlayerStat($tgt, 'PV', max(getPlayerStat($tgt,'PV') - $dano, 0));
            }
            $ligacaoNatural = false;
        }
       return $out; 
    }







    function removeEffect(string $efeito, array $remover): string {
        $linhas = explode("\n", $efeito);

        $linhasFiltradas = array_filter($linhas, function($linha) use ($remover) {
            return ! in_array(trim($linha), $remover, true);
        });

        return implode("\n", $linhasFiltradas);
    }
  



    function selectTarget($cur, $validTargets) {
        foreach ($validTargets as $tgt) if ($tgt !== $cur) {
            $isFuria    = ! empty($_SESSION['battle']['notes'][$tgt]['furia']);
            $isAgarrado = ! empty($_SESSION['battle']['agarrao'][$tgt]['agarrado']);
            echo '<option value="'.htmlspecialchars($tgt).'" '
            .'data-furia="'.($isFuria    ? '1' : '0').'" '
            .'data-agarrao="'.($isAgarrado? '1' : '0').'">'
            .htmlspecialchars($tgt).'</option>';                              
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