<?php


namespace Renter;


use App\Models\Client;
use App\Models\Property;
use App\Models\PropertyType;
use App\Models\Renter;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RenterRentUpdateTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_renter_can_cancel_rent_of_client() {
        $this->withoutExceptionHandling();
        $renter = Renter::factory()->create();
        $client = Client::factory()->create();
        PropertyType::factory()->create();
        $property = Property::factory()->create(['user_id' => $renter->id]);
        $start_time = Carbon::now()->toDateString();
        $end_time = Carbon::now()->addDays(5)->toDateString();
        $client->properties()->attach($property->id, ['start_time' =>$start_time,'end_time' => $end_time]);
        $property->status = 'pending';
        $property->save();
        $this->actingAs($renter)->json('delete','/properties/'. $property->id . '/rent')
            ->assertStatus(200);
        $this->assertDatabaseCount('reservations', 0);
        $this->assertDatabaseHas('properties', ['status' => 'available']);
    }


    /** @test */
    public function a_renter_cannot_cancel_rent_of_others_properties() {

        $renter = Renter::factory()->create();
        $renter2 = Renter::factory()->create();
        $client = Client::factory()->create();
        PropertyType::factory()->create();
        $property = Property::factory()->create(['user_id' => $renter->id]);
        $start_time = Carbon::now()->toDateString();
        $end_time = Carbon::now()->addDays(5)->toDateString();
        $client->properties()->attach($property->id, ['start_time' =>$start_time,'end_time' => $end_time]);
        $this->actingAs($renter2)->json('delete','/properties/'. $property->id . '/rent')
            ->assertStatus(401);
        $this->assertDatabaseCount('reservations', 1);
    }
    /** @test */
    public function a_renter_can_approve_rent_of_client() {
        $this->withoutExceptionHandling();
        $renter = Renter::factory()->create();
        $client = Client::factory()->create();
        PropertyType::factory()->create();
        $property = Property::factory()->create(['user_id' => $renter->id]);
        $start_time = Carbon::now()->toDateString();
        $end_time = Carbon::now()->addDays(5)->toDateString();
        $client->properties()->attach($property->id, ['start_time' =>$start_time,'end_time' => $end_time]);
        $property->status = 'pending';
        $property->save();
        $this->actingAs($renter)->json('patch','/properties/'. $property->id . '/rent')
            ->assertStatus(200);
        $this->assertDatabaseCount('reservations', 1);
        $this->assertDatabaseHas('properties', ['status' => 'approved']);
    }
    /** @test */
    public function a_renter_cannot_approve_rent_of_client_on_others_properties() {
        $this->withoutExceptionHandling();
        $renter = Renter::factory()->create();
        $renter2 = Renter::factory()->create();
        $client = Client::factory()->create();
        PropertyType::factory()->create();
        $property = Property::factory()->create(['user_id' => $renter->id]);
        $start_time = Carbon::now()->toDateString();
        $end_time = Carbon::now()->addDays(5)->toDateString();
        $client->properties()->attach($property->id, ['start_time' =>$start_time,'end_time' => $end_time]);
        $property->status = 'pending';
        $property->save();
        $this->actingAs($renter2)->json('patch','/properties/'. $property->id . '/rent')
            ->assertStatus(401);
        $this->assertDatabaseCount('reservations', 1);
        $this->assertDatabaseHas('properties', ['status' => 'pending']);
    }
    /** @test */
    public function a_client_cannot_approve_rent() {
        $this->withoutExceptionHandling();
        $renter = Renter::factory()->create();
        $client = Client::factory()->create();
        PropertyType::factory()->create();
        $property = Property::factory()->create(['user_id' => $renter->id]);
        $start_time = Carbon::now()->toDateString();
        $end_time = Carbon::now()->addDays(5)->toDateString();
        $client->properties()->attach($property->id, ['start_time' =>$start_time,'end_time' => $end_time]);
        $property->status = 'pending';
        $property->save();
        $this->actingAs($client)->json('patch','/properties/'. $property->id . '/rent')
            ->assertStatus(401);
        $this->assertDatabaseCount('reservations', 1);
        $this->assertDatabaseHas('properties', ['status' => 'pending']);
    }
}
