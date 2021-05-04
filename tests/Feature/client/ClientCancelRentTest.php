<?php


namespace Tests\Feature\client;


use App\Models\Client;
use App\Models\Property;
use App\Models\Renter;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ClientCancelRentTest extends \Tests\TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_client_can_cancel_a_rent()
    {
        $this->withoutExceptionHandling();
        $renter = Renter::factory()->create();
        $client = Client::factory()->create();
        $property = Property::factory()->create(['user_id' => $renter->id]);
        $start_time = Carbon::now()->toDateString();
        $end_time = Carbon::now()->addDays(5)->toDateString();
        $client->properties()->attach($property->id, ['start_time' =>$start_time,'end_time' => $end_time]);
        $this->actingAs($client)->json('delete','/properties/'. $property->id . '/rent')
            ->assertStatus(200);
        $this->assertDatabaseCount('client_properties', 0);
    }

    /** @test */
    public function a_client_cannot_cancel_other_client_rent()
    {
        $this->withoutExceptionHandling();
        $renter = Renter::factory()->create();
        $client = Client::factory()->create();
        $client2 = Client::factory()->create();
        $property = Property::factory()->create(['user_id' => $renter->id]);
        $start_time = Carbon::now()->toDateString();
        $end_time = Carbon::now()->addDays(5)->toDateString();
        $client->properties()->attach($property->id, ['start_time' =>$start_time,'end_time' => $end_time]);
        $this->actingAs($client2)->json('delete','/properties/'. $property->id . '/rent')
            ->assertStatus(401);
        $this->assertDatabaseCount('client_properties', 1);
    }
}
