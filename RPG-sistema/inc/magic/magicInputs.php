<?php
include_once './inc/battleFuncs.php';

function renderMagicInputs($slug, $caster){
    switch ($slug) {
        case 'bola_de_fogo':
            inputBolaDeFogo($caster);
            break;

        case 'cancelamento_de_magia':
            inputCancelamento();
            break;

        case 'ataque_magico':
            inputAtaqueMagico($caster);
            break;

        case 'lanca_infalivel_de_talude':
            inputLancaInfalivel($caster);
            break;

        case 'brilho_explosivo':
            inputBrilhoExplosivo($caster);
            break;

        case 'morte_estelar':
            inputMorteEstelar($caster);
            break;

        case 'enxame_de_trovoes':
            inputEnxameDeTrovoes($caster);
            break;

        case 'nulificacao_total_de_talude':
            inputNulificacaoTotalDeTalude($caster);
            break;

        case 'bola_de_fogo_instavel':
            inputBolaDeFogoInstavel($caster);
            break;

        case 'bola_de_lama':
            inputBolaDeLama($caster);
            break;

        case 'bomba_de_luz':
            inputBombaDeLuz($caster);
            break;

        case 'bomba_de_terra':
            inputBombaDeTerra($caster);
            break;

        case 'solisanguis':
            inputSolisanguis($caster);
            break;

        case 'solisanguis_ruptura':
            inputSolisanguisRuptura($caster);
            break;

        case 'solisanguis_evisceratio':
            inputSolisanguisEvisceratio($caster);
            break;

        case 'sortilegium':
            inputSortilegium($caster);
            break;

        case 'sancti_sanguis':
            inputSanctiSanguis($caster);
            break;

        case 'luxcruentha':
            inputLuxcruentha();
            break;

        case 'artifanguis':
            inputArtifanguis();
            break;

        case 'excruentio':
            inputExcruentio($caster);
            break;

        case 'speculusanguis':
            inputSpeculusanguis($caster);
            break;

        case 'vis_ex_vulnere':
            inputVisExVulnere();
            break;

        case 'solcruoris':
            inputSolcruoris();
            break;

        case 'spectraematum':
            inputSpectraematum($caster);
            break;

        case 'aeternum_tribuo':
            inputAeternumTribuo($caster);
            break;

        case 'inhaerescorpus':
            inputInhaerescorpus($caster);
            break;

        case 'hemeopsia':
            inputHemeopsia();
            break;

        case 'cegueira':
            inputCegueira($caster);
            break;    

        case 'amor_incontestavel':
            inputAmorIncontestavel($caster);
            break;

        case 'ataque_vorpal':
            inputAtaqueVorpal($caster);
            break; 

        case 'cura_para_o_mal':
            inputCuraParaOMal($caster);
            break;

        case 'desmaio':
            inputDesmaio($caster);
            break;

        case 'destrancar':
            inputDestrancar();
            break;

        case 'escapatoria_de_valkaria':
            inputAEscapatoriaDeValkaria();
            break;

        case 'fada_servil':
            inputFadaServil();
            break;

        case 'farejar_tesouro':
            inputFarejarTesouro();
            break;

        case 'flor_perene_de_milady_a':
            inputFlorPereneDeMiladyA($caster);
            break;

        case 'furtividade_de_hyninn':
            inputFurtividadeDeHyninn($caster);
            break;

        case 'luz':
            inputLuz();
            break;

        case 'protecao_magica_superior':
            inputProtecaoMagicaSuperior($caster);
            break;

        case 'protecao_magica':
            inputProtecaoMagica($caster);
            break;

        case 'recuperacao_natural':
            inputRecuperacaoNatural($caster);
            break;

        case 'reflexos':
            inputReflexos();
            break;

        case 'retribuicao_de_wynna':
            inputRetribuicaoDeWynna();
            break;

        case 'sacrificio_de_marah':
            inputSacrificioDeMarah($caster);
            break;

        case 'sentidos_especiais_magia':
            inputSentidosEspeciais();
            break;

        case 'teleportacao_aprimorada':
            inputTeleportacaoAprimorada();
            break;

        case 'teleportacao':
            inputTeleportacao();
            break;

        case 'teleportacao_planar':
            inputTeleportacaoPlanar();
            break;

        case 'transporte':
            inputTransporte();
            break;
            
        case 'deteccao_de_magia':
            inputDeteccaoDeMagia();
            break;

        case 'raio_desintegrador':
            inputRaioDesintegrador($caster);
            break;

        case 'excidium_stellae':
            inputExcidiumStellae($caster);
            break;
            
        default:
            echo 'Essa magia ainda não foi implementada';
            break;
    }
}

