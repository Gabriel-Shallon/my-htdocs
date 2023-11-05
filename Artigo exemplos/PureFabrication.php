<?php

class EmailSender {
    public function sendEmail($recipient, $subject, $body) {
        // Lógica para enviar o email...
    }
}

class OrderService {
    private $emailSender;

    public function __construct() {
        $this->emailSender = new EmailSender();
    }

    public function processOrder($order) {
        // Lógica para processar o pedido...
        $this->emailSender->sendEmail($order->getCustomerEmail(), 'Pedido Confirmado', 'Seu pedido foi recebido com sucesso.');
    }
}
