<?php


namespace Tests\Feature\client;


use App\Models\Client;
use App\Models\Property;
use App\Models\PropertyType;
use App\Models\Rating;
use App\Models\Renter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientRatingsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_client_can_rate_property()
    {
        $this->withoutExceptionHandling();
        $renter = Renter::factory()->create();
        PropertyType::factory()->create();
        $property = Property::factory()->create(['user_id' => $renter->id]);
        $client = Client::factory()->create();
        $data = [
            'rating' => 4
        ];
        $this->actingAs($client)->json('post','/properties/'. $property->id . '/rating', $data)
            ->assertStatus(201);
        $this->assertDatabaseCount('ratings',1);

    }

    /** @test */
    public function a_client_rating_is_required()
    {
        $this->withoutExceptionHandling();
        $renter = Renter::factory()->create();
        PropertyType::factory()->create();
        $property = Property::factory()->create(['user_id' => $renter->id]);
        $client = Client::factory()->create();
        $data = [];
        $this->actingAs($client)->json('post','/properties/'. $property->id . '/rating', $data)
            ->assertStatus(403);
        $this->assertDatabaseCount('ratings',0);
    }
    /** @test */
    public function a_client_rating_must_be_between_1_or_5()
    {
        $this->withoutExceptionHandling();
        $renter = Renter::factory()->create();
        PropertyType::factory()->create();
        $property = Property::factory()->create(['user_id' => $renter->id]);
        $client = Client::factory()->create();
        $data = [
            'rating' => 0
        ];
        $this->actingAs($client)->json('post','/properties/'. $property->id . '/rating', $data)
            ->assertStatus(403);
        $this->assertDatabaseCount('ratings',0);
        $data = [
            'rating' => 6
        ];
        $this->actingAs($client)->json('post','/properties/'. $property->id . '/rating', $data)
            ->assertStatus(403);
        $this->assertDatabaseCount('ratings',0);
    }

    /** @test */
    public function a_client_can_remove_a_rating()
    {
        PropertyType::factory()->create();
        $renter = Renter::factory()->create();
        $property = Property::factory()->create(['user_id' => $renter->id]);
        $client = Client::factory()->create();
        $rating = Rating::factory()->create(['property_id' => $property->id]);
        $this->assertDatabaseCount('ratings',1);
        $this->actingAs($client)->json('delete','/properties/'. $property->id . '/rating/'. $rating->id)
            ->assertStatus(200);
        $this->assertDatabaseCount('ratings',0);
    }

    /** @test */
    public function a_renter_cannot_add_rating()
    {
        PropertyType::factory()->create();
        $renter = Renter::factory()->create();
        $property = Property::factory()->create(['user_id' => $renter->id]);
        $data = [
            'rating' => 2
        ];
        $client = Client::factory()->create();
        $this->actingAs($renter)->json('post','/properties/'. $property->id . '/rating/', $data)
            ->assertStatus(401);
        $this->assertDatabaseCount('ratings',0);
    }
    /** @test */
    public function a_visitor_cannot_add_rating()
    {
        PropertyType::factory()->create();
        $renter = Renter::factory()->create();
        $property = Property::factory()->create(['user_id' => $renter->id]);
        $data = [
            'rating' => 2
        ];
        $this->json('post','/properties/'. $property->id . '/rating/', $data)
            ->assertStatus(401);
        $this->assertDatabaseCount('ratings',0);
    }

}