function inputBolaDeFogo($caster){
    $validTargets = getValidTargets($caster);
    $selectTargets = selectTarget($caster, $validTargets);
    echo 'Custo em PMs (1-10): <input type="number" id="magic_pm_cost" name="magic_pm_cost" value="1" min="1" max="10" step="1" required><br>';
    echo 'Quantidade de inimigos na área: <input type="number" id="magic_num_targets" name="magic_num_targets" value="1" min="1" required><br>';
    echo 'Dado de ataque: <input type="number" name="dadoFA" value="1" min="1" max="6" required><br>';
    echo '<h4>Alvos na Área</h4><div id="magic_targets_container"></div>';
    echo '<input type="hidden" class="magic-properties" data-attack-type="PdF">';
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
                let optionsHTML = '';
                optionsHTML = <?php echo json_encode($selectTargets) ?>;
                for (let i = 0; i < numTargets; i++) {
                    const fieldset = document.createElement('fieldset');
                    fieldset.className = 'magic-input-group';
                    fieldset.style.marginTop = '10px';
                    const legend = document.createElement('legend');
                    legend.textContent = `Alvo ${i + 1}`;
                    fieldset.appendChild(legend);
                    let targetSelectHTML = `<label>Selecionar Alvo: <select class="magic-target-select" name="magic_targets[${i}][name]" required>${optionsHTML}</select></label>`;
                    let reactionSelectHTML = `<br><label>Reação do Alvo: <select name="magic_targets[${i}][reaction]" class="magic-reaction-select" required>`;
                    reactionSelectHTML += `<option value="defender">Defender</option>`;
                    reactionSelectHTML += `<option value="defender_esquiva">Esquivar</option>`;
                    reactionSelectHTML += `<option value="defender_esquiva_deflexao" >Deflexão (2PM)</option>`;
                    reactionSelectHTML += `<option value="indefeso">Indefeso</option>`;
                    reactionSelectHTML += `</select></label><br>`;
                    targetFDHTML = `<br><label>Dado de defesa: <input type="number" name="magic_targets[${i}][dFD]" min="1" max="6" required></label>`;
                    fieldset.innerHTML += targetSelectHTML + targetFDHTML + reactionSelectHTML;
                    targetsContainer.appendChild(fieldset);
                }
            }
            function updateAllInputs() {
                generateTargetInputs();
            }
            numTargetsInput.addEventListener('input', updateAllInputs);
            updateAllInputs();
        })();
    </script>
<?php
}

