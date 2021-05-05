<?php


namespace Renter;


use App\Models\Property;
use App\Models\PropertyType;
use App\Models\Renter;
use Database\Seeders\WilayaCommuneSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class RenterPropertiesUpdateTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_renter_can_update_property_title()
    {
        $this->withoutExceptionHandling();
        $renter = Renter::factory()->create();
        PropertyType::factory()->create();
        $this->seed(WilayaCommuneSeeder::class);
        $property = Property::factory()->create(['user_id' => $renter->id]);
        $property->title = 'welcome';
        $property->type = 'house';
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
        PropertyType::factory()->create();
        $renter = Renter::factory()->create();
        $property = Property::factory()->create(['user_id' => $renter->id]);
        $this->seed(WilayaCommuneSeeder::class);
        $title = $property->title;
        $property->title = '';
        $property->type = 'house';
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
        PropertyType::factory()->create();
        $renter = Renter::factory()->create();
        $property = Property::factory()->create(['user_id' => $renter->id]);
        $this->seed(WilayaCommuneSeeder::class);
        $property->state = 'Oran';
        $property->type = 'house';
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
        PropertyType::factory()->create();
        $renter = Renter::factory()->create();
        $property = Property::factory()->create(['user_id' => $renter->id]);
        $this->seed(WilayaCommuneSeeder::class);
        $state = $property->state;
        $property->state = '';
        $property->type = 'house';
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
        PropertyType::factory()->create();
        $renter = Renter::factory()->create();
        $property = Property::factory()->create(['user_id' => $renter->id]);
        $this->seed(WilayaCommuneSeeder::class);
        $property->city = 'Baraki';
        $property->type = 'house';
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
        PropertyType::factory()->create();
        $renter = Renter::factory()->create();
        $property = Property::factory()->create(['user_id' => $renter->id]);
        $this->seed(WilayaCommuneSeeder::class);
        $city = $property->city;
        $property->city = '';
        $property->type = 'house';
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
        PropertyType::factory()->create();
        $renter = Renter::factory()->create();
        $property = Property::factory()->create(['user_id' => $renter->id]);
        $this->seed(WilayaCommuneSeeder::class);
        $property->street = 'some street';
        $property->type = 'house';
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
        PropertyType::factory()->create();
        $renter = Renter::factory()->create();
        $property = Property::factory()->create(['user_id' => $renter->id]);
        $this->seed(WilayaCommuneSeeder::class);
        $street = $property->street;
        $property->street = '';
        $property->type = 'house';
        $property = $property->toArray();
        $this->actingAs($renter)->json('patch','/properties/'. $property['id'], $property)
            ->assertStatus(403)
            ->assertJsonValidationErrors('street');

        $this->assertDatabaseCount('properties',1);
        $this->assertDatabaseHas('properties', ['street' => $street]);
    }

    /** @test */
    public function a_renter_can_update_property_price()
    {
        $this->withoutExceptionHandling();
        PropertyType::factory()->create();
        $renter = Renter::factory()->create();
        $property = Property::factory()->create(['user_id' => $renter->id]);
        $this->seed(WilayaCommuneSeeder::class);
        $property->price = 4000;
        $property->type = 'house';
        $property = $property->toArray();
        $this->actingAs($renter)->json('patch','/properties/'. $property['id'], $property)
            ->assertStatus(200);

        $this->assertDatabaseCount('properties',1);
        $this->assertDatabaseHas('properties', ['price' => 4000]);
    }
    /** @test */
    public function a_renter_property_price_is_required()
    {
        $this->withoutExceptionHandling();
        PropertyType::factory()->create();
        $renter = Renter::factory()->create();
        $property = Property::factory()->create(['user_id' => $renter->id]);
        $this->seed(WilayaCommuneSeeder::class);
        $price = $property->price;
        $property->price = '';
        $property->type = 'house';
        $property = $property->toArray();
        $this->actingAs($renter)->json('patch','/properties/'. $property['id'], $property)
            ->assertStatus(403)
            ->assertJsonValidationErrors('price');

        $this->assertDatabaseCount('properties',1);
        $this->assertDatabaseHas('properties', ['price' => $price]);
    }
    /** @test */
    public function a_renter_property_price_cannot_be_less_than_200()
    {
        $this->withoutExceptionHandling();
        PropertyType::factory()->create();
        $renter = Renter::factory()->create();
        $property = Property::factory()->create(['user_id' => $renter->id]);
        $this->seed(WilayaCommuneSeeder::class);
        $price = $property->price;
        $property->price = 150;
        $property->type = 'house';
        $property = $property->toArray();
        $this->actingAs($renter)->json('patch','/properties/'. $property['id'], $property)
            ->assertStatus(403)
            ->assertJsonValidationErrors('price');

        $this->assertDatabaseCount('properties',1);
        $this->assertDatabaseHas('properties', ['price' => $price]);
    }


    /** @test */
    public function a_renter_property_rooms_can_be_updated()
    {
        $this->withoutExceptionHandling();
        PropertyType::factory()->create();
        $renter = Renter::factory()->create();
        $property = Property::factory()->create(['user_id' => $renter->id]);
        $this->seed(WilayaCommuneSeeder::class);
        $rooms = $property->rooms + 1;
        $property->rooms = $property->rooms + 1;
        $property->type = 'house';
        $property = $property->toArray();
        $this->actingAs($renter)->json('patch','/properties/'. $property['id'], $property)
            ->assertStatus(200);

        $this->assertDatabaseCount('properties',1);
        $this->assertDatabaseHas('properties', ['rooms' => $rooms]);
    }

    /** @test */
    public function a_renter_property_description_cannot_be_empty()
    {
        PropertyType::factory()->create();
        $renter = Renter::factory()->create();
        $property = Property::factory()->create(['user_id' => $renter->id]);
        $this->seed(WilayaCommuneSeeder::class);
        $desc = $property->description;
        $property->description = '';
        $property->type = 'house';
        $property = $property->toArray();
        $this->actingAs($renter)->json('patch','/properties/'. $property['id'], $property)
            ->assertStatus(403);

        $this->assertDatabaseCount('properties',1);
        $this->assertDatabaseHas('properties', ['description' => $desc]);
    }
    /** @test */
    public function if_pics_are_not_changed_the_you_can_update_and_keep_old_pics()
    {
        $renter = Renter::factory()->create();
        PropertyType::factory()->create();
        $property = Property::factory()->create(['user_id' => $renter->id]);
        $property->images()->create(['url' => '1.jpg']);
        $property->images()->create(['url' => '2.jpg']);
        $this->seed(WilayaCommuneSeeder::class);
        $data = [
            'title' => 'prop 1',
            'state' => 'Alger',
            'city' => 'Alger Centre',
            'street' => 'lorem ipsum',
            'price' => 5000,
            'bedrooms' => 4,
            'bathrooms' => 4,
            'beds' => 4,
            'type' => 'house',
            'description' => 'some text for description',
            'rooms' => 3,
        ];
        $this->actingAs($renter)->json('patch','/properties/'. $property->id, $data)
            ->assertStatus(200);
        $this->assertDatabaseCount('properties',1);
        $this->assertDatabaseCount('images', 2);
        $this->assertDatabaseHas('properties', collect($data)->except('type')->toArray());
    }
    /** @test */
    public function if_pics_are_updated_if_exist_in_request()
    {

        $renter = Renter::factory()->create();
        PropertyType::factory()->create();
        $property = Property::factory()->create(['user_id' => $renter->id]);
        $property->images()->create(['url' => '1.jpg']);
        $property->images()->create(['url' => '2.jpg']);
        $this->seed(WilayaCommuneSeeder::class);
        $pictures = [
            '0' => UploadedFile::fake()->image('pic1.png'),
            '1' => UploadedFile::fake()->image('pic2.png'),
            '2' => UploadedFile::fake()->image('pic3.png'),
            '3' => UploadedFile::fake()->image('pic4.png'),
        ];
        $property->images = $pictures;
        $property->type='house';
        $property = $property->toArray();
        $this->actingAs($renter)->json('patch','/properties/'. $property['id'], $property)
            ->assertStatus(200);
        $this->assertDatabaseCount('properties',1);
        $this->assertDatabaseCount('images', 4);
    }
    /** @test */
    public function a_renter_property_bedrooms_required()
    {
        $this->withoutExceptionHandling();
        PropertyType::factory()->create();
        $renter = Renter::factory()->create();
        $property = Property::factory()->create(['user_id' => $renter->id]);
        $this->seed(WilayaCommuneSeeder::class);
        $bedrooms = $property->bedrooms;
        $property->bedrooms = '';
        $property->type = 'house';
        $property = $property->toArray();
        $this->actingAs($renter)->json('patch','/properties/'. $property['id'], $property)
            ->assertStatus(403);
        $this->assertDatabaseHas('properties',['bedrooms' => $bedrooms]);
    }
    /** @test */
    public function a_renter_property_bathrooms_required()
    {
        $this->withoutExceptionHandling();
        PropertyType::factory()->create();
        $renter = Renter::factory()->create();
        $property = Property::factory()->create(['user_id' => $renter->id]);
        $this->seed(WilayaCommuneSeeder::class);
        $bathrooms = $property->bathrooms;
        $property->bathrooms = '';
        $property->type = 'house';
        $property = $property->toArray();
        $this->actingAs($renter)->json('patch','/properties/'. $property['id'], $property)
            ->assertStatus(403);
        $this->assertDatabaseHas('properties',['bathrooms' => $bathrooms]);
    }
    /** @test */
    public function a_renter_property_beds_required()
    {
        $this->withoutExceptionHandling();
        PropertyType::factory()->create();
        $renter = Renter::factory()->create();
        $property = Property::factory()->create(['user_id' => $renter->id]);
        $this->seed(WilayaCommuneSeeder::class);
        $beds = $property->beds;
        $property->beds = '';
        $property->type = 'house';
        $property = $property->toArray();
        $this->actingAs($renter)->json('patch','/properties/'. $property['id'], $property)
            ->assertStatus(403);
        $this->assertDatabaseHas('properties',['beds' => $beds]);
    }
}
