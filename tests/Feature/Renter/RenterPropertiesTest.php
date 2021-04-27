<?php


namespace Renter;


use App\Models\User;
use Database\Seeders\WilayaCommuneSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Kossa\AlgerianCities\Commune;
use Kossa\AlgerianCities\Wilaya;
use Tests\TestCase;

class RenterPropertiesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_renter_can_add_new_property() {
        $this->withoutExceptionHandling();

        $renter = User::factory()->create(['user_role' => 'renter']);
        $this->seed(WilayaCommuneSeeder::class);
        $data = [
            'title' => 'prop 1',
            'state' => 'Alger',
            'city' => 'Alger Centre',
            'street' => 'stade 20 aout',
            'price' => 5000,
            'type' => 'house',
            'description' => 'some text for description'
        ];
        $response = $this->actingAs($renter)->json('POST', '/properties', $data);

        $response->assertStatus(201);
        $this->assertDatabaseCount('properties',1);
        $this->assertDatabaseHas('properties',$data);
    }

    /** @test */
    public function a_property_title_is_required()
    {
        $this->withoutExceptionHandling();
        $renter = User::factory()->create(['user_role' => 'renter']);
        $data = [
            'title' => '',
            'state' => 'Alger',
            'city' => 'Alger Centre',
            'street' => 'stade 20 aout',
            'price' => 5000,
            'type' => 'house',
            'description' => 'some text for description'
        ];

        $this->actingAs($renter)->post('/properties', $data)
            ->assertStatus(403);
        $this->assertDatabaseCount('properties',0);
    }

    /** @test */
    public function a_property_title_min()
    {
        $this->withoutExceptionHandling();
        $renter = User::factory()->create(['user_role' => 'renter']);
        $data = [
            'title' => 'a',
            'state' => 'Alger',
            'city' => 'Alger Centre',
            'street' => 'stade 20 aout',
            'price' => 5000,
            'type' => 'house',
            'description' => 'some text for description'
        ];

        $this->actingAs($renter)->post('/properties', $data)
            ->assertStatus(403);
        $this->assertDatabaseCount('properties',0);
    }

    /** @test */
    public function a_property_title_max()
    {
        $this->withoutExceptionHandling();
        $renter = User::factory()->create(['user_role' => 'renter']);
        $data = [
            'title' => 'lorem ipsum,lorem ipsum,lorem ipsum,lorem ipsum,lorem ipsum,lorem ipsum,lorem ipsum,lorem ipsum,lorem ipsum,lorem ipsum',
            'state' => 'Alger',
            'city' => 'Alger Centre',
            'street' => 'stade 20 aout',
            'price' => 5000,
            'type' => 'house',
            'description' => 'some text for description'
        ];

        $this->actingAs($renter)->post('/properties', $data)
            ->assertStatus(403);
        $this->assertDatabaseCount('properties',0);
    }

    /** @test */
    public function a_property_state_only_if_exists() {
        $this->withoutExceptionHandling();

        $renter = User::factory()->create(['user_role' => 'renter']);
        $this->seed(WilayaCommuneSeeder::class);
        $data = [
            'title' => 'prop 1',
            'state' => 'Algeraa',
            'city' => 'Alger Centre',
            'street' => 'stade 20 aout',
            'price' => 5000,
            'type' => 'house',
            'description' => 'some text for description'
        ];
        $response = $this->actingAs($renter)->json('POST', '/properties', $data);

        $response->assertStatus(403);
        $this->assertDatabaseCount('properties',0);
    }

    /** @test */
    public function a_property_city_only_if_exists() {
        $this->withoutExceptionHandling();

        $renter = User::factory()->create(['user_role' => 'renter']);
        $this->seed(WilayaCommuneSeeder::class);
        $data = [
            'title' => 'prop 1',
            'state' => 'Alger',
            'city' => 'Alger Centress',
            'street' => 'stade 20 aout',
            'price' => 5000,
            'type' => 'house',
            'description' => 'some text for description'
        ];
        $response = $this->actingAs($renter)->json('POST', '/properties', $data);

        $response->assertStatus(403);
        $this->assertDatabaseCount('properties',0);
    }

    /** @test */
    public function a_property_street_is_required() {
        $this->withoutExceptionHandling();

        $renter = User::factory()->create(['user_role' => 'renter']);
        $this->seed(WilayaCommuneSeeder::class);
        $data = [
            'title' => 'prop 1',
            'state' => 'Alger',
            'city' => 'Alger Centre',
            'street' => '',
            'price' => 5000,
            'type' => 'house',
            'description' => 'some text for description'
        ];
        $response = $this->actingAs($renter)->json('POST', '/properties', $data);
        $response->assertStatus(403);
        $this->assertDatabaseCount('properties',0);
    }
    /** @test */
    public function a_property_price_is_required() {
        $this->withoutExceptionHandling();

        $renter = User::factory()->create(['user_role' => 'renter']);
        $this->seed(WilayaCommuneSeeder::class);
        $data = [
            'title' => 'prop 1',
            'state' => 'Alger',
            'city' => 'Alger Centre',
            'street' => 'lorem ipsum',
            'type' => 'house',
            'description' => 'some text for description'
        ];
        $response = $this->actingAs($renter)->json('POST', '/properties', $data);
        $response->assertStatus(403);
        $this->assertDatabaseCount('properties',0);
    }
}
