<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Abhiram Chandramohan',
            'email' => 'admin@abhiram.co',
            'password' => bcrypt('KillerMiller123#@!'),
            'is_admin' => true,
        ]);

        $this->call(WorkingDaysSeeder::class);
        $this->call(TimeSlotsSeeder::class);
    }
}