function inputAtaqueMagico($caster){
    $validTargets = getValidTargets($caster);
    $selectTargets = selectTarget($caster, $validTargets);
    echo 'Custo em PMs (1-5): <input type="number" id="magic_pm_cost" name="magic_pm_cost" value="1" min="1" max="5" required><br>';
    echo 'Tipo de Ataque: <select class="magic-attack-type" id="magic_attack_type" name="magic_attack_type"> <option value="F">Corpo a Corpo</option><option value="PdF">A Distância</option></select><br>';
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
                optionsHTML = <?php echo json_encode($selectTargets) ?>;
                for (let i = 0; i < numTargets; i++) {
                    const fieldset = document.createElement('fieldset');
                    fieldset.className = 'magic-input-group';
                    fieldset.style.marginTop = '10px';

                    const legend = document.createElement('legend');
                    legend.textContent = `Alvo ${i + 1}`;
                    fieldset.appendChild(legend);
                    let targetSelectHTML = `<label>Selecionar Alvo: <select class="magic-target-select" name="magic_targets[${i}][name]" required>${optionsHTML}</select></label>`;

                    let reactionSelectHTML = `<br><label>Reação do Alvo: <select name="magic_targets[${i}][reaction]" class="magic-reaction-select" required>`;
                    reactionSelectHTML += `<option value="defender">Defender</option>`;
                    if (isPdfAttack) {
                        reactionSelectHTML += `<option value="defender_esquiva">Esquivar</option>`;
                        reactionSelectHTML += `<option value="defender_esquiva_deflexao" >Deflexão (2PM)</option>`;
                    }
                    reactionSelectHTML += `<option value="indefeso">Indefeso</option>`;
                    reactionSelectHTML += `</select></label><br>`;

                    const reactionRollFDHTML = `<label>Dado de Reação: <input type="number" name="magic_targets[${i}][rollFD]" min="1" max="6" required></label><br>`;
                    const reactionRollFAHTML = `<label>Dado de Ataque: <input type="number" name="magic_targets[${i}][rollFA]" min="1" max="6" required></label>`;
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

function inputLancaInfalivel($caster){
    $validTargets = getValidTargets($caster);
    $selectTargets = selectTarget($caster, $validTargets);
    echo 'Custo em PMs (1-5): <input type="number" id="magic_pm_cost" name="magic_pm_cost" value="1" min="1" max="5" required><br>';
    echo 'Quantidade de Alvos: <input type="number" id="magic_num_targets" name="magic_num_targets" value="1" min="1" max="1" required><br>';
    echo '<div id="magic_targets_container"></div>';
    echo '<input type="hidden" class="magic-properties" data-attack-type="PdF">';
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
                optionsHTML = <?php echo json_encode($selectTargets) ?>;
                for (let i = 0; i < numTargets; i++) {
                    const fieldset = document.createElement('fieldset');
                    fieldset.className = 'magic-input-group';
                    fieldset.style.marginTop = '10px';

                    const legend = document.createElement('legend');
                    legend.textContent = `Alvo ${i + 1}`;
                    fieldset.appendChild(legend);

                    let targetSelectHTML = `<label>Selecionar Alvo: <select class="magic-target-select" name="magic_targets[${i}][name]" required>${optionsHTML}`;
                    const qtdAtkHTML = `<br><label>Quantidade de Lanças atacando esse alvo: <input type="number" name="magic_targets[${i}][qtdAtk]" min="1" max="${pmCostInput.value}"required></label><br>`;;
                    targetSelectHTML += '</select></label>';
                    let reactionSelectHTML = `<label>Reação do Alvo: <select name="magic_targets[${i}][reaction]" class="magic-reaction-select" required>`;
                    reactionSelectHTML += `<option value="defender_esquiva">Esquivar</option>`;
                    reactionSelectHTML += `<option value="defender_esquiva_deflexao" >Deflexão (2PM)</option>`;
                    reactionSelectHTML += `<option value="indefeso">Indefeso</option>`;
                    reactionSelectHTML += `</select></label><br>`;
                    fieldset.innerHTML += targetSelectHTML + qtdAtkHTML + reactionSelectHTML;
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

function inputBrilhoExplosivo($caster){
    $validTargets = getValidTargets($caster);
    echo '<div class="magic-input-group">'; 
    echo '<label>Selecionar Alvo: <select id="magic_target" name="magic_target" class="magic-target-select" required>';
    echo selectTarget($caster, $validTargets);
    echo '</select></label><br>';
    for ($i = 1; $i < 11; $i++) {
        echo '<label>' . $i . '° dado: <input type="number" name="dado' . $i . '" min="1" max="6" required></label><br>';
    }
    echo 'Reação: <select id="def" name="magic_def" class="magic-reaction-select">'
        . '<option value="defender">Defender</option>'
        . '<option value="defender_esquiva">Esquivar</option>'
        . '<option value="defender_esquiva_deflexao" >Deflexão (2 PM)</option>'
        . '<option value="indefeso">Indefeso</option>'
        . '</select>';
    echo '<br><label>Dado de defesa: <input type="number" name="dadoFD" min="1" max="6" required></label>';
    echo '<input type="hidden" class="magic-properties" data-attack-type="PdF">';
    echo '</div>';
}

function inputBolaDeFogoInstavel($caster){
    $validTargets = getValidTargets($caster);
    $selectTargets = selectTarget($caster, $validTargets);
    echo 'Custo em PMs (2-10): <input type="number" id="magic_pm_cost_instavel" name="magic_pm_cost" value="2" min="2" max="10" step="1" required><br>';
    echo 'Quantidade de inimigos na área: <input type="number" id="magic_num_targets_instavel" name="magic_num_targets" value="1" min="1" required><br>';
    echo '<hr><h4>Dados de Ataque</h4><div id="magic_dice_container"></div><hr>';
    echo '<h4>Alvos na Área</h4><div id="magic_targets_container"></div>';
    echo '<input type="hidden" class="magic-properties" data-attack-type="PdF">';
?>
    <script>
        (function() {
            const pmCostInput = document.getElementById('magic_pm_cost_instavel');
            const numTargetsInput = document.getElementById('magic_num_targets_instavel');
            const diceContainer = document.getElementById('magic_dice_container');
            const targetsContainer = document.getElementById('magic_targets_container');
            const validTargets = <?php echo json_encode($validTargets); ?>;
            function generateDiceInputs() {
                diceContainer.innerHTML = '';
                const currentPmCost = parseInt(pmCostInput.value, 10);
                const numDice = 1 + Math.floor(currentPmCost / 2);
                for (let i = 0; i < numDice; i++) {
                    let dInputHTML = `<label>${i + 1} dado de ataque: <input type="number" name="dados[${i}]" min="1" max="6" required></label><br>`;
                    diceContainer.innerHTML += dInputHTML;
                }
            }
            optionsHTML = <?php echo json_encode($selectTargets) ?>;
            function generateTargetInputs() {
                targetsContainer.innerHTML = '';
                const numTargets = parseInt(numTargetsInput.value, 10);
                for (let i = 0; i < numTargets; i++) {
                    const fieldset = document.createElement('fieldset');
                    fieldset.className = 'magic-input-group';
                    fieldset.style.marginTop = '10px';
                    const legend = document.createElement('legend');
                    legend.textContent = `Alvo ${i + 1}`;
                    fieldset.appendChild(legend);
                    let targetSelectHTML = `<label>Selecionar Alvo: <select class="magic-target-select" name="magic_targets[${i}][name]" required>${optionsHTML}</select></label>`;
                    let reactionSelectHTML = `<br><label>Reação do Alvo: <select name="magic_targets[${i}][reaction]" class="magic-reaction-select" required>`;
                    reactionSelectHTML += `<option value="defender">Defender</option>`;
                    reactionSelectHTML += `<option value="defender_esquiva">Esquivar</option>`;
                    reactionSelectHTML += `<option value="defender_esquiva_deflexao" >Deflexão (2PM)</option>`;
                    reactionSelectHTML += `<option value="indefeso">Indefeso</option>`;
                    reactionSelectHTML += `</select></label><br>`;
                    targetFDHTML = `<br><label>Dado de defesa: <input type="number" name="magic_targets[${i}][dFD]" min="1" max="6" required></label>`;
                    fieldset.innerHTML += targetSelectHTML + targetFDHTML + reactionSelectHTML;
                    targetsContainer.appendChild(fieldset);
                }
            }
            function updateAllInputs() {
                generateDiceInputs();
                generateTargetInputs();
            }
            pmCostInput.addEventListener('input', updateAllInputs);
            numTargetsInput.addEventListener('input', updateAllInputs);
            updateAllInputs();
        })();
    </script>
<?php
}

function inputExcidiumStellae($caster){
    $validTargets = getValidTargets($caster);
    $selectTargets = selectTarget($caster, $validTargets);
    echo 'Quantidade de inimigos na área: <input type="number" id="magic_num_targets_instavel" name="magic_num_targets" value="1" min="1" required><br>';
    echo '<label>1° dado de ataque: <input type="number" name="dados[0]" min="1" max="6" required></label><br>';
    echo '<label>2° dado de ataque: <input type="number" name="dados[1]" min="1" max="6" required></label><br>';
    echo '<label>3° dado de ataque: <input type="number" name="dados[2]" min="1" max="6" required></label><br>';
    echo '<label>4° dado de ataque: <input type="number" name="dados[3]" min="1" max="6" required></label><br>';
    echo '<label>5° dado de ataque: <input type="number" name="dados[4]" min="1" max="6" required></label><br>';
    echo '<h4>Alvos na Área</h4><div id="magic_targets_container"></div>';
    echo '<input type="hidden" class="magic-properties" data-attack-type="PdF">';
?>
    <script>
        (function() {
            const numTargetsInput = document.getElementById('magic_num_targets_instavel');
            const targetsContainer = document.getElementById('magic_targets_container');
            const validTargets = <?php echo json_encode($validTargets); ?>;
            optionsHTML = <?php echo json_encode($selectTargets) ?>;
            function generateTargetInputs() {
                targetsContainer.innerHTML = '';
                const numTargets = parseInt(numTargetsInput.value, 10);
                for (let i = 0; i < numTargets; i++) {
                    const fieldset = document.createElement('fieldset');
                    fieldset.className = 'magic-input-group';
                    fieldset.style.marginTop = '10px';
                    const legend = document.createElement('legend');
                    legend.textContent = `Alvo ${i + 1}`;
                    fieldset.appendChild(legend);
                    let targetSelectHTML = `<label>Selecionar Alvo: <select class="magic-target-select" name="magic_targets[${i}][name]" required>${optionsHTML}</select></label>`;
                    let reactionSelectHTML = `<br><label>Reação do Alvo: <select name="magic_targets[${i}][reaction]" class="magic-reaction-select" required>`;
                    reactionSelectHTML += `<option value="defender">Defender</option>`;
                    reactionSelectHTML += `<option value="defender_esquiva">Esquivar</option>`;
                    reactionSelectHTML += `<option value="defender_esquiva_deflexao" >Deflexão (2PM)</option>`;
                    reactionSelectHTML += `<option value="indefeso">Indefeso</option>`;
                    reactionSelectHTML += `</select></label><br>`;
                    targetFDHTML = `<br><label>Dado de defesa: <input type="number" name="magic_targets[${i}][dFD]" min="1" max="6" required></label>`;
                    testRHTML = `<label>Teste de Resistência: <input type="number" name="magic_targets[${i}][testR]" min="1" max="6"></label>`;
                    fieldset.innerHTML += targetSelectHTML + targetFDHTML + reactionSelectHTML + testRHTML;
                    targetsContainer.appendChild(fieldset);
                }
            }
            function updateAllInputs() {
                generateTargetInputs();
            }
            numTargetsInput.addEventListener('input', updateAllInputs);
            updateAllInputs();
        })();
    </script>
<?php
}

function inputBombaDeLuz($caster){
    $validTargets = getValidTargets($caster);
    $selectTargets = selectTarget($caster, $validTargets);
    echo 'Custo em PMs (1-5): <input type="number" id="magic_pm_cost" name="magic_pm_cost" value="1" min="1" max="5" step="1" required><br>';
    echo 'Quantidade de inimigos na área: <input type="number" id="magic_num_targets" name="magic_num_targets" value="1" min="1" required><br>';
    echo '<h4>Alvos na Área</h4><div id="magic_targets_container"></div>';
    echo '<input type="hidden" class="magic-properties" data-attack-type="PdF">';
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
                optionsHTML = <?php echo json_encode($selectTargets) ?>;
                for (let i = 0; i < numTargets; i++) {
                    const fieldset = document.createElement('fieldset');
                    fieldset.className = 'magic-input-group';
                    fieldset.style.marginTop = '10px';
                    const legend = document.createElement('legend');
                    legend.textContent = `Alvo ${i + 1}`;
                    fieldset.appendChild(legend);
                    let targetSelectHTML = `<label>Selecionar Alvo: <select class="magic-target-select" name="magic_targets[${i}][name]" required>${optionsHTML}</select></label>`;
                    let reactionSelectHTML = `<br><label>Reação do Alvo: <select name="magic_targets[${i}][reaction]" class="magic-reaction-select" required>`;
                    reactionSelectHTML += `<option value="defender_sem_armadura">Defender</option>`;
                    reactionSelectHTML += `<option value="defender_esquiva">Esquivar</option>`;
                    reactionSelectHTML += `<option value="defender_esquiva_deflexao" >Deflexão (2PM)</option>`;
                    reactionSelectHTML += `<option value="indefeso">Indefeso</option>`;
                    reactionSelectHTML += `</select></label><br>`;
                    targetFDHTML = `<br><label>Dado de defesa: <input type="number" name="magic_targets[${i}][dFD]" min="1" max="6" required></label>`;
                    fieldset.innerHTML += targetSelectHTML + targetFDHTML + reactionSelectHTML;
                    targetsContainer.appendChild(fieldset);
                }
            }
            function updateAllInputs() {
                generateTargetInputs();
            }
            numTargetsInput.addEventListener('input', updateAllInputs);
            updateAllInputs();
        })();
    </script>
<?php
}

function inputSacrificioDeMarah($caster){
    $validTargets = getValidTargets($caster);
    $selectTargets = selectTarget($caster, $validTargets);
    echo 'Quantidade de aliados na área: <input type="number" id="magic_num_targets" name="magic_num_targets" value="1" min="1" required><br>';
    echo '<h4>Alvos na Área</h4><div id="magic_targets_container"></div>';
    echo '<input type="hidden" class="magic-properties" data-attack-type="PdF">';
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
                optionsHTML = <?php echo json_encode($selectTargets) ?>;
                for (let i = 0; i < numTargets; i++) {
                    const fieldset = document.createElement('fieldset');
                    fieldset.className = 'magic-input-group';
                    fieldset.style.marginTop = '10px';
                    const legend = document.createElement('legend');
                    legend.textContent = `Alvo ${i + 1}`;
                    fieldset.appendChild(legend);
                    let targetSelectHTML = `<label>Selecionar Alvo: <select class="magic-target-select" name="magic_targets[${i}][name]" required>${optionsHTML}</select></label>`;
                    fieldset.innerHTML += targetSelectHTML;
                    targetsContainer.appendChild(fieldset);
                }
            }
            function updateAllInputs() {
                generateTargetInputs();
            }
            numTargetsInput.addEventListener('input', updateAllInputs);
            updateAllInputs();
        })();
    </script>
