<?php

class Ator {
    private $ator_id;
    private $primeiro_nome;
    private $ultimo_nome;

    public function __construct($ator_id, $primeiro_nome, $ultimo_nome) {
        $this->ator_id = $ator_id;
        $this->primeiro_nome = $primeiro_nome;
        $this->ultimo_nome = $ultimo_nome;
    }

    public function getAtor_id() {
        return $this->ator_id;
    }

    public function getPrimeiro_nome() {
        return $this->primeiro_nome;
    }

    public function getUltimo_nome() {
        return $this->ultimo_nome;
    }
}
?>
