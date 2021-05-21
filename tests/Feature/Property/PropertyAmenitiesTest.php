<?php


namespace Property;


use App\Models\Amenity;
use App\Models\Property;
use App\Models\PropertyType;
use App\Models\Renter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PropertyAmenitiesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function get_all_property_amenities()
    {
        $this->withoutExceptionHandling();
        PropertyType::factory()->create();
        $renter = Renter::factory()->create();
        $property = Property::factory()->create(['user_id' => $renter->id]);

        $amenity1 = Amenity::factory()->create(['title' => 'Amenity 1']);
        $amenity2 = Amenity::factory()->create(['title' => 'Amenity 2']);

        $property->amenities()->sync([$amenity1->id,$amenity2->id]);
        $response = $this->json('get', route('property.amenities.index', $property->id));
        $response->assertStatus(200);
        $response->assertSee('Amenity 1');
        $response->assertSee('Amenity 2');
    }
    /** @test */
    public function add_new_property_amenity()
    {
        $this->withoutExceptionHandling();
        $type = PropertyType::factory()->create();
        $renter = Renter::factory()->create();
        $property = Property::factory()->create(['user_id' => $renter->id, 'type_id' => $type->id]);
        $amenity = Amenity::factory()->create(['title' => 'Amenity']);
        $this->actingAs($renter)->json('post', route('property.amenities.store',$property->id),[
            'id' => $amenity->id,
        ])->assertStatus(201);
        $this->assertDatabaseHas('amenity_properties', ['Amenity_id' => $amenity->id]);
    }
    /** @test */
    public function add_new_property_amenity_doesnt_exist()
    {
        $this->withoutExceptionHandling();
        $type = PropertyType::factory()->create();
        $renter = Renter::factory()->create();
        $property = Property::factory()->create(['user_id' => $renter->id, 'type_id' => $type->id]);
        $amenity = Amenity::factory()->create(['title' => 'Amenity']);
        $this->actingAs($renter)->json('post', route('property.amenities.store',$property->id),[
            'id' => 999,
        ])->assertStatus(403)
            ->assertJsonValidationErrors('id');
        $this->assertDatabaseCount('amenity_properties', 0);
    }

    /** @test */
    public function delete_amenity_from_property()
    {
        $this->withoutExceptionHandling();
        $type = PropertyType::factory()->create();
        $renter = Renter::factory()->create();
        $amenity = Amenity::factory()->create(['title' => 'Amenity']);
        $property = Property::factory()->create(['user_id' => $renter->id, 'type_id' => $type->id]);
        $property->amenities()->attach($amenity->id);
        $this->actingAs($renter)->json('delete', route('property.amenities.destroy', [$property->id, $amenity->id]))
            ->assertStatus(200);
        $this->assertDatabaseCount('amenity_properties', 0);
    }
    /** @test */
    public function delete_unexisted_amenity_from_property()
    {
        $type = PropertyType::factory()->create();
        $renter = Renter::factory()->create();
        $amenity = Amenity::factory()->create(['title' => 'Amenity']);
        $property = Property::factory()->create(['user_id' => $renter->id, 'type_id' => $type->id]);
        $property->amenities()->attach($amenity->id);
        $this->actingAs($renter)->json('delete', route('property.amenities.destroy', [$property->id, 999]))
            ->assertStatus(403);
        $this->assertDatabaseCount('amenity_properties', 1);
    }
}
