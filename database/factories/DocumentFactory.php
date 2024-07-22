<?php

namespace Database\Factories;

use App\Models\Asset;
use App\Models\Document;
use Illuminate\Database\Eloquent\Factories\Factory;

class DocumentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Document::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'filename' => $this->faker->word(),
            'path' => $this->faker->word(),
            'sort' => $this->faker->word(),
            'asset_id' => Asset::factory(),
        ];
    }
}
