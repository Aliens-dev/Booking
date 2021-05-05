<?php

namespace Database\Factories;

use App\Models\Property;
use App\Models\PropertyType;
use Illuminate\Database\Eloquent\Factories\Factory;

class PropertyFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Property::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' => $this->faker->text(70),
            'state' => 'Alger',
            'city' => 'Alger Centre',
            'street' => $this->faker->streetAddress,
            'description' => $this->faker->text(150),
            'price' => rand(250,10000),
            'type_id' => 1,
            'bedrooms' => rand(1,10),
            'bathrooms' => rand(1,10),
            'beds' => rand(1,10),
            'rooms' => rand(1,5),
        ];
    }
}
