<?php


use App\Services\PriceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PriceServiceTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function is_fee_calculated_correctly_when_lower_than_5000() {
        $price = 1000;
        $price_service = new PriceService(1,$price);

        $this->assertEquals(500, $price_service->getDayFee());
    }

    /** @test */
    public function is_fee_calculated_correctly_when_more_than_5000() {
        $price = 6000;
        $price_service = new PriceService(1,$price);

        $this->assertEquals(600, $price_service->getDayFee());
    }

    /** @test */
    public function is_fee_calculated_correctly_when_equals_Zero() {
        $price = 0;
        $price_service = new PriceService(1,$price);

        $this->assertEquals(0, $price_service->getDayFee());
    }

    /** @test */
    public function get_total_fee_less_than_5000() {
        $price = 4000;
        $price_service = new PriceService(5,$price);
        $this->assertEquals(2500, $price_service->getTotalFee());
    }
    /** @test */
    public function get_total_fee_more_than_5000() {
        $price = 6000;
        $price_service = new PriceService(5,$price);
        $this->assertEquals(3000, $price_service->getTotalFee());
    }
    /** @test */
    public function get_total_price_when_less_than_5000() {
        $price = 4000;
        $price_service = new PriceService(5,$price);
        $this->assertEquals(22500, $price_service->getTotalPrice());
    }
    /** @test */
    public function get_total_price_when_more_than_5000() {
        $price = 6000;
        $price_service = new PriceService(5,$price);
        $this->assertEquals(33000, $price_service->getTotalPrice());
    }
}

