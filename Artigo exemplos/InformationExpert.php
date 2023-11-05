<?php


class Order {
    private $customerId;
    private $orderItems;

    // Getters e Setters...

    public function calculateTotal() {
        $total = 0;
        foreach ($this->orderItems as $item) {
            $total += $item->getPrice() * $item->getQuantity();
        }
        return $total;
    }
}

class OrderItem {
    private $productId;
    private $quantity;
    private $price;

    // Getters e Setters...
}

?>