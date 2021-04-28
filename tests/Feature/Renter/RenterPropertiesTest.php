<?php


namespace Renter;


use App\Models\User;
use Database\Seeders\WilayaCommuneSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Kossa\AlgerianCities\Commune;
use Kossa\AlgerianCities\Wilaya;
use Tests\TestCase;

class RenterPropertiesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_renter_can_add_new_property() {
        $this->withoutExceptionHandling();
        Storage::fake('property');
        $picture = UploadedFile::fake()->image('pic.png');

        $renter = User::factory()->create(['user_role' => 'renter']);
        $this->seed(WilayaCommuneSeeder::class);
        $data = [
            'title' => 'prop 1',
            'state' => 'Alger',
            'city' => 'Alger Centre',
            'street' => 'stade 20 aout',
            'price' => 5000,
            'type' => 'house',
            'description' => 'some text for description',
            'rooms' => 3,
            'images' => [
                'image_1' => $picture,
            ]
        ];
        $response = $this->actingAs($renter)->json('POST', '/properties', $data);

        $response->assertStatus(201);
        $this->assertDatabaseCount('properties',1);
        $data = collect($data);
        $this->assertDatabaseHas('properties', $data->except('images')->toArray());
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
            'description' => 'some text for description',
            'rooms' => 3,
        ];

        $this->actingAs($renter)->post('/properties', $data)
            ->assertStatus(403)
            ->assertJsonValidationErrors('title');
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
            'description' => 'some text for description',
            'rooms' => 3,
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
            'description' => 'some text for description',
            'rooms' => 3,
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
            'description' => 'some text for description',
            'rooms' => 3,
        ];
        $response = $this->actingAs($renter)->json('POST', '/properties', $data);

        $response->assertStatus(403);
        $response->assertJsonValidationErrors('state');
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
            'description' => 'some text for description',
            'rooms' => 3,
        ];
        $response = $this->actingAs($renter)->json('POST', '/properties', $data);

        $response->assertStatus(403);
        $response->assertJsonValidationErrors('city');
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
            'description' => 'some text for description',
            'rooms' => 3,
        ];
        $response = $this->actingAs($renter)->json('POST', '/properties', $data);
        $response->assertStatus(403);
        $response->assertJsonValidationErrors('street');

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
            'description' => 'some text for description',
            'rooms' => 3,
        ];
        $response = $this->actingAs($renter)->json('POST', '/properties', $data);
        $response->assertStatus(403);
        $response->assertJsonValidationErrors('price');
        $this->assertDatabaseCount('properties',0);
    }
    /** @test */
    public function a_property_price_cannot_be_decimal() {
        $this->withoutExceptionHandling();

        $renter = User::factory()->create(['user_role' => 'renter']);
        $this->seed(WilayaCommuneSeeder::class);
        $data = [
            'title' => 'prop 1',
            'state' => 'Alger',
            'city' => 'Alger Centre',
            'street' => 'lorem ipsum',
            'type' => 'house',
            'description' => 'some text for description',
            'rooms' => 3,
            'price' => 500.5,
        ];
        $response = $this->actingAs($renter)->json('POST', '/properties', $data);
        $response->assertStatus(403);
        $response->assertJsonValidationErrors('price');
        $this->assertDatabaseCount('properties',0);
    }
    /** @test */
    public function a_property_price_cannot_be_zero() {
        $this->withoutExceptionHandling();

        $renter = User::factory()->create(['user_role' => 'renter']);
        $this->seed(WilayaCommuneSeeder::class);
        $data = [
            'title' => 'prop 1',
            'state' => 'Alger',
            'city' => 'Alger Centre',
            'street' => 'lorem ipsum',
            'type' => 'house',
            'description' => 'some text for description',
            'rooms' => 3,
            'price' => 0,
        ];
        $response = $this->actingAs($renter)->json('POST', '/properties', $data);
        $response->assertStatus(403);
        $response->assertJsonValidationErrors('price');
        $this->assertDatabaseCount('properties',0);
    }

    /** @test */
    public function a_property_rooms_are_required() {
        $this->withoutExceptionHandling();

        $renter = User::factory()->create(['user_role' => 'renter']);
        $this->seed(WilayaCommuneSeeder::class);
        $data = [
            'title' => 'prop 1',
            'state' => 'Alger',
            'city' => 'Alger Centre',
            'street' => 'lorem ipsum',
            'type' => 'house',
            'description' => 'some text for description',
        ];
        $response = $this->actingAs($renter)->json('POST', '/properties', $data);
        $response->assertStatus(403);
        $response->assertJsonValidationErrors('rooms');
        $this->assertDatabaseCount('properties',0);
    }
    /** @test */
    public function a_property_rooms_cannot_be_decimal() {
        $this->withoutExceptionHandling();

        $renter = User::factory()->create(['user_role' => 'renter']);
        $this->seed(WilayaCommuneSeeder::class);
        $data = [
            'title' => 'prop 1',
            'state' => 'Alger',
            'city' => 'Alger Centre',
            'street' => 'lorem ipsum',
            'type' => 'house',
            'rooms' => 3.3,
            'description' => 'some text for description',
        ];
        $response = $this->actingAs($renter)->json('POST', '/properties', $data);
        $response->assertStatus(403);
        $response->assertJsonValidationErrors('rooms');
        $this->assertDatabaseCount('properties',0);
    }

    /** @test */
    public function a_property_rooms_cannot_be_Zero() {
        $this->withoutExceptionHandling();

        $renter = User::factory()->create(['user_role' => 'renter']);
        $this->seed(WilayaCommuneSeeder::class);
        $data = [
            'title' => 'prop 1',
            'state' => 'Alger',
            'city' => 'Alger Centre',
            'street' => 'lorem ipsum',
            'type' => 'house',
            'rooms' => 0,
            'description' => 'some text for description',
        ];
        $response = $this->actingAs($renter)->json('POST', '/properties', $data);
        $response->assertStatus(403);
        $response->assertJsonValidationErrors('rooms');
        $this->assertDatabaseCount('properties',0);
    }

    /** @test */
    public function a_property_type_is_required() {
        $this->withoutExceptionHandling();

        $renter = User::factory()->create(['user_role' => 'renter']);
        $this->seed(WilayaCommuneSeeder::class);
        $data = [
            'title' => 'prop 1',
            'state' => 'Alger',
            'city' => 'Alger Centre',
            'street' => 'lorem ipsum',
            'price' => 5000,
            'description' => 'some text for description',
            'rooms' => 3,
        ];
        $response = $this->actingAs($renter)->json('POST', '/properties', $data);
        $response->assertStatus(403);
        $response->assertJsonValidationErrors('type');
        $this->assertDatabaseCount('properties',0);
    }

    /** @test */
    public function a_property_commune_in_correct_wilaya() {
        $this->withoutExceptionHandling();

        $renter = User::factory()->create(['user_role' => 'renter']);
        $this->seed(WilayaCommuneSeeder::class);
        $data = [
            'title' => 'prop 1',
            'state' => 'Alger',
            'city' => 'Beni Aissi',
            'street' => 'lorem ipsum',
            'price' => 5000,
            'type' => 'house',
            'description' => 'some text for description',
            'rooms' => 3,
        ];
        $response = $this->actingAs($renter)->json('POST', '/properties', $data);
        $response->assertStatus(403);
        $this->assertDatabaseCount('properties',0);
    }
    /** @test */
    public function a_property_picture_is_uploaded_and_saved() {

        $this->withoutExceptionHandling();
        Storage::fake('property');
        $picture = UploadedFile::fake()->image('pic.png');
        $renter = User::factory()->create(['user_role' => 'renter']);
        $this->seed(WilayaCommuneSeeder::class);
        $data = [
            'title' => 'prop 1',
            'state' => 'Alger',
            'city' => 'Alger Centre',
            'street' => 'lorem ipsum',
            'price' => 5000,
            'type' => 'house',
            'description' => 'some text for description',
            'rooms' => 3,
            'images' => [
                'image_1' => $picture,
            ]
        ];

        $this->actingAs($renter)->post('/properties', $data)
            ->assertStatus(201);
        $this->assertDatabaseCount('images',1);
    }

    /** @test */
    public function a_property_picture_is_missing() {
        $this->withoutExceptionHandling();
        $renter = User::factory()->create(['user_role' => 'renter']);
        $this->seed(WilayaCommuneSeeder::class);
        $data = [
            'title' => 'prop 1',
            'state' => 'Alger',
            'city' => 'Alger Centre',
            'street' => 'lorem ipsum',
            'price' => 5000,
            'type' => 'house',
            'description' => 'some text for description',
            'rooms' => 3,
        ];

        $this->actingAs($renter)->post('/properties', $data)
            ->assertStatus(403)
            ->assertJsonValidationErrors('images');
        $this->assertDatabaseCount('images',0);
        $this->assertDatabaseCount('properties', 0);
    }

    /** @test */
    public function a_property_cannot_have_more_than_10_pictures() {
        $this->withoutExceptionHandling();
        $renter = User::factory()->create(['user_role' => 'renter']);

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

        $this->seed(WilayaCommuneSeeder::class);
        $data = [
            'title' => 'prop 1',
            'state' => 'Alger',
            'city' => 'Alger Centre',
            'street' => 'lorem ipsum',
            'price' => 5000,
            'type' => 'house',
            'description' => 'some text for description',
            'rooms' => 3,
            'images' => $pictures
        ];

        $this->actingAs($renter)->post('/properties', $data)
            ->assertStatus(403)
            ->assertJsonValidationErrors('images');
        $this->assertDatabaseCount('images',0);
        $this->assertDatabaseCount('properties', 0);
    }

    /** @test */
    public function a_property_invalid_file_type() {

        $this->withoutExceptionHandling();
        Storage::fake('property');
        $file = UploadedFile::fake()->create('document.pdf', 1024, 'application/pdf');
        $renter = User::factory()->create(['user_role' => 'renter']);
        $this->seed(WilayaCommuneSeeder::class);
        $data = [
            'title' => 'prop 1',
            'state' => 'Alger',
            'city' => 'Alger Centre',
            'street' => 'lorem ipsum',
            'price' => 5000,
            'type' => 'house',
            'description' => 'some text for description',
            'rooms' => 3,
            'images' => [
                'image_1' => $file,
            ]
        ];

        $this->actingAs($renter)->post('/properties', $data)
            ->assertStatus(403)
            ->assertJsonValidationErrors('images.image_1');
        $this->assertDatabaseCount('images',0);
    }

    /** @test */
    public function a_property_invalid_picture_size() {

        $this->withoutExceptionHandling();
        Storage::fake('property');
        $file = UploadedFile::fake()->create('document.pdf', 1024, 'application/pdf');
        $renter = User::factory()->create(['user_role' => 'renter']);
        $this->seed(WilayaCommuneSeeder::class);
        $data = [
            'title' => 'prop 1',
            'state' => 'Alger',
            'city' => 'Alger Centre',
            'street' => 'lorem ipsum',
            'price' => 5000,
            'type' => 'house',
            'description' => 'some text for description',
            'rooms' => 3,
            'images' => [
                'image_1' => $file,
            ]
        ];

        $this->actingAs($renter)->post('/properties', $data)
            ->assertStatus(403)
            ->assertJsonValidationErrors('images.image_1');
        $this->assertDatabaseCount('images',0);
    }
}
