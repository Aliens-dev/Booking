<?php


namespace Renter;


use App\Models\Amenity;
use App\Models\Client;
use App\Models\Facility;
use App\Models\PropertyType;
use App\Models\Renter;
use App\Models\Rule;
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
        $this->seed(WilayaCommuneSeeder::class);
        PropertyType::factory()->count(5)->create();
        Rule::factory()->create();
        Facility::factory()->create();
        Amenity::factory()->create();
    }

    /** @test */
    public function a_renter_can_add_new_property()
    {
        $renter = Renter::factory()->create();
        $data = $this->data()->toArray();
        $response = $this->actingAs($renter)->json('POST', '/properties', $data);
        $response->assertStatus(201);
        $this->assertDatabaseCount('properties',1);
        $data = collect($data);
        $this->assertDatabaseHas('properties', $data->except('images','type','rules','facilities','amenities')->toArray());
    }

    /** @test */
    public function a_client_cannot_add_new_property()
    {
        $client = Client::factory()->create();
        $data = $this->data()->toArray();
        $response = $this->actingAs($client)->json('POST', '/properties', $data);
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
        $this->actingAs($renter)->post('/properties', $data)
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
        $this->actingAs($renter)->post('/properties', $data)
            ->assertStatus(403);
        $this->assertDatabaseCount('properties',0);
    }

    /** @test */
    public function a_property_title_max()
    {
        $renter = Renter::factory()->create();
        $data = $this->data()->toArray();
        $data['title'] = "lorem ipsum,lorem ipsum,lorem ipsum,lorem ipsum,lorem ipsum,lorem ipsum,lorem ipsum,lorem ipsum,lorem ipsum,lorem ipsum";
        $this->actingAs($renter)->post('/properties', $data)
            ->assertStatus(403);
        $this->assertDatabaseCount('properties',0);
    }

    /** @test */
    public function a_property_state_only_if_exists()
    {
        $renter = Renter::factory()->create();
        $data = $this->data()->toArray();
        $data['state'] = 'Aleraa';
        $response = $this->actingAs($renter)->json('POST', '/properties', $data);
        $response->assertStatus(403);
        $response->assertJsonValidationErrors('state');
        $this->assertDatabaseCount('properties',0);
    }

    /** @test */
    public function a_property_city_only_if_exists()
    {
        $renter = Renter::factory()->create();
        $data = $this->data()->toArray();
        $data['city'] = 'Alger Centress';
        $response = $this->actingAs($renter)->json('POST', '/properties', $data);
        $response->assertStatus(403);
        $response->assertJsonValidationErrors('city');
        $this->assertDatabaseCount('properties',0);
    }

    /** @test */
    public function a_property_street_is_required()
    {
        $renter = Renter::factory()->create();
        $data = $this->data()->except('street')->toArray();
        $response = $this->actingAs($renter)->json('POST', '/properties', $data);
        $response->assertStatus(403);
        $response->assertJsonValidationErrors('street');
        $this->assertDatabaseCount('properties',0);
    }
    /** @test */
    public function a_property_price_is_required()
    {
        $renter = Renter::factory()->create();
        $data = $this->data()->except('price')->toArray();
        $response = $this->actingAs($renter)->json('POST', '/properties', $data);
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
        $response = $this->actingAs($renter)->json('POST', '/properties', $data);
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
        $response = $this->actingAs($renter)->json('POST', '/properties', $data);
        $response->assertStatus(403);
        $response->assertJsonValidationErrors('price');
        $this->assertDatabaseCount('properties',0);
    }

    /** @test */
    public function a_property_rooms_are_required()
    {
        $renter = Renter::factory()->create();
        $data = $this->data()->except('rooms')->toArray();
        $response = $this->actingAs($renter)->json('POST', '/properties', $data);
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
        $response = $this->actingAs($renter)->json('POST', '/properties', $data);
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
        $response = $this->actingAs($renter)->json('POST', '/properties', $data);
        $response->assertStatus(403);
        $response->assertJsonValidationErrors('rooms');
        $this->assertDatabaseCount('properties',0);
    }

    /** @test */
    public function a_property_type_is_required()
    {
        $renter = Renter::factory()->create();
        $data = $this->data()->except('type')->toArray();
        $response = $this->actingAs($renter)->json('POST', '/properties', $data);
        $response->assertStatus(403);
        $response->assertJsonValidationErrors('type');
        $this->assertDatabaseCount('properties',0);
    }

    /** @test */
    public function a_property_commune_in_correct_wilaya()
    {
        $renter = Renter::factory()->create();
        $data = $this->data()->toArray();
        $data['city'] = 'Beni Aissi';
        $response = $this->actingAs($renter)->json('POST', '/properties', $data);
        $response->assertStatus(403);
        $this->assertDatabaseCount('properties',0);
    }
    /** @test */
    public function a_property_picture_is_uploaded_and_saved()
    {
        $renter = Renter::factory()->create();
        $data = $this->data()->toArray();
        $this->actingAs($renter)->post('/properties', $data)
            ->assertStatus(201);
        $this->assertDatabaseCount('images',1);
    }

    /*
     *
    public function a_property_picture_is_missing()
    {
        $renter = Renter::factory()->create();
        $data = $this->data()->except('images')->toArray();
        $this->actingAs($renter)->post('/properties', $data)
            ->assertStatus(403)
            ->assertJsonValidationErrors('images');
        $this->assertDatabaseCount('images',0);
        $this->assertDatabaseCount('properties', 0);
    }
    */

    /** @test */
    public function a_property_cannot_have_more_than_10_pictures()
    {
        $renter = Renter::factory()->create();
        Storage::fake('property');
        $pictures = [
            'image1' => UploadedFile::fake()->image('pic1.png'),
            'image2' => UploadedFile::fake()->image('pic2.png'),
            'image3' => UploadedFile::fake()->image('pic3.png'),
            'image4' => UploadedFile::fake()->image('pic4.png'),
            'image5' => UploadedFile::fake()->image('pic5.png'),
            'image6' => UploadedFile::fake()->image('pic6.png'),
            'image7' => UploadedFile::fake()->image('pic7.png'),
            'image8' => UploadedFile::fake()->image('pic8.png'),
            'image9' => UploadedFile::fake()->image('pic9.png'),
            'image10' => UploadedFile::fake()->image('pic10.png'),
            'image11' => UploadedFile::fake()->image('pic11.png'),
            'image12' => UploadedFile::fake()->image('pic12.png'),
        ];
        $data = $this->data()->toArray();
        $data['images'] = $pictures;
        $this->actingAs($renter)->post('/properties', $data)
            ->assertStatus(403)
            ->assertJsonValidationErrors('images');
        $this->assertDatabaseCount('images',0);
        $this->assertDatabaseCount('properties', 0);
    }

    /** @test */
    public function a_property_invalid_file_type()
    {
        $file = UploadedFile::fake()->create('document.pdf', 1024, 'application/pdf');
        $renter = Renter::factory()->create();
        $data = $this->data()->toArray();
        $data['images'][] = $file;
        $this->actingAs($renter)->post('/properties', $data)
            ->assertStatus(403)
            ->assertJsonValidationErrors('images.0');
        $this->assertDatabaseCount('images',0);
    }

    /** @test */
    public function a_property_invalid_picture_size()
    {
        Storage::fake('property');
        $file = UploadedFile::fake()->create('document.pdf', 1024, 'application/pdf');
        $renter = Renter::factory()->create();
        $this->seed(WilayaCommuneSeeder::class);
        $data = $this->data()->toArray();
        $data['images'][] = $file;
        $this->actingAs($renter)->post('/properties', $data)
            ->assertStatus(403)
            ->assertJsonValidationErrors('images.0');
        $this->assertDatabaseCount('images',0);
    }

    /** @test */
    public function a_property_bedrooms_required() {
        $renter = Renter::factory()->create();
        $data = $this->data()->except('bedrooms')->toArray();
        $response = $this->actingAs($renter)->json('POST', '/properties', $data);
        $response->assertStatus(403)->assertJsonValidationErrors('bedrooms');
        $this->assertDatabaseCount('properties',0);
    }
    /** @test */
    public function a_property_bathrooms_required() {
        $renter = Renter::factory()->create();
        $data = $this->data()->except('bathrooms')->toArray();
        $response = $this->actingAs($renter)->json('POST', '/properties', $data);
        $response->assertStatus(403)->assertJsonValidationErrors('bathrooms');
        $this->assertDatabaseCount('properties',0);
    }
    /** @test */
    public function a_property_beds_required() {
        $renter = Renter::factory()->create();
        $data = $this->data()->except('beds')->toArray();
        $response = $this->actingAs($renter)->json('POST', '/properties', $data);
        $response->assertStatus(403)->assertJsonValidationErrors('beds');
        $this->assertDatabaseCount('properties',0);
    }
    /** @test */
    public function a_property_rules_optional() {
        $this->withoutExceptionHandling();
        $renter = Renter::factory()->create();
        $data = $this->data()->except('rules')->toArray();
        $response = $this->actingAs($renter)->json('POST', '/properties', $data);
        $response->assertStatus(201);
        $this->assertDatabaseCount('properties',1);
    }

    /** @test */
    public function a_property_rule_must_exist() {
        $this->withoutExceptionHandling();
        $renter = Renter::factory()->create();
        $data = $this->data()->except('rules')->toArray();
        $data['rules'] = [
            'test'
        ];
        $response = $this->actingAs($renter)->json('POST', '/properties', $data);
        $response->assertStatus(403);
        $this->assertDatabaseCount('properties',0);
    }

    /** @test */
    public function a_property_facilities_added_to_db() {
        $this->withoutExceptionHandling();
        $renter = Renter::factory()->create();
        $data = $this->data()->toArray();
        $response = $this->actingAs($renter)->json('POST', '/properties', $data);
        $response->assertStatus(201);
        $this->assertDatabaseCount('properties',1);
        $this->assertDatabaseCount('facility_properties',1);
    }

    /** @test */
    public function a_property_facilities_optional() {
        $this->withoutExceptionHandling();
        $renter = Renter::factory()->create();
        $data = $this->data()->except('facilities')->toArray();
        $response = $this->actingAs($renter)->json('POST', '/properties', $data);
        $response->assertStatus(201);
        $this->assertDatabaseCount('properties',1);
    }

    /** @test */
    public function a_property_facility_must_exist() {
        $this->withoutExceptionHandling();
        $renter = Renter::factory()->create();
        $data = $this->data()->except('facilities')->toArray();
        $data['facilities'] = [
            'test'
        ];
        $response = $this->actingAs($renter)->json('POST', '/properties', $data);
        $response->assertStatus(403)
            ->assertJsonValidationErrors('facilities.0');
        $this->assertDatabaseCount('properties',0);
    }



    /** @test */
    public function a_property_amenities_added_to_db() {
        $this->withoutExceptionHandling();
        $renter = Renter::factory()->create();
        $data = $this->data()->toArray();
        $response = $this->actingAs($renter)->json('POST', '/properties', $data);
        $response->assertStatus(201);
        $this->assertDatabaseCount('properties',1);
        $this->assertDatabaseCount('amenity_properties',1);
    }

    /** @test */
    public function a_property_amenities_optional() {
        $this->withoutExceptionHandling();
        $renter = Renter::factory()->create();
        $data = $this->data()->except('amenities')->toArray();
        $response = $this->actingAs($renter)->json('POST', '/properties', $data);
        $response->assertStatus(201);
        $this->assertDatabaseCount('properties',1);
    }

    /** @test */
    public function a_property_amenity_must_exist() {
        $this->withoutExceptionHandling();
        $renter = Renter::factory()->create();
        $data = $this->data()->except('amenities')->toArray();
        $data['amenities'] = [
            'test'
        ];
        $response = $this->actingAs($renter)->json('POST', '/properties', $data);
        $response->assertStatus(403)
            ->assertJsonValidationErrors('amenities.0');
        $this->assertDatabaseCount('properties',0);
    }

    private function data() {
        $picture = UploadedFile::fake()->image('pic.png');
        return collect([
            'title' => 'prop 1',
            'state' => 'Alger',
            'city' => 'Alger Centre',
            'street' => 'stade 20 aout',
            'price' => 5000,
            'type' => 'house',
            'bedrooms' => 3,
            'bathrooms' => 3,
            'beds' => 3,
            'description' => 'some text for description',
            'rooms' => 3,
            'images' => [
                'image_1' => $picture,
            ],
            'rules' => [
                'PET_NOT_ALLOWED',
            ],
            'facilities' => [
                "FREE_PARKING",
            ],
            'amenities' => [
                "KITCHEN",
            ],
        ]);
    }
}