<?php 
}

function inputRaioDesintegrador($caster){
    $allTargets = getValidTargets($caster, 'enemies');
    $casterH = getPlayerStat($caster, 'H');
    $validTargets = [];
    foreach ($allTargets as $target) {
        $isImmune = getPlayerStat($target, 'R') > $casterH;
        if (isCorporeo($target) && !$isImmune) {
            $validTargets[] = $target;
        }
    }
    echo '<label>Selecionar Alvo: <select id="raio_desintegrador_target" name="magic_target" required>';
    echo '<option value="objeto">Objeto</option>';
    echo selectTarget($caster, $validTargets);
    echo '</select></label><br>';
    echo '<label>Custo em PMs: <input type="number" name="magic_pm_cost" min="1" step="5" value="5" required></label><br>';
    echo '<div id="raio_desintegrador_r_test_container" style="display:none; margin-top: 10px;">';
    echo '<label>Teste de Resistência: <input type="number" name="testR" min="1" max="6"></label>';
    echo '</div>';
?>
    <script>
        (function() {
            const targetSelect = document.getElementById('raio_desintegrador_target');
            if (!targetSelect) return;
            const rTestContainer = document.getElementById('raio_desintegrador_r_test_container');
            const rTestInput = rTestContainer ? rTestContainer.querySelector('input[name="testR"]') : null;
            if (!rTestContainer || !rTestInput) return;
            function toggleRTest() {
                if (targetSelect.value === 'objeto') {
                    rTestContainer.style.display = 'none';
                    rTestInput.required = false;
                } else {
                    rTestContainer.style.display = 'block';
                    rTestInput.required = true;
                }
            }
            targetSelect.addEventListener('change', toggleRTest);
            toggleRTest();
        })();
    </script>
<?php
}

