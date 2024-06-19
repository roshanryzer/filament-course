<?php

namespace Database\Seeders;

use App\Models\Conference;
use App\Models\Speaker;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Venue;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        User::create([
            'name'  => 'Roshan Shrestha' ,
            'email' => 'roshan@matter.city',
            'password'=> bcrypt('Roshan')
        ]);

        Speaker::factory(15)->withTalks(2)->create();
        Venue::factory(20)->create();
        Conference::factory(5)->create();

    }
}
