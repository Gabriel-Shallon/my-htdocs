<?php
class App {
    private $carrinho = [];
    

    public function adicionarAoCarrinho($espetinho, $quantidade) {
        // Verifique se há estoque disponível
        if ($espetinho->getQuantidade() >= $quantidade) {
            if (array_key_exists($espetinho->getNome(), $this->carrinho)) {
                $this->carrinho[$espetinho->getNome()]['quantidade'] += $quantidade;
            } else {
                $this->carrinho[$espetinho->getNome()] = [
                    'espetinho' => $espetinho,
                    'quantidade' => $quantidade,
                ];
            }

            // Atualize o estoque
            $espetinho->setQuantidade($espetinho->getQuantidade() - $quantidade);
        } else {
            echo "Desculpe, não há estoque suficiente para esse espetinho.";
        }
    }

    public function getCarrinho() {
        return $this->carrinho;
    }

    public function removerDoCarrinho($nomeEspetinho, $quantidade) {
        if (array_key_exists($nomeEspetinho, $this->carrinho)) {
            $espetinhoNoCarrinho = $this->carrinho[$nomeEspetinho]['espetinho'];
            if ($this->carrinho[$nomeEspetinho]['quantidade'] >= $quantidade) {
                $this->carrinho[$nomeEspetinho]['quantidade'] -= $quantidade;
                // Atualize o estoque
                $espetinhoNoCarrinho->setQuantidade($espetinhoNoCarrinho->getQuantidade() + $quantidade);
            } else {
                echo "Quantidade insuficiente no carrinho para remover.";
            }
        } else {
            echo "O espetinho não está no carrinho.";
        }
    }

    public function processarPagamento($metodoPagamento) {
        $venda = new VenderEspeto();

        foreach ($this->carrinho as $item) {
            $nome = $item['espetinho']->getNome();
            $quantidade = $item['quantidade'];
            $preco = $item['espetinho']->getPreco();
            $venda->adicionarItem($nome, $quantidade, $preco);
        }

        $venda->efetuarPagamento($metodoPagamento);
        $venda->imprimirComprovante();

        // Limpe o carrinho após a conclusão da venda
        $this->carrinho = [];
    }

    



}