function inputMorteEstelar($caster){
    $validTargets = getValidTargets($caster);
    echo '<div class="magic-input-group">'; 
    echo '<label>Selecionar Alvo: <select id="morte_estelar_select" name="magic_target" class="magic-target-select" required>';
    echo '<option value="" selected>Prossiga com Cuidado</option>';
    echo selectTarget($caster, $validTargets);
    echo '</select></label><br>';
    echo '<div id="morte_estelar_confirm_container" style="margin-top:8px; display:none;"></div>';
?>
    <script>
        (function() {
            const select = document.getElementById('morte_estelar_select');
            const container = document.getElementById('morte_estelar_confirm_container');
            const magicForm = document.getElementById('magicForm');
            select.addEventListener('change', function() {
                const alvo = select.value;
                if (!alvo) {
                    container.style.display = 'none';
                    container.innerHTML = '';
                    return;
                }
                container.style.display = 'block';
                container.innerHTML = `
                <div style="margin-bottom:8px;color:darkred;font-weight:600;">
                    Confirmar: -5 PMs permanentemente & Aniquilação de <strong>${alvo}</strong>?
                </div>
                <button type="submit" form="magicForm" style="margin-right:8px;">Confirmar</button>
                <button type="button" id="morte_estelar_cancel">Cancelar</button>`;
                document.getElementById('morte_estelar_cancel').addEventListener('click', function() {
                    select.value = '';
                    container.style.display = 'none';
                    container.innerHTML = '';
                });
            });
        })();
    </script>
<?php
}

function inputNulificacaoTotalDeTalude($caster){
    $validTargets = getValidTargets($caster);
    echo '<label>Selecionar Alvo: <select id="nulificacao_select" name="magic_target" class="magic-target-select" required>';
    echo '<option value="" selected>Prossiga com Cuidado</option>';
    echo selectTarget($caster, $validTargets);
    echo '</select></label><br>';
    echo '<div id="nulificacao_confirm_container" style="margin-top:8px; display:none;"></div>';
?>
    <script>
        (function() {
            const select = document.getElementById('nulificacao_select');
            const container = document.getElementById('nulificacao_confirm_container');
            const magicForm = document.getElementById('magicForm');
            select.addEventListener('change', function() {
                const alvo = select.value;
                if (!alvo) {
                    container.style.display = 'none';
                    container.innerHTML = '';
                    return;
                }
                container.style.display = 'block';
                container.innerHTML = `
                <div style="margin-bottom:8px;color:darkred;font-weight:600;">
                    Confirmar: -50 PMs para apagar <strong>${alvo}</strong> da existência?
                </div>
                <label>Teste de resistência: <input type="number" name="RTest" min="1" max="6" required></label><br>
                <button type="submit" form="magicForm" style="margin-right:8px;">Confirmar</button>
                <button type="button" id="nulificacao_cancel">Cancelar</button>`;
                document.getElementById('nulificacao_cancel').addEventListener('click', function() {
                    select.value = '';
                    container.style.display = 'none';
                    container.innerHTML = '';
                });
            });
        })();
    </script>
<?php
}

