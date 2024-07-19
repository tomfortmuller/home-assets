<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Asset;
use App\Models\Location;
use App\Models\Manufacturer;
use App\Models\Vendor;

class AssetFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Asset::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'model_number' => $this->faker->word(),
            'serial_number' => $this->faker->word(),
            'purchased_at' => $this->faker->date(),
            'purchase_price' => $this->faker->randomFloat(0, 0, 9999999999.),
            'location_id' => Location::factory(),
            'manufacturer_id' => Manufacturer::factory(),
            'vendor_id' => Vendor::factory(),
        ];
    }
}
