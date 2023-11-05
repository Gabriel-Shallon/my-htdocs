<?php

class OrderProcessor {
    private $paymentProcessor;

    public function __construct(PaymentProcessor $paymentProcessor) {
        $this->paymentProcessor = $paymentProcessor;
    }

    public function processOrder($order) {
        // Lógica para processar o pedido...
        $this->paymentProcessor->processPayment($order->getTotal());
    }
}
