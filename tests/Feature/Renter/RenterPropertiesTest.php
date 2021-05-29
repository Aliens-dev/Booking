<?php


namespace Renter;


use App\Models\Amenity;
use App\Models\Client;
use App\Models\Facility;
use App\Models\Property;
use App\Models\PropertyType;
use App\Models\Renter;
use App\Models\Rule;
use App\Models\TypeOfPlace;
use Database\Factories\TypeOfPlaceFactory;
use Database\Seeders\WilayaCommuneSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RenterPropertiesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('property');
        //$this->seed(WilayaCommuneSeeder::class);
        PropertyType::factory()->count(5)->create();
        TypeOfPlace::factory()->create();
    }

    /** @test */
    public function a_renter_can_add_new_property()
    {
        $this->withoutExceptionHandling();
        $renter = Renter::factory()->create();
        $data = $this->data()->toArray();
        $response = $this->actingAs($renter)->json('POST', route('property.store'), $data);
        $response->assertStatus(201);
        $this->assertDatabaseCount('properties',1);
        $data = collect($data);
        $this->assertDatabaseHas('properties', $data->except('images','type','type_of_place','rules','facilities','amenities')->toArray());
    }

    /** @test */
    public function a_client_cannot_add_new_property()
    {
        $client = Client::factory()->create();
        $data = $this->data()->toArray();
        $response = $this->actingAs($client)->json('POST', route('property.store'), $data);
        $response->assertStatus(401);
        $this->assertDatabaseCount('properties',0);
        $this->assertDatabaseCount('images',0);
    }
    /** @test */
    public function a_visitor_cannot_add_new_property() {
        $data = $this->data()->toArray();
        $this->json('POST', '/properties', $data)
            ->assertStatus(401);
    }

    /** @test */
    public function a_property_title_is_required()
    {
        $renter = Renter::factory()->create();
        $data = $this->data()->except('title')->toArray();
        $this->actingAs($renter)->post(route('property.store'), $data)
            ->assertStatus(403)
            ->assertJsonValidationErrors('title');
        $this->assertDatabaseCount('properties',0);
    }

    /** @test */
    public function a_property_title_min()
    {
        $renter = Renter::factory()->create();
        $data = $this->data()->toArray();
        $data['title'] = 'a';
        $this->actingAs($renter)->post(route('property.store'), $data)
            ->assertStatus(403);
        $this->assertDatabaseCount('properties',0);
    }

    /** @test */
    public function a_property_title_max()
    {
        $renter = Renter::factory()->create();
        $data = $this->data()->toArray();
        $data['title'] = "lorem ipsum,lorem ipsum,lorem ipsum,lorem ipsum,lorem ipsum,lorem ipsum,lorem ipsum,lorem ipsum,lorem ipsum,lorem ipsum";
        $this->actingAs($renter)->post(route('property.store'), $data)
            ->assertStatus(403);
        $this->assertDatabaseCount('properties',0);
    }

    /*
    public function a_property_state_only_if_exists()
    {
        $renter = Renter::factory()->create();
        $data = $this->data()->toArray();
        $data['state'] = 'Aleraa';
        $response = $this->actingAs($renter)->json('POST', route('property.store'), $data);
        $response->assertStatus(403);
        $response->assertJsonValidationErrors('state');
        $this->assertDatabaseCount('properties',0);
    }

    public function a_property_city_only_if_exists()
    {
        $renter = Renter::factory()->create();
        $data = $this->data()->toArray();
        $data['city'] = 'Alger Centress';
        $response = $this->actingAs($renter)->json('POST', route('property.store'), $data);
        $response->assertStatus(403);
        $response->assertJsonValidationErrors('city');
        $this->assertDatabaseCount('properties',0);
    }
    */
    /** @test */
    public function a_property_street_is_required()
    {
        $renter = Renter::factory()->create();
        $data = $this->data()->except('street')->toArray();
        $response = $this->actingAs($renter)->json('POST', route('property.store'), $data);
        $response->assertStatus(403);
        $response->assertJsonValidationErrors('street');
        $this->assertDatabaseCount('properties',0);
    }
    /** @test */
    public function a_property_price_is_required()
    {
        $renter = Renter::factory()->create();
        $data = $this->data()->except('price')->toArray();
        $response = $this->actingAs($renter)->json('POST', route('property.store'), $data);
        $response->assertStatus(403);
        $response->assertJsonValidationErrors('price');
        $this->assertDatabaseCount('properties',0);
    }
    /** @test */
    public function a_property_price_cannot_be_decimal()
    {
        $renter = Renter::factory()->create();
        $data = $this->data()->toArray();
        $data['price'] = 1054.1;
        $response = $this->actingAs($renter)->json('POST', route('property.store'), $data);
        $response->assertStatus(403);
        $response->assertJsonValidationErrors('price');
        $this->assertDatabaseCount('properties',0);
    }
    /** @test */
    public function a_property_price_cannot_be_zero()
    {
        $renter = Renter::factory()->create();
        $data = $this->data()->toArray();
        $data['price'] = 0;
        $response = $this->actingAs($renter)->json('POST', route('property.store'), $data);
        $response->assertStatus(403);
        $response->assertJsonValidationErrors('price');
        $this->assertDatabaseCount('properties',0);
    }

    /** @test */
    public function a_property_rooms_are_required()
    {
        $renter = Renter::factory()->create();
        $data = $this->data()->except('rooms')->toArray();
        $response = $this->actingAs($renter)->json('POST', route('property.store'), $data);
        $response->assertStatus(403);
        $response->assertJsonValidationErrors('rooms');
        $this->assertDatabaseCount('properties',0);
    }
    /** @test */
    public function a_property_rooms_cannot_be_decimal()
    {
        $renter = Renter::factory()->create();
        $data = $this->data()->toArray();
        $data['rooms'] = 1.1;
        $response = $this->actingAs($renter)->json('POST', route('property.store'), $data);
        $response->assertStatus(403);
        $response->assertJsonValidationErrors('rooms');
        $this->assertDatabaseCount('properties',0);
    }

    /** @test */
    public function a_property_rooms_cannot_be_Zero()
    {
        $renter = Renter::factory()->create();
        $data = $this->data()->toArray();
        $data['rooms'] = 0;
        $response = $this->actingAs($renter)->json('POST', route('property.store'), $data);
        $response->assertStatus(403);
        $response->assertJsonValidationErrors('rooms');
        $this->assertDatabaseCount('properties',0);
    }

    /** @test */
    public function a_property_type_is_required()
    {
        $renter = Renter::factory()->create();
        $data = $this->data()->except('type')->toArray();
        $response = $this->actingAs($renter)->json('POST', route('property.store'), $data);
        $response->assertStatus(403);
        $response->assertJsonValidationErrors('type');
        $this->assertDatabaseCount('properties',0);
    }

    /*
    public function a_property_commune_in_correct_wilaya()
    {
        $renter = Renter::factory()->create();
        $data = $this->data()->toArray();
        $data['city'] = 'Beni Aissi';
        $response = $this->actingAs($renter)->json('POST', route('property.store'), $data);
        $response->assertStatus(403);
        $this->assertDatabaseCount('properties',0);
    }
*/
    /** @test */
    public function a_property_bedrooms_required() {
        $renter = Renter::factory()->create();
        $data = $this->data()->except('bedrooms')->toArray();
        $response = $this->actingAs($renter)->json('POST', route('property.store'), $data);
        $response->assertStatus(403)->assertJsonValidationErrors('bedrooms');
        $this->assertDatabaseCount('properties',0);
    }
    /** @test */
    public function a_property_bathrooms_required() {
        $renter = Renter::factory()->create();
        $data = $this->data()->except('bathrooms')->toArray();
        $response = $this->actingAs($renter)->json('POST', route('property.store'), $data);
        $response->assertStatus(403)->assertJsonValidationErrors('bathrooms');
        $this->assertDatabaseCount('properties',0);
    }
    /** @test */
    public function a_property_beds_required() {
        $renter = Renter::factory()->create();
        $data = $this->data()->except('beds')->toArray();
        $response = $this->actingAs($renter)->json('POST', route('property.store'), $data);
        $response->assertStatus(403)->assertJsonValidationErrors('beds');
        $this->assertDatabaseCount('properties',0);
    }

    /** @test */

    public function get_all_renter_properties()
    {
        $renter = Renter::factory()->create();
        $type = PropertyType::find(1)->first();
        $property = Property::factory()->create(['user_id' =>$renter->id, 'type_id' => $type->id]);
        $renter->properties()->save($property);
        $this->json('GET', route('user.properties.index', $renter->id))
            ->assertStatus(200)
            ->assertSee($property->title);
    }

    /** @test */

    private function data() {
        $picture = UploadedFile::fake()->image('pic.png');
        return collect([
            'title' => 'prop 1',
            'state' => 'Alger',
            'city' => 'Alger Centre',
            'street' => 'stade 20 aout',
            'price' => 5000,
            'type' => PropertyType::find(1)->first()->title,
            'bedrooms' => 3,
            'bathrooms' => 3,
            'beds' => 3,
            'description' => 'some text for description',
            'rooms' => 3,
            'type_of_place'=> TypeOfPlace::find(1)->first()->title
        ]);
    }
}
