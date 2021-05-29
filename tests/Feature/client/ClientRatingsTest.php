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
        $this->actingAs($client)->json('post',route('property.rating.store', $property->id), $data)
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
        $this->actingAs($client)->json('post',route('property.rating.store', $property->id), $data)
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
        $this->actingAs($client)->json('post',route('property.rating.store', $property->id), $data)
            ->assertStatus(403);
        $this->assertDatabaseCount('ratings',0);
        $data = [
            'rating' => 6
        ];
        $this->actingAs($client)->json('post',route('property.rating.store', $property->id), $data)
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
        $rating = $property->ratings()->create(['rating' => 1,'client_id' => $client->id]);
        $this->assertDatabaseCount('ratings',1);
        $this->actingAs($client)->json('delete',route('property.rating.destroy.single', [$property->id,$rating->id]))
            ->assertStatus(200);
        $this->assertDatabaseCount('ratings',0);
    }

    /** @test */
    public function a_client_can_update_a_rating()
    {
        $this->withoutExceptionHandling();
        PropertyType::factory()->create();
        $renter = Renter::factory()->create();
        $property = Property::factory()->create(['user_id' => $renter->id]);
        $client = Client::factory()->create();
        $rating = $property->ratings()->create(['rating' => 1,'client_id' => $client->id]);
        $this->assertDatabaseCount('ratings',1);
        $this->actingAs($client)->json('patch',route('property.rating.update', [$property->id,$rating->id]),[
            'rating' => 5
        ])
            ->assertStatus(200);
        $this->assertDatabaseCount('ratings',1);
        $this->assertDatabaseHas('ratings', ['rating' => 5]);
    }

    /** @test */
    public function a_client_cannot_update_another_client_rating()
    {
        $this->withoutExceptionHandling();
        PropertyType::factory()->create();
        $renter = Renter::factory()->create();
        $property = Property::factory()->create(['user_id' => $renter->id]);
        $client = Client::factory()->create();
        $client2 = Client::factory()->create();
        $rating = $property->ratings()->create(['rating' => 1,'client_id' => $client->id]);
        $this->assertDatabaseCount('ratings',1);
        $this->actingAs($client2)->json('patch',route('property.rating.update', [$property->id,$rating->id]),[
            'rating' => 5
        ])
            ->assertStatus(401);
        $this->assertDatabaseCount('ratings',1);
        $this->assertDatabaseHas('ratings', ['rating' => 1]);
    }

    /** @test */
    public function a_client_cannot_delete_another_client_rating()
    {
        $this->withoutExceptionHandling();
        PropertyType::factory()->create();
        $renter = Renter::factory()->create();
        $property = Property::factory()->create(['user_id' => $renter->id]);
        $client = Client::factory()->create();
        $client2 = Client::factory()->create();
        $rating = $property->ratings()->create(['rating' => 1,'client_id' => $client->id]);
        $this->assertDatabaseCount('ratings',1);
        $this->actingAs($client2)->json('patch',route('property.rating.destroy.single', [$property->id,$rating->id]))
            ->assertStatus(401);
        $this->assertDatabaseCount('ratings',1);
        $this->assertDatabaseHas('ratings', ['rating' => 1]);
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
        $this->actingAs($renter)->json('post',route('property.rating.store', $property->id), $data)
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
        $this->json('post',route('property.rating.store', $property->id), $data)
            ->assertStatus(401);
        $this->assertDatabaseCount('ratings',0);
    }

    /** @test */
    public function a_client_can_rate_a_renter()
    {
        $this->withoutExceptionHandling();
        $renter = Renter::factory()->create();
        $client = Client::factory()->create();
        $data = [
            'rating' => 4
        ];
        $this->actingAs($client)->json('post',route('user.rating.store', $renter->id), $data)
            ->assertStatus(201);
        $this->assertDatabaseCount('ratings',1);

    }

    /** @test */
    public function a_client_renter_rating_is_required()
    {
        $this->withoutExceptionHandling();
        $renter = Renter::factory()->create();
        $client = Client::factory()->create();
        $data = [];
        $this->actingAs($client)->json('post',route('user.rating.store', $renter->id), $data)
            ->assertStatus(403);
        $this->assertDatabaseCount('ratings',0);
    }
    /** @test */
    public function a_client_renter_rating_must_be_between_1_or_5()
    {
        $this->withoutExceptionHandling();
        $renter = Renter::factory()->create();
        $client = Client::factory()->create();
        $data = [
            'rating' => 0
        ];
        $this->actingAs($client)->json('post',route('user.rating.store', $renter->id), $data)
            ->assertStatus(403);
        $this->assertDatabaseCount('ratings',0);
        $data = [
            'rating' => 6
        ];
        $this->actingAs($client)->json('post',route('user.rating.store', $renter->id), $data)
            ->assertStatus(403);
        $this->assertDatabaseCount('ratings',0);
    }

    /** @test */
    public function a_client_can_remove_a_renter_rating()
    {
        PropertyType::factory()->create();
        $renter = Renter::factory()->create();
        $client = Client::factory()->create();
        $rating = $renter->ratings()->create(['rating' => 1,'client_id' => $client->id]);
        $this->assertDatabaseCount('ratings',1);
        $this->actingAs($client)->json('delete',route('user.rating.destroy.single', [$renter->id,$rating->id]))
            ->assertStatus(200);
        $this->assertDatabaseCount('ratings',0);
    }

    /** @test */
    public function a_client_can_update_a_renter_rating()
    {
        $this->withoutExceptionHandling();
        PropertyType::factory()->create();
        $renter = Renter::factory()->create();
        $client = Client::factory()->create();
        $rating = $renter->ratings()->create(['rating' => 1,'client_id' => $client->id]);
        $this->assertDatabaseCount('ratings',1);
        $this->actingAs($client)->json('patch',route('user.rating.update', [$renter->id,$rating->id]),[
            'rating' => 5
        ])
            ->assertStatus(200);
        $this->assertDatabaseCount('ratings',1);
        $this->assertDatabaseHas('ratings', ['rating' => 5]);
    }

    /** @test */
    public function a_renter_cannot_add_rating_for_renter()
    {
        PropertyType::factory()->create();
        $renter = Renter::factory()->create();
        $property = Property::factory()->create(['user_id' => $renter->id]);
        $data = [
            'rating' => 2
        ];
        $client = Client::factory()->create();
        $this->actingAs($renter)->json('post',route('user.rating.store', $property->id), $data)
            ->assertStatus(401);
        $this->assertDatabaseCount('ratings',0);
    }
    /** @test */
    public function a_visitor_cannot_add_rating_for_renter()
    {
        PropertyType::factory()->create();
        $renter = Renter::factory()->create();
        $property = Property::factory()->create(['user_id' => $renter->id]);
        $data = [
            'rating' => 2
        ];
        $this->json('post',route('user.rating.store', $property->id), $data)
            ->assertStatus(401);
        $this->assertDatabaseCount('ratings',0);
    }

    /** @test */
    public function a_client_cannot_update_another_client_renter_rating()
    {
        $this->withoutExceptionHandling();
        $renter = Renter::factory()->create();
        $client = Client::factory()->create();
        $client2 = Client::factory()->create();
        $rating = $renter->ratings()->create(['rating' => 1,'client_id' => $client->id]);
        $this->assertDatabaseCount('ratings',1);
        $this->actingAs($client2)->json('patch',route('user.rating.update', [$renter->id,$rating->id]),[
            'rating' => 5
        ])
            ->assertStatus(401);
        $this->assertDatabaseCount('ratings',1);
        $this->assertDatabaseHas('ratings', ['rating' => 1]);
    }

    /** @test */
    public function a_client_cannot_delete_another_client_renter_rating()
    {
        $this->withoutExceptionHandling();
        $renter = Renter::factory()->create();
        $client = Client::factory()->create();
        $client2 = Client::factory()->create();
        $rating = $renter->ratings()->create(['rating' => 1,'client_id' => $client->id]);
        $this->assertDatabaseCount('ratings',1);
        $this->actingAs($client2)->json('patch',route('user.rating.destroy.single', [$renter->id,$rating->id]))
            ->assertStatus(401);
        $this->assertDatabaseCount('ratings',1);
        $this->assertDatabaseHas('ratings', ['rating' => 1]);
    }


}
