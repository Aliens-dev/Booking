<?php

namespace Database\Factories;

use App\Models\Facility;
use App\Models\PropertyType;
use App\Models\Rule;
use Illuminate\Database\Eloquent\Factories\Factory;

class FacilityFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Facility::class;

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
