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