<?php

namespace Database\Factories;

use App\Models\Property;
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
            'price' => rand(150,10000),
            'type' => 'House',
            'rooms' => rand(1,5),
        ];
    }
}
