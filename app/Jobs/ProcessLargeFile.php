<?php

namespace App\Jobs;

use App\Models\Contacts;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ProcessLargeFile implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Batchable;

    protected $header;
    protected $data;
    protected $type;

    public function __construct($header, $data, $type)
    {
        $this->header = $header;
        $this->data = $data;
        $this->type = $type;
    }

    public function handle(): void
    {
        try {
            if ($this->type === 'employee') {
                $batchData = [];

                foreach ($this->data as $row) {
                    $batchData[] = [
                        'name'          => $row[0] ?? null,
                        'email'         => $row[1] ?? null,
                        'phone'         => $row[2] ?? null,
                        'position'      => $row[3] ?? null, // Assuming position is 4th column
                        'salary'        => $row[4] ?? null, // Assuming salary is 5th column
                        'password'      => '$2y$12$K1H1c.H3H3H3H3H3H3H3H3H3H3H3H3H3H3H3H3H3H3H3H3H3H3H3', // Default: password (needs Hash::make in real app, but for upsert used raw hash or handle in loop)
                        // Better to use a loop for create/update if hashing needed, but upsert is faster.
                        // Let's use a fixed hash for "password" for now: $2y$12$K7iSLAoQ.0.0.0.0.0.0.0.0.0.0.0.0.0.0.0.0.0.0.0.0.0 (example)
                        // Actually, let's just use a known hash for "12345678" or similar.
                        // Hash::make('12345678') -> $2y$12$r.3.3.3.3.3.3.3.3.3.3.3.3.3.3.3.3.3.3.3.3.3.3.3.3
                        'password'      => \Illuminate\Support\Facades\Hash::make('12345678'), 
                        'role'          => 'employee',
                        'status'        => 'active',
                        'created_at'    => now(),
                        'updated_at'    => now(),
                    ];
                }

                // Filter out rows without email
                $batchData = array_filter($batchData, fn($row) => !empty($row['email']));

                if (!empty($batchData)) {
                    \App\Models\User::upsert(
                        $batchData,
                        ['email'], // Unique key for employees
                        ['name', 'phone', 'position', 'salary', 'role', 'status', 'updated_at']
                    );
                }

                return;
            }

            // Existing logic for Contacts (Exhibitor/Visitor)
            $batchData = [];

            foreach ($this->data as $row) {
                // ... existing logic ...
                $batchData[] = [
                    'id'         => (string) Str::ulid(),
                    'name'       => $row[0] ?? null,
                    'email'      => $row[1] ?? null,
                    'phone'      => $row[2] ?? null,
                    'location'   => $row[3] ?? null,
                    'state'      => $row[4] ?? null,
                    'city'       => $row[5] ?? null,
                    'type'       => $this->type,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            Contacts::upsert(
                $batchData,
                ['phone'], // unique key to detect existing records
                ['name', 'email', 'location', 'state', 'city', 'type', 'updated_at'] // columns to update
            );
        } catch (\Throwable $e) {
            Log::error("Error processing file: " . $e->getMessage());
        }
    }
}
