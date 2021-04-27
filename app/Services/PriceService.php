<?php


namespace App\Services;


class PriceService
{
    private $price;

    public function __construct($price = 0)
    {
        $this->price = $price;
    }

    public function getFee()
    {
        if($this->price === 0) {
            return 0;
        }
        if($this->price > 5000) {
            return ($this->price * 10) / 100;
        }
        return 500;
    }

    public function getTotal() {
        return $this->price + $this->getFee();
    }
}
