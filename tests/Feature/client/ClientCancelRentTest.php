<?php


namespace Tests\Feature\client;


use App\Models\Client;
use App\Models\Property;
use App\Models\PropertyType;
use App\Models\Renter;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ClientCancelRentTest extends \Tests\TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->renter = Renter::factory()->create();
        $this->client = Client::factory()->create();
        PropertyType::factory()->create();
    }

    /** @test */
    public function a_client_can_cancel_a_rent()
    {
        $this->withoutExceptionHandling();
        $property = Property::factory()->create(['user_id' => $this->renter->id]);
        $start_time = Carbon::now()->toDateString();
        $end_time = Carbon::now()->addDays(5)->toDateString();
        $this->client->properties()->attach($property->id, ['start_time' =>$start_time,'end_time' => $end_time]);
        $this->actingAs($this->client)->json('delete',route('property.rent.destroy', $property->id))
            ->assertStatus(200);
        $this->assertDatabaseCount('reservations', 0);
    }

    /** @test */
    public function a_client_cannot_cancel_other_client_rent()
    {
        $this->withoutExceptionHandling();
        $client2 = Client::factory()->create();
        $property = Property::factory()->create(['user_id' => $this->renter->id]);
        $start_time = Carbon::now()->toDateString();
        $end_time = Carbon::now()->addDays(5)->toDateString();
        $this->client->properties()->attach($property->id, ['start_time' =>$start_time,'end_time' => $end_time]);
        $this->actingAs($client2)->json('delete',route('property.rent.destroy', $property->id))
            ->assertStatus(401);
        $this->assertDatabaseCount('reservations', 1);
    }
    /** @test */
    public function a_property_is_available_after_client_cancel_rent()
    {
        $this->withoutExceptionHandling();
        $property = Property::factory()->create(['user_id' => $this->renter->id]);
        $start_time = Carbon::now()->toDateString();
        $end_time = Carbon::now()->addDays(5)->toDateString();
        $property->status = 'pending';
        $property->save();
        $this->client->properties()->attach($property->id, ['start_time' =>$start_time,'end_time' => $end_time]);
        $this->actingAs($this->client)->json('delete',route('property.rent.destroy', $property->id))
            ->assertStatus(200);
        $this->assertDatabaseCount('reservations', 0);
        $this->assertDatabaseHas('properties', ['status' => 'available']);
    }
}
