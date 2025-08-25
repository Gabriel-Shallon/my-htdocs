<?php
include_once 'actFuncs.php';

//Central action file

function actSwitch($postData, &$b, $pl){
    $out = '';
    switch ($postData['action'] ?? '') {
        case 'pass':
            $out = actPass($postData, $b, $pl);
            break;

        case 'ataque':
            $out = actAtaque($postData, $b, $pl);
            break;

        case 'multiple':
            $out = actAtaqueMultiplo($postData, $b, $pl);
            break;

        case 'tiro_multiplo':
            $out = actTiroMultiplo($postData, $b, $pl);
            break;

        case 'start_concentrar':
            $out = actStartConcentrar($postData, $b, $pl);
            break;
        case 'release_concentrar':
            $out = actReleaseConcentrar($postData, $b, $pl);
            break;

        case 'agarrao':
            $out = actAgarrao($postData, $b, $pl);
            break;
        case 'soltar_agarrao':
            $out = actSoltarAgarrao($postData, $b, $pl);
            break;
        case 'se_soltar_agarrao':
            $out = actSeSoltarAgarrao($postData, $b, $pl);
            break;

        case 'ataque_debilitante':
            $out = actAtaqueDebilitante($postData, $b, $pl);
            break;

        case 'activate_draco':
            $out = actActivateDraco($postData, $b, $pl);
            break;
        case 'deactivate_draco':
            $out = actDeactivateDraco($postData, $b, $pl);
            break;

        case 'activate_fusao':
            $out = actActivateFusao($postData, $b, $pl);
            break;
        case 'deactivate_fusao':
            $out = actDeactivateFusao($postData, $b, $pl);
            break;

        case 'activate_incorp':
            $out = actActivateIncorp($postData, $b, $pl);
            break;
        case 'deactivate_incorp':
            $out = actDeactivateIncorp($postData, $b, $pl);
            break;

        case 'enable_use_pv':
            $out = actEnableUsePV($postData, $b, $pl);
            break;
        case 'disable_use_pv':
            $out = actDisableUsePV($postData, $b, $pl);
            break;

        case 'extra_energy':
            $out = actExtraEnergy($postData, $b, $pl);
            break;

        case 'magia_extra':
            $out = actMagiaExtra($postData, $b, $pl);
            break;

        case 'use_ally':
            actUseAlly($postData, $b, $pl);
            exit;
        case 'back_to_owner':
            actBackToOwner($postData, $b, $pl);
            exit;

        case 'start_partner':
            actStartPartner($postData, $b, $pl);
            exit;
        case 'end_partner':
            actEndPartner($postData, $b, $pl);
            exit;

        case 'aceleracao_ii':
            $out = actAceleracaoII($postData, $b, $pl);
            break;
        case 'aceleracao_i':
            $out = actAceleracaoI($postData, $b, $pl);
            break;

        case 'activate_invisibilidade':
            $out = actActivateInvisibilidade($postData, $b, $pl);
            break;
        case 'deactivate_invisibilidade':
            $out = actDeactivateInvisibilidade($postData, $b, $pl);
            break;

        case 'sword_luxcruentha':
            $out = actSwordLuxcruentha($postData, $b, $pl);
            break;

        case 'sustain_act':
            actSustain($postData, $b, $pl);
            break;

        case 'fim':
            actFim($postData, $b, $pl);
            exit;

        // Processamento de dados de magias
        case 'cast_magic':
            $out = actMagic($postData, $b, $pl);    
            break;

        default:
            $out = 'Ação inválida ou não reconhecida.';
            break;
    }
    return $out;
};