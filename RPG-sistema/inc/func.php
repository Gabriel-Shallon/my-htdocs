<?php
//funcao conexao com banco de dados
    function conecta(){
        $pdo = new PDO('mysql:dbname=RPG;charset=utf8mb4','root','');
        //com xampp - não usa semha
        //se não usa xampp - senha 'root'
        $pdo -> setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
        //Ativar no PDO o modo de tratar erros ao conectar no banco

        return $pdo;
    }//conecta

    function newPlayer($nome, $F, $H, $R, $A, $PdF, $PE, $inventario, $equipado){

        $PV_max = ($R > 0 ? $R * 5 : 1);
        $PM_max = ($R > 0 ? $R * 5 : 1);
        $PV     = $PV_max;
        $PM     = $PM_max;

        $pdo = conecta();
        $sql = 'INSERT INTO RPG.player
          (nome,F,H,R,A,PdF,PV,PV_max,PM,PM_max,PE,inventario,equipado)
         VALUES
          (:nome,:F,:H,:R,:A,:PdF,:PV,:PV_max,:PM,:PM_max,:PE,:inventario,:equipado)';
        $query = $pdo->prepare($sql);
        $query->bindValue(':nome',   $nome);
        $query->bindValue(':F',      $F);
        $query->bindValue(':H',      $H);
        $query->bindValue(':R',      $R);
        $query->bindValue(':A',      $A);
        $query->bindValue(':PdF',    $PdF);
        $query->bindValue(':PV',     $PV);
        $query->bindValue(':PV_max', $PV_max);
        $query->bindValue(':PM',     $PM);
        $query->bindValue(':PM_max', $PM_max);
        $query->bindValue(':PE',     $PE);
        $query->bindValue(':inventario',     $inventario);
        $query->bindValue(':equipado',     $equipado);
        $query->execute();

    }

    function getPlayer($nome){
        $pdo = conecta();
        $player = $pdo->prepare('SELECT * FROM RPG.player WHERE nome = :nome');
        $player->bindValue(':nome', $nome, PDO::PARAM_STR);
        $player->execute();
        return $player->fetch(PDO::FETCH_ASSOC) ?? NULL;
    }

    function getPlayerStat(string $nome, string $campo) {
        return getPlayer($nome)[$campo] ?? null;
    }

    function setPlayerStat(string $nome, string $campo, $valor){
        $pdo = conecta();
        $sql = "UPDATE RPG.player SET {$campo} = :valor WHERE nome = :nome";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':valor', $valor);
        $stmt->bindValue(':nome', $nome, PDO::PARAM_STR);
        $stmt->execute();
    }

    function getAllPlayers(){
        $pdo = conecta();
        $players = $pdo->prepare('SELECT nome FROM RPG.player');
        $players->execute();
        return $players->fetchAll(PDO::FETCH_ASSOC) ?? NULL;
    }





    function FA (string $atacante, string $atkType, int $dado){
        return getPlayerStat($atacante, $atkType) + getPlayerStat($atacante, 'H') + $dado;
    }


    function FD (string $defensor, int $dado){
        return getPlayerStat($defensor, 'A') + getPlayerStat($defensor, 'H') + $dado;
    }


    function FDindefeso (string $defensor){
        return getPlayerStat($defensor, 'A');
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
            $totalFA += ($baseFA + (int)$dados[$i]);
        }
    
        return $totalFA;
    }
    




    function FAFDresult(string $atacante, string $defensor, int $dadoFA, int $dadoFD, string $atkType){
        return max(FA($atacante, $atkType, $dadoFA) - FD($defensor,$dadoFD),0);
    }

    
    function FAFDindefeso(string $atacante, string $defensor, int $dadoFA, string $atkType){
        return max(FA($atacante, $atkType, $dadoFA) - FDindefeso($defensor),0);
    }


    function FAFDesquiva(string $atacante, string $defensor, int $dadoEsquiva, int $dadoFA, string $atkType){
        $meta = getPlayerStat($defensor, 'H') - getPlayerStat($atacante, 'H');
        if ($meta <= 0){
            return FAFDindefeso($atacante, $defensor, $dadoFA, $atkType);
        }
        if ($meta > 0 && $meta < 6){
            if($dadoEsquiva <= $meta){
                return 0;
            }else{
                return FAFDindefeso($atacante, $defensor, $dadoFA, $atkType);
            }
        }
        if ($meta>6){
            return 0;
        }
    }




    function iniciativa(array $lutadores, array $dados): array {
        $inicList = [];
        foreach ($lutadores as $idx => $nome) {
            $H = (int) getPlayerStat($nome, 'H');
            $dado = (int) ($dados[$idx] ?? 0);
            $total = $H + $dado;
    
            $inicList[] = [
                'nome'  => $nome,
                'H'     => $H,
                'total' => $total,
            ];
        }
    
        usort($inicList, function($a, $b) {
            return $b['total'] <=> $a['total'];
        });
    
        $resultado = [];
        foreach ($inicList as $item) {
            $resultado[] = [
                $item['nome'],
                $item['H'],
                $item['total'],
            ];
        } 
        return $resultado;
    }


  
