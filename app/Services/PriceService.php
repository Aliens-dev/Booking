<?php


namespace App\Services;


class PriceService
{
    private $pricePerDay;

    public function __construct($days, $pricePerDay)
    {
        $this->pricePerDay = $pricePerDay;
        $this->days = $days;
    }

    public function getDayFee()
    {
        if($this->pricePerDay === 0) {
            return 0;
        }
        if($this->pricePerDay > 5000) {
            return ($this->pricePerDay * 10) / 100;
        }
        return 500;
    }

    public function getTotalFee()
    {
        return $this->getDayFee() * $this->days;
    }

    public function getTotalPrice()
    {
        return ($this->pricePerDay * $this->days) + $this->getTotalFee();
    }

    public static function calculateFee($days, $price) {
        
    }
}