function inputBolaDeLama($caster){
    $validTargets = getValidTargets($caster);
    echo '<div class="magic-input-group">';
    echo '<label>Selecionar Alvo: <select id="magic_target" name="magic_target" class="magic-target-select" required>';
    echo selectTarget($caster, $validTargets);
    echo '</select></label><br>';
    for ($i = 0; $i < getPlayerStat($caster, 'H'); $i++) {
        echo '<label>' . ($i + 1) . '° dado de ataque: <input type="number" name="dadosFA[' . $i . ']" min="1" max="6" required></label><br>';
    }
    echo 'Reação: <select id="def" name="magic_def" class="magic-reaction-select">'
        . '<option value="defender_sem_armadura">Defender</option>'
        . '<option value="defender_esquiva">Esquivar</option>'
        . '<option value="defender_esquiva_deflexao" >Deflexão (2 PM)</option>'
        . '<option value="indefeso">Indefeso</option>'
        . '</select><br>';
    echo '<label>Dado de defesa: <input type="number" name="dadoFD" min="1" max="6" required></label>';
    echo '<input type="hidden" class="magic-properties" data-attack-type="PdF">';
    echo '</div>';
}

function inputEnxameDeTrovoes($caster){
    $validTargets = getValidTargets($caster);
    echo '<div class="magic-input-group">';
    echo '<label>Selecionar Alvo: <select name="magic_target" class="magic-target-select" required>';
    echo selectTarget($caster, $validTargets);
    echo '</select></label><br>';
    echo '<br><label>1° dado de ataque: <input type="number" name="dadoFA1" min="1" max="6" required></label>';
    echo '<br><label>2° dado de ataque: <input type="number" name="dadoFA2" min="1" max="6" required></label><br>';
    echo 'Reação: <select id="def" name="magic_def" class="magic-reaction-select">'
        . '<option value="defender_sem_armadura">Defender</option>'
        . '<option value="defender_esquiva">Esquivar</option>'
        . '<option value="defender_esquiva_deflexao" >Deflexão (2 PM)</option>'
        . '<option value="indefeso">Indefeso</option>'
        . '</select>';
    echo '<br><label>Dado de defesa: <input type="number" name="dadoFD" min="1" max="6" required></label>';
    echo '<input type="hidden" class="magic-properties" data-attack-type="PdF">';
    echo '</div>';
}

function inputBombaDeTerra($caster){
    $validTargets = getValidTargets($caster);
    echo '<div class="magic-input-group">';
    echo '<label>Selecionar Alvo: <select id="magic_target" name="magic_target" class="magic-target-select" required>';
    echo selectTarget($caster, $validTargets);
    echo '</select></label><br>';
    echo 'Reação: <select id="def" name="magic_def" class="magic-reaction-select">'
        . '<option value="defender">Defender</option>'
        . '<option value="defender_esquiva">Esquivar</option>'
        . '<option value="indefeso">Indefeso</option>'
        . '<option value="defender_esquiva_deflexao" >Deflexão (2 PM)</option>'
        . '</select><br>';
    echo '<label>Dado de defesa: <input type="number" name="dadoFD" min="1" max="6" required></label>';
    echo '<input type="hidden" class="magic-properties" data-attack-type="PdF">';
    echo '</div>';
}

function inputSolisanguis($caster){
    $validTargets = getValidTargets($caster);
    echo '<label>Selecionar Alvo: <select id="magic_target" name="magic_target" class="magic-target-select" required>';
    echo selectTarget($caster, $validTargets);
    echo '</select></label>';
    echo '<br><label>Dado do custo: <input type="number" name="dadoCusto" min="1" max="6" required></label>';
    echo '<br><label>1° dado de ataque: <input type="number" name="dadoFA1" min="1" max="6" required></label>';
    echo '<br><label>2° dado de ataque: <input type="number" name="dadoFA2" min="1" max="6" required></label>';
}

function inputSolisanguisRuptura($caster){
    $validTargets = getValidTargets($caster);
    echo '<label>Selecionar Alvo: <select id="magic_target" name="magic_target" class="magic-target-select" required>';
    echo selectTarget($caster, $validTargets);
    echo '</select></label>';
    echo '<br><label>Dado do custo: <input type="number" name="dadoCusto" min="1" max="6" required></label>';
    echo '<br><label>1° dado de ataque: <input type="number" name="dadoFA1" min="1" max="6" required></label>';
    echo '<br><label>2° dado de ataque: <input type="number" name="dadoFA2" min="1" max="6" required></label>';
    echo '<br><label>3° dado de ataque: <input type="number" name="dadoFA3" min="1" max="6" required></label>';
    echo '<br><label>4° dado de ataque: <input type="number" name="dadoFA4" min="1" max="6" required></label>';
}


function inputSolisanguisEvisceratio($caster){
    $validTargets = getValidTargets($caster);
    echo '<label>Selecionar Alvo: <select id="magic_target" name="magic_target" class="magic-target-select" required>';
    echo selectTarget($caster, $validTargets);
    echo '</select></label>';
    echo '<br><label>Dado de Custo & Ataque: <input type="number" name="dado" min="1" max="6" required></label>';
}

function inputSortilegium($caster){
    $validTargets = getValidTargets($caster, 'allies');
    echo '<label>Selecionar Alvo: <select id="magic_target" name="magic_target" class="magic-target-select" required>';
    echo selectTarget($caster, $validTargets, true);
    echo '</select></label>';
    echo '<br><label>Dado de Custo: <input type="number" name="dadoCusto" min="1" max="6" required></label>';
    echo '<br><label>1° dado de recuperação: <input type="number" name="dadoPV1" min="1" max="6" required></label>';
    echo '<br><label>2° dado de recuperação: <input type="number" name="dadoPV2" min="1" max="6" required></label>';
}

