<?php

namespace Database\Factories;

use App\Models\Conference;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Attendee>
 */
class AttendeeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->safeEmail(),
            'ticket_cost' => $this->faker->randomElement([4000, 5000, 6000]),
            'is_paid' => $this->faker->randomElement([true, false]),
            'conference_id' => Conference::factory()
        ];
    }

    public function forConference(Conference $conference): self{
        return $this->state(function (array $attributes) use ($conference) {
            return [
                'conference_id' => $conference->id,
            ];
        });
    }
}
