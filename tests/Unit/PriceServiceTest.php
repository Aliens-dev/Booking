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
        $price_service = new PriceService($price);

        $this->assertEquals(500, $price_service->getFee());
    }

    /** @test */
    public function is_fee_calculated_correctly_when_more_than_5000() {
        $price = 6000;
        $price_service = new PriceService($price);

        $this->assertEquals(600, $price_service->getFee());
    }

    /** @test */
    public function is_fee_calculated_correctly_when_equals_Zero() {
        $price = 0;
        $price_service = new PriceService($price);

        $this->assertEquals(0, $price_service->getFee());
    }

    /** @test */
    public function is_Total_calculated_correctly_when_equals_Zero() {
        $price = 0;
        $price_service = new PriceService($price);

        $this->assertEquals(0, $price_service->getTotal());
    }

    /** @test */
    public function is_Total_calculated_correctly_when_more_than_5000() {
        $price = 6000;
        $price_service = new PriceService($price);

        $this->assertEquals(6600, $price_service->getTotal());
    }
    /** @test */
    public function is_Total_calculated_correctly_when_less_than_5000() {
        $price = 4000;
        $price_service = new PriceService($price);

        $this->assertEquals(4500, $price_service->getTotal());
    }
}

