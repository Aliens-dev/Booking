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
        $response = $this->json('get', '/properties/'. $property->id . '/rules');
        $response->assertStatus(200);
        $response->assertSee('rule 1');
        $response->assertSee('rule 2');
    }
}
