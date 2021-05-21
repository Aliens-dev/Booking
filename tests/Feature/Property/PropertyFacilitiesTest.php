<?php


namespace Property;


use App\Models\Facility;
use App\Models\Property;
use App\Models\PropertyType;
use App\Models\Renter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PropertyFacilitiesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function get_all_property_facilities()
    {
        $this->withoutExceptionHandling();
        PropertyType::factory()->create();
        $renter = Renter::factory()->create();
        $property = Property::factory()->create(['user_id' => $renter->id]);

        $facility1 = Facility::factory()->create(['title' => 'facility 1']);
        $facility2 = Facility::factory()->create(['title' => 'facility 2']);

        $property->facilities()->sync([$facility1->id,$facility2->id]);
        $response = $this->json('get', route('property.facilities.index', $property->id));
        $response->assertStatus(200);
        $response->assertSee('facility 1');
        $response->assertSee('facility 2');
    }
    /** @test */
    public function add_new_property_facility()
    {
        $this->withoutExceptionHandling();
        $type = PropertyType::factory()->create();
        $renter = Renter::factory()->create();
        $property = Property::factory()->create(['user_id' => $renter->id, 'type_id' => $type->id]);
        $facility = Facility::factory()->create(['title' => 'facility']);
        $this->actingAs($renter)->json('post', route('property.facilities.store', $property->id),[
            'id' => $facility->id,
        ])->assertStatus(201);
        $this->assertDatabaseHas('facility_properties', ['facility_id' => $facility->id]);
    }
    /** @test */
    public function add_new_property_facility_doesnt_exist()
    {
        $this->withoutExceptionHandling();
        $type = PropertyType::factory()->create();
        $renter = Renter::factory()->create();
        $property = Property::factory()->create(['user_id' => $renter->id, 'type_id' => $type->id]);
        $facility = Facility::factory()->create(['title' => 'facility']);
        $this->actingAs($renter)->json('post', route('property.facilities.store', $property->id),[
            'id' => 999,
        ])->assertStatus(403)
            ->assertJsonValidationErrors('id');
        $this->assertDatabaseCount('facility_properties', 0);
    }

    /** @test */
    public function delete_facility_from_property()
    {
        $this->withoutExceptionHandling();
        $type = PropertyType::factory()->create();
        $renter = Renter::factory()->create();
        $facility = Facility::factory()->create(['title' => 'facility']);
        $property = Property::factory()->create(['user_id' => $renter->id, 'type_id' => $type->id]);
        $property->facilities()->attach($facility->id);
        $this->actingAs($renter)->json('delete', route('property.facilities.destroy', [$property->id,$facility->id]))
            ->assertStatus(200);
        $this->assertDatabaseCount('facility_properties', 0);
    }
    /** @test */
    public function delete_unexisted_facility_from_property()
    {
        $type = PropertyType::factory()->create();
        $renter = Renter::factory()->create();
        $facility = Facility::factory()->create(['title' => 'facility']);
        $property = Property::factory()->create(['user_id' => $renter->id, 'type_id' => $type->id]);
        $property->facilities()->attach($facility->id);
        $this->actingAs($renter)->json('delete', route('property.facilities.destroy', [$property->id,999]))
            ->assertStatus(403);
        $this->assertDatabaseCount('facility_properties', 1);
    }
}
