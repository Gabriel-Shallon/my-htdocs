<?php

class Car {
    public function start() {
        // Lógica para iniciar o carro...
    }
}

class CarFactory {
    public function createCar() {
        $car = new Car();
        // Configurações adicionais do carro...
        return $car;
    }
}
