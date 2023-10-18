<?php

namespace Database\Factories\Crm\Contacts;

use App\Models\Crm\Contacts\LegalEntity;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Crm\Contacts\LegalEntity>
 */
class LegalEntityFactory extends Factory
{
    protected $model = LegalEntity::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name'  => $this->faker->company,
            'email' => $this->faker->unique()->companyEmail,
            'cnpj'  => $this->faker->unique()->numerify('##.###.###/####-##'),
            // ...
        ];
    }
}
