<?php


namespace Renter;


use App\Models\Property;
use App\Models\User;
use Database\Seeders\WilayaCommuneSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RenterPropertiesUpdateTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_renter_can_update_property_title()
    {
        $this->withoutExceptionHandling();

        $renter = User::factory()->create(['user_role' => 'renter']);
        $this->seed(WilayaCommuneSeeder::class);
        $property = Property::factory()->create(['user_id' => $renter->id]);
        $property->title = 'welcome';
        $property = $property->toArray();
        $this->actingAs($renter)->json('patch','/properties/'. $property['id'], $property)
            ->assertStatus(200);

        $this->assertDatabaseCount('properties',1);
        $this->assertDatabaseHas('properties', ['title' => 'welcome']);
    }
    /** @test */

    public function a_renter_property_title_is_required()
    {
        $this->withoutExceptionHandling();

        $renter = User::factory()->create(['user_role' => 'renter']);
        $property = Property::factory()->create(['user_id' => $renter->id]);
        $this->seed(WilayaCommuneSeeder::class);
        $title = $property->title;
        $property->title = '';
        $property = $property->toArray();
        $this->actingAs($renter)->json('patch','/properties/'. $property['id'], $property)
            ->assertStatus(403)
            ->assertJsonValidationErrors('title');
        $this->assertDatabaseHas('properties', ['title' => $title]);
    }

    /** @test */
    public function a_renter_can_update_property_state()
    {
        $this->withoutExceptionHandling();

        $renter = User::factory()->create(['user_role' => 'renter']);
        $property = Property::factory()->create(['user_id' => $renter->id]);
        $this->seed(WilayaCommuneSeeder::class);
        $property->state = 'Oran';
        $property = $property->toArray();
        $this->actingAs($renter)->json('patch','/properties/'. $property['id'], $property)
            ->assertStatus(200);

        $this->assertDatabaseCount('properties',1);
        $this->assertDatabaseHas('properties', ['state' => 'Oran']);
    }
    /** @test */
    public function a_renter_property_state_is_required()
    {
        $this->withoutExceptionHandling();

        $renter = User::factory()->create(['user_role' => 'renter']);
        $property = Property::factory()->create(['user_id' => $renter->id]);
        $this->seed(WilayaCommuneSeeder::class);
        $state = $property->state;
        $property->state = '';
        $property = $property->toArray();
        $this->actingAs($renter)->json('patch','/properties/'. $property['id'], $property)
            ->assertStatus(403)
            ->assertJsonValidationErrors('state');

        $this->assertDatabaseCount('properties',1);
        $this->assertDatabaseHas('properties', ['state' => $state]);
    }

    /** @test */
    public function a_renter_can_update_property_city()
    {
        $this->withoutExceptionHandling();

        $renter = User::factory()->create(['user_role' => 'renter']);
        $property = Property::factory()->create(['user_id' => $renter->id]);
        $this->seed(WilayaCommuneSeeder::class);
        $property->city = 'Baraki';
        $property = $property->toArray();
        $this->actingAs($renter)->json('patch','/properties/'. $property['id'], $property)
            ->assertStatus(200);

        $this->assertDatabaseCount('properties',1);
        $this->assertDatabaseHas('properties', ['city' => 'Baraki']);
    }
    /** @test */
    public function a_renter_property_city_is_required()
    {
        $this->withoutExceptionHandling();

        $renter = User::factory()->create(['user_role' => 'renter']);
        $property = Property::factory()->create(['user_id' => $renter->id]);
        $this->seed(WilayaCommuneSeeder::class);
        $city = $property->city;
        $property->city = '';
        $property = $property->toArray();
        $this->actingAs($renter)->json('patch','/properties/'. $property['id'], $property)
            ->assertStatus(403)
            ->assertJsonValidationErrors('city');

        $this->assertDatabaseCount('properties',1);
        $this->assertDatabaseHas('properties', ['city' => $city]);
    }

    /** @test */
    public function a_renter_can_update_property_street()
    {
        $this->withoutExceptionHandling();

        $renter = User::factory()->create(['user_role' => 'renter']);
        $property = Property::factory()->create(['user_id' => $renter->id]);
        $this->seed(WilayaCommuneSeeder::class);
        $property->street = 'some street';
        $property = $property->toArray();
        $this->actingAs($renter)->json('patch','/properties/'. $property['id'], $property)
            ->assertStatus(200);

        $this->assertDatabaseCount('properties',1);
        $this->assertDatabaseHas('properties', ['street' => 'some street']);
    }
    /** @test */
    public function a_renter_property_street_is_required()
    {
        $this->withoutExceptionHandling();

        $renter = User::factory()->create(['user_role' => 'renter']);
        $property = Property::factory()->create(['user_id' => $renter->id]);
        $this->seed(WilayaCommuneSeeder::class);
        $street = $property->street;
        $property->street = '';
        $property = $property->toArray();
        $this->actingAs($renter)->json('patch','/properties/'. $property['id'], $property)
            ->assertStatus(403)
            ->assertJsonValidationErrors('street');

        $this->assertDatabaseCount('properties',1);
        $this->assertDatabaseHas('properties', ['street' => $street]);
    }
}
