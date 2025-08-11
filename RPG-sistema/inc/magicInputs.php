<?php

function renderMagicInputs($slug, $caster, &$battle) {
    switch ($slug) {
        case 'bola_de_fogo':
            inputBolaDeFogo($caster, $battle);
            break;

        case 'cura_magica':
            inputCuraMagica($caster, $battle);
            break;
            
        case 'cancelamento_de_magia':
            inputCancelamento($caster, $battle);
            break;
        
        case 'ataque_magico':
            inputAtaqueMagico($caster, $battle);
            break;

        case 'lanca_infalivel_de_talude':
            inputLancaInfalivel($caster, $battle);
            break;

        case 'brilho_explosivo':
            inputBrilhoExplosivo($caster, $battle);
            break;


        default:
            echo 'Essa magia ainda não foi implementada';
            break;
    }
}

function inputBolaDeFogo($caster, &$b) {
    $validTargets = getValidTargets($caster, $b, 'enemies'); // Função auxiliar para pegar alvos

    echo 'Alvo: <select name="magic_target">';
    foreach ($validTargets as $tgt) {
        echo '<option value="' . htmlspecialchars($tgt) . '">' . htmlspecialchars($tgt) . '</option>';
    }
    echo '</select><br>';

    echo 'Custo em PMs (1-10): <input type="number" name="magic_pm_cost" value="1" min="1" max="10" required><br>';
}


function inputCuraMagica($caster, &$b) {
    $validTargets = getValidTargets($caster, $b, 'allies');

    echo 'Alvo: <select name="magic_target">';
    foreach ($validTargets as $tgt) {
        echo '<option value="' . htmlspecialchars($tgt) . '">' . htmlspecialchars($tgt) . '</option>';
    }
    echo '</select><br>';
    
    echo 'Tipo de Cura: <select name="magic_heal_type">';
    echo '<option value="heal_pv">Curar PVs (2 PMs)</option>';
    echo '<option value="cure_status">Curar status (4 PMs)</option>';
    echo '</select><br>';
}



// Ataque mágico
function inputAtaqueMagico($caster, &$b) {
    $validTargets = getValidTargets($caster, $b, 'enemies');
    echo 'Custo em PMs (1-5): <input type="number" id="magic_pm_cost" name="magic_pm_cost" value="1" min="1" max="5" required><br>';
    echo 'Tipo de Ataque: <select id="magic_attack_type" name="magic_attack_type"> <option value="F">Corpo a Corpo</option><option value="PdF">A Distância</option></select><br>';
    echo 'Quantidade de Alvos: <input type="number" id="magic_num_targets" name="magic_num_targets" value="1" min="1" max="1" required><br>';
    echo '<div id="magic_targets_container"></div>';
    ?>
    <script>
    (function() {
        const pmCostInput = document.getElementById('magic_pm_cost');
        const numTargetsInput = document.getElementById('magic_num_targets');
        const attackTypeSelect = document.getElementById('magic_attack_type');
        const targetsContainer = document.getElementById('magic_targets_container');
        const validTargets = <?php echo json_encode($validTargets); ?>;
        function generateTargetInputs() {
            targetsContainer.innerHTML = '';
            const numTargets = parseInt(numTargetsInput.value, 10);
            const isPdfAttack = attackTypeSelect.value === 'PdF';
            for (let i = 0; i < numTargets; i++) {
                const fieldset = document.createElement('fieldset');
                fieldset.style.marginTop = '10px';
            
                const legend = document.createElement('legend');
                legend.textContent = `Alvo ${i + 1}`;
                fieldset.appendChild(legend);

                let targetSelectHTML = `<label>Selecionar Alvo: <select name="magic_targets[${i}][name]" required>`;
                validTargets.forEach(target => {
                    targetSelectHTML += `<option value="${target}">${target}</option>`;
                });
                targetSelectHTML += '</select></label><br>';
                
                let reactionSelectHTML = `<label>Reação do Alvo: <select name="magic_targets[${i}][reaction]" required>`;
                reactionSelectHTML += `<option value="defender">Defender</option>`;
                if (isPdfAttack) {
                    reactionSelectHTML += `<option value="defender_esquiva">Esquivar</option>`;
                }
                reactionSelectHTML += `<option value="indefeso">Indefeso</option>`;
                reactionSelectHTML += `</select></label><br>`;

                const reactionRollFDHTML = `<label>Dado de Reação: <input type="number" name="magic_targets[${i}][rollFD]" required></label><br>`;
                const reactionRollFAHTML = `<label>Dado de Ataque: <input type="number" name="magic_targets[${i}][rollFA]" required></label>`;
                fieldset.innerHTML += targetSelectHTML + reactionSelectHTML + reactionRollFDHTML + reactionRollFAHTML;
                targetsContainer.appendChild(fieldset);
            }
        }
        function updateMaxTargets() {
            const currentPmCost = parseInt(pmCostInput.value, 10);
            numTargetsInput.max = currentPmCost;
            if (parseInt(numTargetsInput.value, 10) > currentPmCost) {
                numTargetsInput.value = currentPmCost;
            }
            generateTargetInputs();
        }
        pmCostInput.addEventListener('change', updateMaxTargets);
        numTargetsInput.addEventListener('change', generateTargetInputs);
        attackTypeSelect.addEventListener('change', generateTargetInputs);
        updateMaxTargets();
    })();
    </script>
    <?php
}