function inputSanctiSanguis($caster){
    $validTargets = getValidTargets($caster, 'allies');
    echo '<label>Selecionar Alvo: <select id="magic_target" name="magic_target" class="magic-target-select" required>';
    echo selectTarget($caster, $validTargets, true);
    echo '</select></label>';
    echo '<br><label>Trânsferencia: <input type="number" name="qtd" min="1"required></label>';
}

function inputLuxcruentha(){
    echo 'Essa magia não precisa de nenhum input.';
}

function inputArtifanguis(){
    echo '<label>Objeto: <input type="text" name="obj" required></label>';
    echo '<br><label>Custo (0 ou 2): <input type="text" name="cost" min="0" max="2" required></label>';
}

function inputExcruentio($caster){
    $validTargets = getValidTargets($caster);
    echo '<div class="magic-input-group">';
    echo '<label>Selecionar Alvo: <select id="magic_target" name="magic_target" class="magic-target-select" required>';
    echo selectTarget($caster, $validTargets);
    echo '</select></label>';
    echo '<br><label>1° dado de Ataque: <input type="number" name="dadoFA1" min="1" max="6" required></label>';
    echo '<br><label>2° dado de Ataque: <input type="number" name="dadoFA2" min="1" max="6" required></label><br>';
    echo 'Reação: <select id="def" name="magic_def" class="magic-reaction-select">'
        . '<option value="defender">Defender</option>'
        . '<option value="defender_esquiva">Esquivar</option>'
        . '<option value="defender_esquiva_deflexao" >Deflexão (2 PM)</option>'
        . '<option value="indefeso">Indefeso</option>'
        . '</select><br>';
    echo '<label>Dado de defesa: <input type="number" name="dadoFD" min="1" max="6" required></label>';
    echo '<input type="hidden" class="magic-properties" data-attack-type="PdF">';
    echo '</div>';
}

function inputSpeculusanguis($caster){
    $validTargets = getValidTargets($caster);
    echo '<label>Selecionar Alvo: <select id="magic_target" name="magic_target" class="magic-target-select" required>';
    echo selectTarget($caster, $validTargets);
    echo '</select></label>';
}

function inputVisExVulnere(){
    echo 'Essa magia não precisa de inputs.';
}

function inputSolcruoris(){
    echo '<label>Custo: <input type="number" name="cost" min="3" required></label>';
}

function inputSpectraematum($caster){
    $validTargets = getValidTargets($caster);
    echo '<label>Selecionar Alvo: <select id="magic_target" name="magic_target" class="magic-target-select" required>';
    echo selectTarget($caster, $validTargets);
    echo '</select></label>';
    echo '<br><label>Custo do debuff H: <input type="number" name="debuff" min="2" value="0" required></label>';
}

function inputAeternumTribuo($caster){
    $validTargets = getValidTargets($caster);
    echo '<label>Selecionar Alvo: <select id="magic_target" name="magic_target" class="magic-target-select" required>';
    foreach ($validTargets as $target) {
        if (getPlayerStat($target, 'PV') <= 0) {
            echo '<option value="' . $target . '">' . $target . '</option>';
        }
    }
    echo '</select></label><br>Apenas mortos são alvos válidos.';
}

function inputInhaerescorpus($caster){
    $validTargets = getValidTargets($caster);
    echo '<label>Selecionar Alvo: <select id="magic_target" name="magic_target" class="magic-target-select" required>';
    echo selectTarget($caster, $validTargets);
    echo '</select></label>';
    echo '<br><label>Dado para o Teste de Resistência: <input type="number" name="testR" min="1" max="6" required></label>';
}

function inputAmorIncontestavel($caster){
    $validTargets = getValidTargets($caster, 'allies');
    echo '<label>Selecionar Alvo: <select id="magic_target" name="magic_target" class="magic-target-select" required>';
    echo selectTarget($caster, $validTargets, true);
    echo '</select></label><br>';
    echo '<label>Paixão do alvo: <select id="magic_target" name="magic_love" class="magic-target-select" required>';
    echo selectTarget($caster, $validTargets, true);
    echo '</select></label>';
    echo '<br><label>Dado para o Teste de Resistência: <input type="number" name="testR" min="1" max="6" required></label>';
}

function inputHemeopsia(){
    echo 'Essa magia não precisa de inputs.';
}

function inputAtaqueVorpal($caster){
    $validTargets = getValidTargets($caster, 'allies');
    echo '<label>Selecionar Alvo: <select id="magic_target" name="magic_target" class="magic-target-select" required>';
    echo selectTarget($caster, $validTargets, true);
    echo '</select></label>';
}

function inputCegueira($caster){
    $validTargets = getValidTargets($caster);
    echo '<label>Selecionar Alvo: <select id="magic_target" name="magic_target" class="magic-target-select" required>';
    echo selectTarget($caster, $validTargets);
    echo '</select></label>';
    echo '<br><label>Dado para o Teste de Resistência: <input type="number" name="testR" min="1" max="6" required></label>';
}

function inputCuraParaOMal($caster){
    $targets = getValidTargets($caster);
    $validTargets = [];
    foreach ($targets as $tgt){
        if (getPlayerStat($tgt, 'R') < getPlayerStat($caster, 'H')){
            $validTargets[] = $tgt;
        }
    }
    echo '<label>Selecionar Alvo: <select id="magic_target" name="magic_target" class="magic-target-select" required>';
    echo selectTarget($caster, $validTargets);
    echo '</select></label><br>';
    echo '<label>Criatura: <select name="evil_cure_mode" required><option value="amiga">Deixar de ser Inimiga</option><option value="aliada">Tornar-se Aliada</option></select></label>';
    echo '<br><label>>A Criatura precisa estar INDEFESA<</label>';
}

