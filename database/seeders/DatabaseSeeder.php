<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Paridhan',
            'email' => 'paridhan@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'admin'
        ]);
        User::factory()->create([
            'name' => 'Employee',
            'email' => 'employee@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'employee'
        ]);

        $this->call(VisitorSeeder::class);
    }
}
