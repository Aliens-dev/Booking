<?php

namespace Database\Factories;

use App\Models\Amenity;
use Illuminate\Database\Eloquent\Factories\Factory;

class AmenityFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Amenity::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' => 'PET_NOT_ALLOWED',
            'title_ar' => 'PET_NOT_ALLOWED',
            'description' => 'description',
            'description_ar' => 'description_ar',
        ];
    }
}
