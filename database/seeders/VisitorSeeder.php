<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class VisitorSeeder extends Seeder
{
    public function run()
    {
        $data = [];
        $now = Carbon::now();

        for ($i = 1; $i <= 200000; $i++) {
            $data[] = [
                'id' => (string) Str::ulid(),
                'name' => "User $i",
                'phone' => '9' . rand(100000000, 999999999),
                'location' => ['Delhi', 'Mumbai', 'Bangalore', 'Chennai', 'Kolkata'][array_rand(['Delhi', 'Mumbai', 'Bangalore', 'Chennai', 'Kolkata'])],
                'type' => 'visitor',
                'created_at' => $now,
                'updated_at' => $now,
            ];

            // Insert in chunks to avoid memory crash
            if ($i % 5000 == 0) {
                DB::table('contacts')->insert($data);
                $data = [];
            }
        }

        // Insert remaining records
        if (!empty($data)) {
            DB::table('contacts')->insert($data);
        }
    }
}
