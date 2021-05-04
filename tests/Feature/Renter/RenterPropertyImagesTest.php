<?php


namespace Renter;


use App\Models\Property;
use App\Models\Renter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RenterPropertyImagesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_renter_can_update_property_images()
    {
        $this->withoutExceptionHandling();
        Storage::fake('property');
        $renter = Renter::factory()->create();
        $property = Property::factory()->create(['user_id' => $renter->id]);
        $property->images()->create(['url' => '1.jpg']);
        $property->images()->create(['url' => '2.jpg']);
        $picture = UploadedFile::fake()->image('pic.png');
        $response = $this->actingAs($renter)->json('patch', '/properties/'. $property->id . '/images', ['images' => [$picture]]);
        $response->assertStatus(200);
        $response->assertSee('success');
        $this->assertDatabaseCount('images', 1);
        $this->assertDatabaseMissing('images', ['url' => '1.jpg']);
        $this->assertDatabaseMissing('images', ['url' => '2.jpg']);
    }
    /** @test */
    public function a_renter_update_property_images_only_valid()
    {
        $this->withoutExceptionHandling();
        Storage::fake('property');
        $renter = Renter::factory()->create();
        $property = Property::factory()->create(['user_id' => $renter->id]);
        $property->images()->create(['url' => '1.jpg']);
        $property->images()->create(['url' => '2.jpg']);
        $response = $this->actingAs($renter)->json('patch', '/properties/'. $property->id . '/images', ['images' => []]);
        $response->assertStatus(403)->assertJsonValidationErrors('images');
        $this->assertDatabaseCount('images', 2);
        $this->assertDatabaseHas('images', ['url' => '1.jpg']);
        $this->assertDatabaseHas('images', ['url' => '2.jpg']);
    }
    /** @test */
    public function a_renter_can_delete_property_images()
    {
        $this->withoutExceptionHandling();
        Storage::fake('property');
        $renter = Renter::factory()->create();
        $property = Property::factory()->create(['user_id' => $renter->id]);
        $property->images()->create(['url' => '1.jpg']);
        $property->images()->create(['url' => '2.jpg']);
        $response = $this->actingAs($renter)->json('delete', '/properties/'. $property->id . '/images');
        $response->assertStatus(200);
        $response->assertSee('success');
        $this->assertDatabaseCount('images', 0);
        $this->assertDatabaseMissing('images', ['url' => '1.jpg']);
        $this->assertDatabaseMissing('images', ['url' => '2.jpg']);
    }
    /** @test */
    public function only_the_property_renter_can_update_property_images()
    {
        $this->withoutExceptionHandling();
        Storage::fake('property');
        $renter = Renter::factory()->create();
        $renter2 = Renter::factory()->create();
        $property = Property::factory()->create(['user_id' => $renter->id]);
        $property->images()->create(['url' => '1.jpg']);
        $property->images()->create(['url' => '2.jpg']);
        $picture = UploadedFile::fake()->image('pic.png');
        $response = $this->actingAs($renter2)->json('patch', '/properties/'. $property->id . '/images', ['images' => $picture]);
        $response->assertStatus(403);
        $this->assertDatabaseCount('images', 2);
        $this->assertDatabaseHas('images', ['url' => '1.jpg']);
        $this->assertDatabaseHas('images', ['url' => '2.jpg']);
    }

    /** @test */
    public function only_the_property_renter_can_delete_property_images()
    {
        $this->withoutExceptionHandling();
        Storage::fake('property');
        $renter = Renter::factory()->create();
        $renter2 = Renter::factory()->create();
        $property = Property::factory()->create(['user_id' => $renter->id]);
        $property->images()->create(['url' => '1.jpg']);
        $property->images()->create(['url' => '2.jpg']);
        $picture = UploadedFile::fake()->image('pic.png');
        $response = $this->actingAs($renter2)->json('delete', '/properties/'. $property->id . '/images');
        $response->assertStatus(403);
        $this->assertDatabaseCount('images', 2);
        $this->assertDatabaseHas('images', ['url' => '1.jpg']);
        $this->assertDatabaseHas('images', ['url' => '2.jpg']);
    }

    /** @test */
    public function get_all_property_images()
    {
        $this->withoutExceptionHandling();
        $renter = Renter::factory()->create();
        $property = Property::factory()->create(['user_id' => $renter->id]);
        $property->images()->create(['url' => '1.jpg']);
        $property->images()->create(['url' => '2.jpg']);
        $response = $this->actingAs($renter)->json('get', '/properties/'. $property->id . '/images');
        $response->assertStatus(200);
        $response->assertSee('success');
        $response->assertJsonCount(2);
    }
    /** @test */
    public function renter_can_delete_single_image()
    {
        $this->withoutExceptionHandling();
        $renter = Renter::factory()->create();
        $property = Property::factory()->create(['user_id' => $renter->id]);
        $property->images()->create(['url' => '1.jpg']);
        $property->images()->create(['url' => '2.jpg']);
        $response = $this->actingAs($renter)->json('delete', '/properties/'. $property->id . '/images/1');
        $response->assertStatus(200);
        $response->assertSee('success');
        $this->assertDatabaseCount('images', 1);
        $this->assertDatabaseMissing('images', ['url' => '1.jpg']);
    }
    /** @test */
    public function renter_cannot_delete_single_image_of_another_property()
    {
        $this->withoutExceptionHandling();
        $renter = Renter::factory()->create();
        $property = Property::factory()->create(['user_id' => $renter->id]);
        $property2 = Property::factory()->create(['user_id' => $renter->id]);
        $property->images()->create(['url' => '1.jpg']);
        $property2->images()->create(['url' => '2.jpg']);
        $response = $this->actingAs($renter)->json('delete', '/properties/'. $property2->id . '/images/1');
        $response->assertStatus(403);
        $response->assertSee('success');
        $this->assertDatabaseCount('images', 2);
        $this->assertDatabaseHas('images', ['url' => '1.jpg']);
        $this->assertDatabaseHas('images', ['url' => '2.jpg']);
    }
}
