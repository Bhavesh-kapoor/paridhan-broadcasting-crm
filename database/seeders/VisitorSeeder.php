<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class VisitorSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'id' => (string) Str::ulid(),
                'name' => 'Rahul Sharma',
                'phone' => '9876543210',
                'location' => 'Delhi',
                'type' => 'visitor',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => (string) Str::ulid(),
                'name' => 'Priya Singh',
                'phone' => '9123456780',
                'location' => 'Mumbai',
                'type' => 'visitor',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => (string) Str::ulid(),
                'name' => 'Amit Verma',
                'phone' => '9988776655',
                'location' => 'Bangalore',
                'type' => 'visitor',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => (string) Str::ulid(),
                'name' => 'Subhash Verma',
                'phone' => '9988776625',
                'location' => 'Delhi',
                'type' => 'visitor',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('contacts')->insert($data);
    }
}
