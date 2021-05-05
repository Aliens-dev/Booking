<?php


namespace Renter;


use App\Models\Property;
use App\Models\PropertyType;
use App\Models\Renter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RenterPropertyDeleteTest extends TestCase
{
    use refreshDatabase;


    /** @test */
    public function a_renter_can_delete_his_property()
    {
        $this->withoutExceptionHandling();
        $renter=  Renter::factory()->create();
        PropertyType::factory()->create();
        $property = Property::factory()->create(['user_id' => $renter->id]);
        $this->actingAs($renter)->json('delete', '/properties/' . $property->id)
            ->assertStatus(200)
            ->assertSee('success');
        $this->assertDatabaseCount('properties', 0);
        $this->assertDatabaseMissing('properties', ['id' => 1]);
    }
    /** @test */
    public function only_the_property_renter_can_delete_his_property()
    {
        $this->withoutExceptionHandling();
        $renter=  Renter::factory()->create();
        $renter2=  Renter::factory()->create();
        PropertyType::factory()->create();
        $property = Property::factory()->create(['user_id' => $renter->id]);
        $this->actingAs($renter2)->json('delete', '/properties/' . $property->id)
            ->assertStatus(401)
            ->assertSee('success');
        $this->assertDatabaseCount('properties', 1);
        $this->assertDatabaseHas('properties', ['id' => 1]);
    }

    /** @test */
    public function an_anauthorized_user_cannot_delete_property()
    {
        $renter=  Renter::factory()->create();
        PropertyType::factory()->create();
        $property = Property::factory()->create(['user_id' => $renter->id]);
        $this->json('delete', '/properties/' . $property->id)
            ->assertStatus(401);
        $this->assertDatabaseCount('properties', 1);
        $this->assertDatabaseHas('properties', ['id' => 1]);
    }
}
