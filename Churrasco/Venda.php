<?php
abstract class Venda {
    protected $itens = array();

    public function adicionarItem($nome, $quantidade, $preco) {
        $this->itens[] = array(
            'nome' => $nome,
            'quantidade' => $quantidade,
            'preco' => $preco
        );
    }

    abstract public function calcularAll();

    public function emitirRecibo() {
        echo "Recibo da Venda:\n";
        foreach ($this->itens as $item) {
            echo "- {$item['quantidade']} x {$item['nome']} (R$ {$item['preco']} cada)\n";
        }
        echo "Total: R$" . number_format($this->calcularAll(), 2) . "\n";
    }
}
