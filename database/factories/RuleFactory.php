<?php

namespace Database\Factories;

use App\Models\PropertyType;
use App\Models\Rule;
use Illuminate\Database\Eloquent\Factories\Factory;

class RuleFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Rule::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' => 'PET_NOT_ALLOWED',
            'title_fr' => 'PET_NOT_ALLOWED',
            'description' => 'description',
            'description_fr' => 'description_ar',
        ];
    }
}
