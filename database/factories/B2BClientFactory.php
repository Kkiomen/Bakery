<?php

namespace Database\Factories;

use App\Models\B2BClient;
use Illuminate\Database\Eloquent\Factories\Factory;

class B2BClientFactory extends Factory
{
    protected $model = B2BClient::class;

    public function definition(): array
    {
        return [
            'company_name' => $this->faker->company(),
            'nip' => $this->faker->numerify('##########'),
            'regon' => $this->faker->numerify('#########'),
            'email' => $this->faker->unique()->companyEmail(),
            'password' => bcrypt('password'),
            'address' => $this->faker->streetAddress(),
            'postal_code' => $this->faker->postcode(),
            'city' => $this->faker->city(),
            'phone' => $this->faker->phoneNumber(),
            'website' => $this->faker->optional()->url(),
            'contact_person' => $this->faker->name(),
            'contact_phone' => $this->faker->phoneNumber(),
            'contact_email' => $this->faker->email(),
            'business_type' => $this->faker->randomElement(['piekarnia', 'sklep', 'restauracja', 'hotel', 'catering']),
            'business_description' => $this->faker->optional()->paragraph(),
            'status' => $this->faker->randomElement(['active', 'inactive', 'pending']),
            'pricing_tier' => $this->faker->randomElement(['standard', 'bronze', 'silver', 'gold', 'platinum']),
            'credit_limit' => $this->faker->numberBetween(5000, 50000),
            'current_balance' => $this->faker->numberBetween(0, 10000),
            'notes' => $this->faker->optional()->paragraph(),
            'email_verified_at' => now(),
        ];
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
        ]);
    }

    public function standard(): static
    {
        return $this->state(fn (array $attributes) => [
            'pricing_tier' => 'standard',
        ]);
    }

    public function gold(): static
    {
        return $this->state(fn (array $attributes) => [
            'pricing_tier' => 'gold',
        ]);
    }

    public function platinum(): static
    {
        return $this->state(fn (array $attributes) => [
            'pricing_tier' => 'platinum',
        ]);
    }
}
