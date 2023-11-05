<?php

    interface Pagamento{
        public function efetuarPagamento($metodoPagamento);
        public function imprimirComprovante();
    }

