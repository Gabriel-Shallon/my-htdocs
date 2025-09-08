<?php
include_once 'magicActFuncs.php';

function magicSwitch($postData, &$b, $pl, $magic_slug, $target){
    $out = '';
    if ($target != $pl){
        if (strpos($_SESSION['battle']['notes'][$target]['sustained_spells'], 'Retribuicao de Wynna') !== false){
            $out .= $target.' está sob efeito da magia Retribuição de Wynna!<br>';
            $target = $pl;
        }
    }
    if ($magic_slug == 'ataque_magico' || $magic_slug == 'lanca_infalivel_de_talude'){
        foreach ($postData['magic_targets'] as &$tgtInfo){
            if (strpos($_SESSION['battle']['notes'][$tgtInfo['name']]['sustained_spells'], 'Retribuicao de Wynna') !== false){
                $out .= $tgtInfo['name'].' está sob efeito da magia Retribuição de Wynna!<br>';
                $tgtInfo['name'] = $pl;
            }
        }
    }
    switch ($magic_slug) {
        case 'bola_de_fogo':
            $tgts = $postData['magic_targets'] ?? ['']; // Info do alvo: name + dFD
            $PMs = $postData['magic_pm_cost'] ?? [''];
            $dadoFA = $postData['dadoFA'] ?? [''];

            $out .= bolaDeFogo($pl, $tgts, $PMs, $dadoFA);

            unset($b['playingAlly']);
            $b['init_index']++;
            break;

        case 'ataque_magico':
            $pmCost = $postData['magic_pm_cost'] ?? 1;
            $atkType = $postData['magic_attack_type'] ?? 'F';
            $tgtsInfo = $postData['magic_targets'] ?? ['']; //alvos [name] + reação [reaction] + dado de defesa [rollFD] + dado de ataque [rollFA]

            $out .= ataqueMagico($b, $pl, $tgtsInfo, $pmCost, $atkType);

            unset($b['playingAlly']);
            $b['init_index']++;
            break;

        case 'lanca_infalivel_de_talude':
            $pmCost = $postData['magic_pm_cost'] ?? 1;
            $tgtsInfo = $postData['magic_targets'] ?? ['']; //alvos [name] + lancas atacando ele [qtdAtk]

            $out .= lancaInfalivelDeTalude($pl, $tgtsInfo, $pmCost);

            unset($b['playingAlly']);
            $b['init_index']++;
            break;

        case 'sacrificio_de_marah':
            $tgtsInfo = $postData['magic_targets'] ?? ['']; //alvos [name]

            $out .= sacrificioDeMarah($pl, $tgtsInfo);

            unset($b['playingAlly']);
            $b['init_index']++;
            break;

        case 'brilho_explosivo':
            $dadoFD = $postData['dadoFD'] ?? [''];
            $def = $postData['magic_def'];

            $somaDosDadosFA = 0;
            for ($i = 1; $i < 11; $i++) {
                $somaDosDadosFA += $postData['dado' . $i] ?? [0];
            }

            $out .= brilhoExplosivo($pl, $target, $somaDosDadosFA, $dadoFD, $def);

            unset($b['playingAlly']);
            $b['init_index']++;
            break;

        case 'morte_estelar':
            $out .= morteEstelar($pl, $target);
            unset($b['playingAlly']);
            $b['init_index']++;
            break;

        case 'enxame_de_trovoes':
            $dFA1 = $postData['dadoFA1'] ?? [''];
            $dFA2 = $postData['dadoFA2'] ?? [''];
            $dFD = $postData['dadoFD'] ?? [''];
            $def = $postData['magic_def'];

            $out .= enxameDeTrovoes($b, $pl, $target, $dFA1, $dFA2, $dFD, $def);

            unset($b['playingAlly']);
            $b['init_index']++;
            break;

        case 'nulificacao_total_de_talude':
            $RTest = $postData['RTest'] ?? [''];

            $out .= nulificacaoTotalDeTalude($pl, $target, $RTest);

            unset($b['playingAlly']);
            $b['init_index']++;
            break;

        case 'bola_de_fogo_instavel':
            $tgts = $postData['magic_targets'] ?? ['']; // Info do alvo: name + dFD + def
            $PMs = $postData['magic_pm_cost'] ?? [''];
            $dadosFA = $postData['dados'] ?? [''];

            $out .= bolaDeFogoInstavel($pl, $tgts, $PMs, $dadosFA);

            unset($b['playingAlly']);
            $b['init_index']++;
            break;

        case 'bola_de_lama':
            $dadosFA = $postData['dadosFA'] ?? [''];
            $dadoFD = $postData['dadoFD'] ?? [''];
            $def = $postData['magic_def'] ?? [''];

            $out .= bolaDeLama($pl, $target, $dadosFA, $dadoFD, $def);

            unset($b['playingAlly']);
            $b['init_index']++;
            break;

        case 'bomba_de_luz':
            $tgts = $postData['magic_targets'] ?? [''];  // Info do alvo: name + dFD + def
            $PMs = $postData['magic_pm_cost'] ?? [''];

            $out .= bombaDeLuz($pl, $tgts, $PMs);

            unset($b['playingAlly']);
            $b['init_index']++;
            break;

        case 'bomba_de_terra':
            $dadoFD = $postData['dadoFD'] ?? [''];
            $def = $postData['magic_def'] ?? [''];

            $out .= bombaDeTerra($pl, $target, $dadoFD, $def);

            unset($b['playingAlly']);
            $b['init_index']++;
            break;

        case 'solisanguis':
            $dadoCusto = $postData['dadoCusto'] ?? [''];
            $dadoFA1 = $postData['dadoFA1'] ?? [''];
            $dadoFA2 = $postData['dadoFA2'] ?? [''];

            $out .= solisanguis($pl, $target, $dadoCusto, $dadoFA1, $dadoFA2);

            unset($b['playingAlly']);
            $b['init_index']++;
            break;

        case 'solisanguis_ruptura':
            $dadoCusto = $postData['dadoCusto'] ?? [''];
            $dadoFA1 = $postData['dadoFA1'] ?? [''];
            $dadoFA2 = $postData['dadoFA2'] ?? [''];
            $dadoFA3 = $postData['dadoFA3'] ?? [''];
            $dadoFA4 = $postData['dadoFA4'] ?? [''];

            $out .= solisanguisRuptura($pl, $target, $dadoCusto, $dadoFA1, $dadoFA2, $dadoFA3, $dadoFA4);

            unset($b['playingAlly']);
            $b['init_index']++;
            break;


        case 'solisanguis_evisceratio':
            $dado = $postData['dado'] ?? [''];

            $out .= solisanguisEvisceratio($pl, $target, $dado);

            unset($b['playingAlly']);
            $b['init_index']++;
            break;

        case 'sortilegium':
            $dadoCusto = $postData['dadoCusto'] ?? [''];
            $dadoPV1 = $postData['dadoPV1'] ?? [''];
            $dadoPV2 = $postData['dadoPV2'] ?? [''];

            $out .= sortilegium($pl, $target, $dadoCusto, $dadoPV1, $dadoPV2);

            unset($b['playingAlly']);
            $b['init_index']++;
            break;

        case 'sancti_sanguis':
            $qtd = $postData['qtd'] ?? [''];

            $out .= sanctiSanguis($pl, $target, $qtd);

            unset($b['playingAlly']);
            $b['init_index']++;
            break;

        case 'luxcruentha':
            $out .= luxcruentha($pl);
            break;

        case 'artifanguis':
            $obj = $postData['obj'] ?? [''];
            $cost = $postData['cost'] ?? [''];

            $out .= artifanguis($pl, $obj, $cost);
            break;

        case 'excruentio':
            $dFD = $postData['dadoFD'] ?? [''];
            $dFA1 = $postData['dadoFA1'] ?? [''];
            $dFA2 = $postData['dadoFA2'] ?? [''];
            $def = $postData['magic_def'] ?? [''];

            $out .= excruentio($pl, $target, $dFD, $dFA1, $dFA2, $def);

            unset($b['playingAlly']);
            $b['init_index']++;
            break;


        case 'speculusanguis':
            $out .= speculusanguis($pl, $target);
            unset($b['playingAlly']);
            $b['init_index']++;
            break;


        case 'vis_ex_vulnere':
            $out .= visExVulnere($pl);
            unset($b['playingAlly']);
            $b['init_index']++;
            break;

        case 'solcruoris':
            $cost = $postData['cost'] ?? [''];

            $out .= solcruoris($pl, $cost);

            unset($b['playingAlly']);
            $b['init_index']++;
            break;

        case 'spectraematum':
            $debuff = $postData['debuff'] ?? [''];

            $out .= spectraematum($pl, $debuff, $target);

            unset($b['playingAlly']);
            $b['init_index']++;
            break;

        case 'aeternum_tribuo':
            $out .= aeternumTribuo($pl, $target);
            unset($b['playingAlly']);
            $b['init_index']++;
            break;

        case 'inhaerescorpus':
            $testR = $postData['testR'] ?? [''];

            $out .= inhaerescorpus($pl, $target, $testR);

            unset($b['playingAlly']);
            $b['init_index']++;
            break;

        case 'hemeopsia':
            $out .= hemeopsia($pl);
            break;

        case 'cegueira':
            $testR = $postData['testR'] ?? [''];

            $out .= cegueira($pl, $target, $testR);

            unset($b['playingAlly']);
            $b['init_index']++;
            break;

        case 'amor_incontestavel':
            $tgtLove = $postData['magic_love'] ?? [''];
            $testR = $postData['testR'] ?? [''];

            $out .= amorIncontestavel($pl, $target, $tgtLove, $testR);

            unset($b['playingAlly']);
            $b['init_index']++;
            break;

        case 'ataque_vorpal':
            $out .= ataqueVorpal($pl, $target);
            unset($b['playingAlly']);
            $b['init_index']++;
            break;
        
        case 'cura_para_o_mal':
            $evilCureMode = $postData['evil_cure_mode'] ?? [''];

            $out .= curaParaOMal($pl, $target, $evilCureMode);

            unset($b['playingAlly']);
            $b['init_index']++;
            break;

        case 'desmaio':
            $cost = $postData['cost'] ?? [''];
            $testR = $postData['testR'] ?? [''];

            $out .= desmaio($pl, $target, $cost, $testR);

            unset($b['playingAlly']);
            $b['init_index']++;
            break;

        case 'destrancar':
            $out .= destrancar($pl);
            unset($b['playingAlly']);
            $b['init_index']++;
            break;

        case 'escapatoria_de_valkaria':
            $qtd = $postData['qtdAlly'] ?? [''];

            $out .= aEscapatoriaDeValkaria($pl, $qtd);

            unset($b['playingAlly']);
            $b['init_index']++;
            break;

        case 'fada_servil':
            $out .= fadaServil($pl);
            unset($b['playingAlly']);
            $b['init_index']++;
            break;

        case 'farejar_tesouro':
            $out .= farejarTesouro($pl);
            unset($b['playingAlly']);
            $b['init_index']++;
            break;

        case 'flor_perene_de_milady_a':
            $testR = $postData['testR'] ?? [''];

            $out .= florPereneDeMiladyA($pl, $target, $testR);

            unset($b['playingAlly']);
            $b['init_index']++;
            break;

        case 'furtividade_de_hyninn':
            $out .= furtividadeDeHyninn($pl, $target);
            unset($b['playingAlly']);
            $b['init_index']++;
            break;

        case 'luz':
            $out .= luz($pl);
            break;

        case 'protecao_magica_superior':
            $custo = $postData['custo'] ?? [''];

            $out .= protecaoMagicaSuperior($pl, $target, $custo);

            unset($b['playingAlly']);
            $b['init_index']++;
            break;

        case 'protecao_magica':
            $custo = $postData['custo'] ?? [''];

            $out .= protecaoMagica($pl, $target, $custo);

            unset($b['playingAlly']);
            $b['init_index']++;
            break;

        case 'recuperacao_natural':
            $out .= recuperacaoNatural($pl, $target);
            unset($b['playingAlly']);
            $b['init_index']++;
            break;

        case 'reflexos':
            $dado = $postData['dado'] ?? [''];

            $out .= reflexos($pl, $dado);

            unset($b['playingAlly']);
            $b['init_index']++;
            break;

        case 'retribuicao_de_wynna':
            $out .= retribuicaoDeWynna($pl);
            unset($b['playingAlly']);
            $b['init_index']++;
            break;

        case 'sentidos_especiais_magia':
            $magicSense = $postData['magic_sense'] ?? [''];

            $out .= sentidosEspeciais($pl, $magicSense);

            unset($b['playingAlly']);
            $b['init_index']++;
            break;

        case 'teleportacao_aprimorada':
            $cost = $postData['cost'] ?? [''];

            $out .= teleportacaoAprimorada($pl, $cost);

            unset($b['playingAlly']);
            $b['init_index']++;
            break; 

        case 'teleportacao':
            $cost = $postData['cost'] ?? [''];

            $out .= teleportacao($pl, $cost);
            
            unset($b['playingAlly']);
            $b['init_index']++;
            break; 

        case 'teleportacao_planar':
            $cost = $postData['cost'] ?? [''];

            $out .= teleportacaoPlanar($pl, $cost);

            unset($b['playingAlly']);
            $b['init_index']++;
            break; 

        case 'transporte':
            $cost = $postData['cost'] ?? [''];

            $out .= transporte($pl, $cost);
            
            unset($b['playingAlly']);
            $b['init_index']++;
            break; 

        case 'deteccao_de_magia':
            $out .= deteccaoDeMagia($pl);
            unset($b['playingAlly']);
            $b['init_index']++;
            break; 

        case 'raio_desintegrador':
            $cost = $postData['cost'] ?? [1];
            $testR = $postData['testR'] ?? [0];

            $out .= raioDesintegrador($pl, $target, $cost, $testR);

            unset($b['playingAlly']);
            $b['init_index']++;
            break;

        case 'excidium_stellae':
            $tgts = $postData['magic_targets'] ?? ['']; // Info do alvo: name + dFD + def
            $dadosFA = $postData['dados'] ?? [''];

            $out .= excidiumStellae($pl, $tgts, $dadosFA);

            unset($b['playingAlly']);
            $b['init_index']++;
            break;

        default:
            $out .= "A magia '{$magic_slug}' foi selecionada, mas sua lógica ainda não foi implementada.";
            unset($b['playingAlly']);
            $b['init_index']++;
            break;
    }
    return $out;
}   