<?php
    
    function conecta(){
        $pdo = new PDO('mysql:dbname=RPG;charset=utf8mb4','root','');
        $pdo -> setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
        return $pdo;
    }

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

    function slugify(string $str): string {
        if (class_exists('Normalizer')) {
            $str = Normalizer::normalize($str, Normalizer::FORM_D);
        }
        $str = preg_replace('/\pM+/u', '', $str);
        $str = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $str);
        $str = preg_replace('/[^A-Za-z0-9 ]/', '', $str);
        $str = strtolower(str_replace(' ', '_', $str));
        return $str;
    }



