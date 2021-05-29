<?php

namespace Database\Factories;

use App\Models\Admin;
use App\Models\TypeOfPlace;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class TypeOfPlaceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = TypeOfPlace::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' => $this->faker->name,
            'title_fr' => $this->faker->name,
            'description' => $this->faker->name,
            'description_fr' => $this->faker->name,
        ];
    }
}
