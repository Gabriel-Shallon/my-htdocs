<?php 

interface PaymentGateway {
    public function processPayment($amount);
}

class PayPalPaymentGateway implements PaymentGateway {
    public function processPayment($amount) {
        // Lógica para processar o pagamento via PayPal...
    }
}

class StripePaymentGateway implements PaymentGateway {
    public function processPayment($amount) {
        // Lógica para processar o pagamento via Stripe...
    }
}

class PaymentProcessor {
    private $paymentGateway;

    public function __construct(PaymentGateway $paymentGateway) {
        $this->paymentGateway = $paymentGateway;
    }

    public function processPayment($amount) {
        // Lógica de proteção contra variações...
        $this->paymentGateway->processPayment($amount);
    }
}
