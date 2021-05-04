<?php


namespace Tests\Feature\client;


use App\Models\Client;
use App\Models\Property;
use App\Models\Renter;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientRentTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_client_can_rent_a_property()
    {
        $this->withoutExceptionHandling();
        $renter = Renter::factory()->create();
        $client = Client::factory()->create();
        $data = [
            'start_time' => Carbon::now()->toDateString(),
            'end_time' => Carbon::now()->addDays(5)->toDateString(),
        ];
        $property = Property::factory()->create(['user_id' => $renter->id]);

        $this->actingAs($client)->json('post','/properties/'. $property->id . '/rent', $data)
            ->assertStatus(200);

        $this->assertDatabaseCount('client_properties', 1);
        $this->assertDatabaseHas('client_properties',
            [
                'client_id' => $client->id,
                'property_id' => $property->id,
                'start_time' => $data['start_time'],
                'end_time' => $data['end_time'],
            ]);
    }

    /** @test */
    public function a_start_time_is_required()
    {
        $this->withoutExceptionHandling();
        $renter = Renter::factory()->create();
        $client = Client::factory()->create();
        $data = [
            'end_time' => Carbon::now()->addDays(2)->toDateString(),
        ];
        $property = Property::factory()->create(['user_id' => $renter->id]);

        $this->actingAs($client)->json('post','/properties/'. $property->id . '/rent', $data)
            ->assertStatus(403);
        $this->assertDatabaseCount('client_properties', 0);
    }
    /** @test */
    public function an_end_time_is_required()
    {
        $this->withoutExceptionHandling();
        $renter = Renter::factory()->create();
        $client = Client::factory()->create();
        $data = [
            'start_time' => Carbon::now()->toDateString(),
        ];
        $property = Property::factory()->create(['user_id' => $renter->id]);

        $this->actingAs($client)->json('post','/properties/'. $property->id . '/rent', $data)
            ->assertStatus(403);
        $this->assertDatabaseCount('client_properties', 0);
    }

    /** @test */
    public function an_start_time_cannot_be_before_today()
    {
        $this->withoutExceptionHandling();
        $renter = Renter::factory()->create();
        $client = Client::factory()->create();
        $data = [
            'start_time' => Carbon::now()->subDays(1)->toDateString(),
            'end_time' => Carbon::now()->addDays(2)->toDateString(),
        ];
        $property = Property::factory()->create(['user_id' => $renter->id]);

        $this->actingAs($client)->json('post','/properties/'. $property->id . '/rent', $data)
            ->assertStatus(403);
        $this->assertDatabaseCount('client_properties', 0);
    }
    /** @test */
    public function an_end_time_cannot_be_before_today()
    {
        $this->withoutExceptionHandling();
        $renter = Renter::factory()->create();
        $client = Client::factory()->create();
        $data = [
            'start_time' => Carbon::now()->toDateString(),
            'end_time' => Carbon::now()->subDays(2)->toDateString(),
        ];
        $property = Property::factory()->create(['user_id' => $renter->id]);

        $this->actingAs($client)->json('post','/properties/'. $property->id . '/rent', $data)
            ->assertStatus(403);
        $this->assertDatabaseCount('client_properties', 0);
    }
    /** @test */
    public function an_end_time_cannot_be_before_or_equals_to_start_time()
    {
        $this->withoutExceptionHandling();
        $renter = Renter::factory()->create();
        $client = Client::factory()->create();
        $data = [
            'start_time' => Carbon::now()->toDateString(),
            'end_time' => Carbon::now()->subDays(2)->toDateString(),
        ];
        $property = Property::factory()->create(['user_id' => $renter->id]);

        $this->actingAs($client)->json('post','/properties/'. $property->id . '/rent', $data)
            ->assertStatus(403);
        $this->assertDatabaseCount('client_properties', 0);
    }

}
