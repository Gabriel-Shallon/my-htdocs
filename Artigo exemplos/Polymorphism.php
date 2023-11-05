<?php

interface Shape {
    public function calculateArea();
}

class Rectangle implements Shape {
    private $width;
    private $height;

    // Getters and Setters...

    public function calculateArea() {
        return $this->width * $this->height;
    }
}

class Circle implements Shape {
    private $radius;

    // Getters and Setters...

    public function calculateArea() {
        return pi() * pow($this->radius, 2);
    }
}