function inputDesmaio($caster){
    $targets = getValidTargets($caster);
    $validTargets = [];
    foreach ($targets as $tgt){
        if (getPlayerStat($tgt, 'R') < getPlayerStat($caster, 'H')){
            $validTargets[] = $tgt;
        }
    }
    echo '<label>Selecionar Alvo: <select id="magic_target" name="magic_target" class="magic-target-select" required>';
    echo selectTarget($caster, $validTargets);
    echo '</select></label>';
    echo '<br><label>Custo: <input type="number" name="cost" min="2" value="2" required></label>';
    echo '<br><label>Dado para o Teste de Resistência +1: <input type="number" name="testR" min="1" max="6" required></label>';
}

function inputDestrancar(){
    echo "Essa magia não precisa de inputs.";
}

function inputAEscapatoriaDeValkaria(){
    echo '<label>Quantidade de aliados: <input type="number" name="qtdAlly" min="1" required></label>';
}

function inputFadaServil(){
    echo "Essa magia não precisa de inputs.";
}

function inputFarejarTesouro(){
    echo "Essa magia não precisa de inputs.";
}

function inputLuz(){
    echo "Essa magia não precisa de inputs.";
}

function inputFlorPereneDeMiladyA($caster){
    $validTargets = getValidTargets($caster, 'allies');
    echo '<label>Selecionar Alvo: <select id="flor_perene_target" name="magic_target" required>';
    echo '<option value="inanimado">Algo Inanimado</option>';
    echo selectTarget($caster, $validTargets, true);
    echo '</select></label>';
    echo '<div id="flor_perene_r_test_container" style="display:none; margin-top: 10px;">';
    echo '<label>Dado para o Teste de Resistência: <input type="number" name="testR" min="1" max="6"></label>';
    echo '</div>';
    echo <<<JS
    <script>
    (function() {
        const targetSelect = document.getElementById('flor_perene_target');
        if (!targetSelect) return;
        const rTestContainer = document.getElementById('flor_perene_r_test_container');
        const rTestInput = rTestContainer ? rTestContainer.querySelector('input[name="testR"]') : null;
        if (!rTestContainer || !rTestInput) return;
        function toggleRTest() {
            if (targetSelect.value === 'inanimado') {
                rTestContainer.style.display = 'none';
                rTestInput.required = false;
            } else {
                rTestContainer.style.display = 'block';
                rTestInput.required = true;
            }
        }
        targetSelect.addEventListener('change', toggleRTest);
        toggleRTest();
    })();
    </script>
JS;
}

function inputFurtividadeDeHyninn($caster){
    $validTargets = getValidTargets($caster, 'allies');
    echo '<label>Selecionar Alvo: <select id="magic_target" name="magic_target" class="magic-target-select" required>';
    echo selectTarget($caster, $validTargets);
    echo '</select></label>';
}

function inputProtecaoMagicaSuperior($caster){
    $validTargets = getValidTargets($caster, 'allies');
    echo '<label>Selecionar Alvo: <select id="magic_target" name="magic_target" class="magic-target-select" required>';
    echo selectTarget($caster, $validTargets);
    echo '</select></label><br>';
    echo '<label>Custo: <input type="number" name="custo" min="1" max="5"></label>';
}

function inputProtecaoMagica($caster){
    $validTargets = getValidTargets($caster, 'allies');
    echo '<label>Selecionar Alvo: <select id="magic_target" name="magic_target" class="magic-target-select" required>';
    echo selectTarget($caster, $validTargets);
    echo '</select></label><br>';
    echo '<label>Custo: <input type="number" name="custo" min="2" max="10"></label>';
}

function inputRecuperacaoNatural($caster){
    $validTargets = getValidTargets($caster, 'allies');
    echo '<label>Selecionar Alvo: <select id="magic_target" name="magic_target" class="magic-target-select" required>';
    echo selectTarget($caster, $validTargets);
    echo '</select></label><br>';
}

function inputReflexos(){
    echo '<label>Dado: <input type="number" name="dado" min="1" max="6"></label>';
}

function inputRetribuicaoDeWynna(){
    echo 'Essa magia não precisa de input.';
}

function inputSentidosEspeciais(){
    echo '<label>Selecionar Alvo: <select id="magic_sense" name="magic_sense" required>';
    echo '<option value="faro_agucado">Faro Aguçado</option>';
    echo '<option value="audicao_augucada">Audição Aguçada</option>';
    echo '<option value="visao_agucada">Visão Aguçada</option>';
    echo '<option value="radar">Radar</option>';
    echo '<option value="infravisao">Infravisão</option>';
    echo '<option value="ver_o_invisivel">Ver o Invisível</option>';
    echo '<option value="visao_raio_x">Visão Raio X</option>';
    echo '</select></label><br>';
}

function inputTeleportacaoAprimorada(){
    echo '<label>Custo do teleporte: <input type="number" name="cost" min="1"></label>';
}

function inputTeleportacao(){
    echo '<label>Custo do teleporte: <input type="number" name="cost" min="1"></label>';
}

function inputTeleportacaoPlanar(){
    echo '<label>Custo do teleporte: <input type="number" name="cost" min="1"></label>';
}

function inputTransporte(){
    echo '<label>Custo do transporte: <input type="number" name="cost" min="1"></label>';
}

function inputDeteccaoDeMagia(){
    echo 'Essa magia não precisa de input.';
}

function inputCancelamento(){
    echo '<p>Selecione a magia a ser cancelada:</p>';
    echo '<select name="magic_to_cancel">';

    echo '<option value="invisibilidade_no_inimigo_X">Invisibilidade (Inimigo X)</option>';
    echo '</select>';
}
?>