// A lança Infalível de Talude
function inputLancaInfalivel($caster, &$b) {
    $validTargets = getValidTargets($caster, $b, 'enemies');
    echo 'Custo em PMs (1-5): <input type="number" id="magic_pm_cost" name="magic_pm_cost" value="1" min="1" max="5" required><br>';
    echo 'Quantidade de Alvos: <input type="number" id="magic_num_targets" name="magic_num_targets" value="1" min="1" max="1" required><br>'; 
    echo '<div id="magic_targets_container"></div>';
    ?>
    <script>
    (function() {
        const pmCostInput = document.getElementById('magic_pm_cost');
        const numTargetsInput = document.getElementById('magic_num_targets');
        const targetsContainer = document.getElementById('magic_targets_container');
        const validTargets = <?php echo json_encode($validTargets); ?>;
        function generateTargetInputs() {
            targetsContainer.innerHTML = '';
            const numTargets = parseInt(numTargetsInput.value, 10);
            for (let i = 0; i < numTargets; i++) {
                const fieldset = document.createElement('fieldset');
                fieldset.style.marginTop = '10px';
            
                const legend = document.createElement('legend');
                legend.textContent = `Alvo ${i + 1}`;
                fieldset.appendChild(legend);

                let targetSelectHTML = `<label>Selecionar Alvo: <select name="magic_targets[${i}][name]" required>`;
                validTargets.forEach(target => {
                    targetSelectHTML += `<option value="${target}">${target}</option>`;
                })
                const qtdAtkHTML = `<label>Quantidade de Lanças atacando esse: <input type="number" name="magic_targets[${i}][qtdAtk]" min="1" max="${pmCostInput.value}"required></label><br>`;;
                targetSelectHTML += '</select></label><br>';
                fieldset.innerHTML += targetSelectHTML + qtdAtkHTML;
                targetsContainer.appendChild(fieldset);
            }
        }
        function updateMaxTargets() {
            const currentPmCost = parseInt(pmCostInput.value, 10);
            numTargetsInput.max = currentPmCost;
            if (parseInt(numTargetsInput.value, 10) > currentPmCost) {
                numTargetsInput.value = currentPmCost;
            }
            generateTargetInputs();
        }
        pmCostInput.addEventListener('change', updateMaxTargets);
        numTargetsInput.addEventListener('change', generateTargetInputs);
        updateMaxTargets();
    })();
    </script>
    <?php
}



function inputBrilhoExplosivo($caster, &$b){
    $validTargets = getValidTargets($caster, $b);
    echo '<label>Selecionar Alvo: <select name="target" required>';
    foreach ($validTargets as $target){
        echo '<option value="'.$target.'">'.$target.'</option>';
    }
    echo '</select></label><br>';
    for ($i = 1; $i < 11; $i++){
        echo '<label>'.$i.'° dado: <input type="number" name="dado'.$i.'" required></label><br>';
    }
    echo '<label>Dado de defesa: <input type="number" name="dadoFD" required></label>';
}








function inputCancelamento($caster, &$b){
    echo '<p>Selecione a magia a ser cancelada:</p>';
    echo '<select name="magic_to_cancel">';
    
    echo '<option value="invisibilidade_no_inimigo_X">Invisibilidade (Inimigo X)</option>';
    echo '</select>';
}



function getValidTargets($caster, &$b, $type = 'enemies') {
    $targets = [];
    $caster_notes = $b['notes'][$caster] ?? [];
    $caster_is_incorp = !empty($caster_notes['incorp_active']);

    if ($type === 'allies') {
        $targets[] = $caster;
    }
    foreach ($b['order'] as $p) {
        if ($p === $caster && $type === 'enemies') continue;
        
        $target_notes = $b['notes'][$p] ?? [];
        $target_is_incorp = !empty($target_notes['incorp_active']);

        if ($caster_is_incorp !== $target_is_incorp) {
            continue;
        }
        
        if ($type === 'enemies' && $p !== $caster) {
            $targets[] = $p;
        } elseif ($type === 'allies' && $p !== $caster) {
             $targets[] = $p;
        }
    }
    return array_unique($targets);
}

?>