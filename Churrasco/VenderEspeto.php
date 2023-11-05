<?php

class VenderEspeto extends Venda implements Pagamento {



public function efetuarPagamento($metodoPagamento) {
    $total = $this->calcularAll(); // Calcula o valor total da venda


    switch ($metodoPagamento) {
        case 'dinheiro':
            echo "Pagamento em dinheiro de R$ " . number_format($total, 2) . " recebido.\n";
            // Registre o pagamento no sistema, se necessário
            break;
        case 'cartao':
            echo "Pagamento com cartão de crédito de R$ " . number_format($total, 2) . " efetuado.\n";
            // Registre o pagamento no sistema, se necessário
            break;
        default:
            echo "Método de pagamento não reconhecido.\n";
            break;
    }
}

public function imprimirComprovante() {
    echo "Comprovante de Pagamento:\n";

    foreach ($this->itens as $item) {
        echo "- {$item['quantidade']} x {$item['nome']} (R$ {$item['preco']} cada)\n";
    }

    $total = $this->calcularAll();
    echo "Total: R$ " . number_format($total, 2) . "\n";
}

public function calcularAll() {
    $total = 0;
    foreach ($this->itens as $item) {
        $total += $item['preco'] * $item['quantidade'];
    }
    return $total;
}
}
