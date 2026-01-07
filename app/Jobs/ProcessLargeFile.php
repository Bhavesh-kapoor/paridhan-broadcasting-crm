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
            $batchData = [];

            foreach ($this->data as $row) {
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

            // Close database connection to free up resources
            \Illuminate\Support\Facades\DB::disconnect();
        } catch (\Throwable $e) {
            Log::error("Error processing file: " . $e->getMessage());
            // Close connection even on error
            \Illuminate\Support\Facades\DB::disconnect();
        }
    }
}
