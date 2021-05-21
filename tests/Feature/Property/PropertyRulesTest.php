<?php


namespace Property;


use App\Models\Property;
use App\Models\PropertyType;
use App\Models\Renter;
use App\Models\Rule;
use Database\Factories\RuleFactory;
use Faker\Factory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PropertyRulesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function get_all_property_rules()
    {
        $this->withoutExceptionHandling();
        PropertyType::factory()->create();
        $renter = Renter::factory()->create();
        $property = Property::factory()->create(['user_id' => $renter->id]);
        $rule1 = Rule::factory()->create(['title' => 'rule 1']);
        $rule2 = Rule::factory()->create(['title' => 'rule 2']);
        $property->rules()->sync([$rule1->id,$rule2->id]);
        $response = $this->json('get', route('property.rules.index', $property->id));
        $response->assertStatus(200);
        $response->assertSee('rule 1');
        $response->assertSee('rule 2');
    }
    /** @test */
    public function add_new_property_rule()
    {
        $this->withoutExceptionHandling();
        $type = PropertyType::factory()->create();
        $renter = Renter::factory()->create();
        $property = Property::factory()->create(['user_id' => $renter->id, 'type_id' => $type->id]);
        $rule = Rule::factory()->create(['title' => 'rule 1']);
        $this->actingAs($renter)->json('post', route('property.rules.store', $property->id),[
            'id' => $rule->id,
        ])->assertStatus(201);
        $this->assertDatabaseHas('property_rules', ['rule_id' => $rule->id]);
    }
    /** @test */
    public function add_new_property_rule_doesnt_exist()
    {
        $this->withoutExceptionHandling();
        $type = PropertyType::factory()->create();
        $renter = Renter::factory()->create();
        $property = Property::factory()->create(['user_id' => $renter->id, 'type_id' => $type->id]);
        $rule = Rule::factory()->create(['title' => 'rule 1']);
        $this->actingAs($renter)->json('post', route('property.rules.index', $property->id),[
            'id' => 999,
        ])->assertStatus(403)
            ->assertJsonValidationErrors('id');
        $this->assertDatabaseCount('property_rules', 0);
    }

    /** @test */
    public function delete_rule_from_property()
    {
        $this->withoutExceptionHandling();
        $type = PropertyType::factory()->create();
        $renter = Renter::factory()->create();
        $rule = Rule::factory()->create(['title' => 'rule 1']);
        $property = Property::factory()->create(['user_id' => $renter->id, 'type_id' => $type->id]);
        $property->rules()->attach($rule->id);
        $this->actingAs($renter)->json('delete', route('property.rules.destroy',[$property->id, $rule->id]))
            ->assertStatus(200);
        $this->assertDatabaseCount('property_rules', 0);
    }
    /** @test */
    public function delete_unexisted_rule_from_property()
    {
        $type = PropertyType::factory()->create();
        $renter = Renter::factory()->create();
        $rule = Rule::factory()->create(['title' => 'rule 1']);
        $property = Property::factory()->create(['user_id' => $renter->id, 'type_id' => $type->id]);
        $property->rules()->attach($rule->id);
        $this->actingAs($renter)->json('delete', route('property.rules.destroy', [$property->id,999]))
            ->assertStatus(403);
        $this->assertDatabaseCount('property_rules', 1);
    }
}
