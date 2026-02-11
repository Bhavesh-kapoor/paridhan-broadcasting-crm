<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessLargeFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Validator;

class ProcessLargeFileController extends Controller
{
    // public function upload(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'file' => 'required|file|mimes:csv|max:102400', // max 100 MB
    //         'type' => 'required|in:exhibitor,visitor,employee',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => $validator->errors()->all()
    //         ]);
    //     }

    //     $filePath = $request->file('file')->getRealPath();
    //     $fileContent = file_get_contents($filePath);

    //     $fileContent = mb_convert_encoding($fileContent, 'UTF-8', 'UTF-8');

    //     $fileLines = explode("\n", $fileContent);

    //     $fileLines = array_filter($fileLines, fn($line) => trim($line) !== '');

    //     $chunks = array_chunk($fileLines, 5000);

    //     $batch = Bus::batch([])->dispatch();

    //     foreach ($chunks as $key => $chunk) {
    //         $data = array_map('str_getcsv', $chunk);

    //         $data = array_filter($data, fn($row) => count(array_filter($row)) > 0);

    //         if ($key === 0) {
    //             $header = array_shift($data);
    //         }

    //         $batch->add(new ProcessLargeFile($header ?? [], $data, $request->type));
    //     }

    //     return response()->json([
    //         'status' => true,
    //         'message' => "File uploaded successfully. Processing started.",
    //     ]);
    // }

//     public function upload(Request $request)
// {
//     $validator = Validator::make($request->all(), [
//         'file' => 'required|file|mimes:csv|max:102400',
//         'type' => 'required|in:exhibitor,visitor,employee',
//     ]);

//     if ($validator->fails()) {
//         return response()->json([
//             'status' => false,
//             'message' => $validator->errors()->all()
//         ]);
//     }

    
//     $filePath = $request->file('file')->getRealPath();

//     $fileContent = file_get_contents($filePath);
//     $fileContent = mb_convert_encoding($fileContent, 'UTF-8', 'UTF-8');

//     // Important fix for Windows CSV
//     $fileContent = str_replace("\r\n", "\n", $fileContent);

//     $fileLines = explode("\n", $fileContent);
//     $fileLines = array_filter($fileLines, fn($line) => trim($line) !== '');

//     // ✅ Header first remove karo
//     $header = str_getcsv(array_shift($fileLines));

//     // ✅ Ab chunk karo (header ke bina)
//     $chunks = array_chunk($fileLines, 5000);

//     $batch = Bus::batch([])->dispatch();

//     foreach ($chunks as $chunk) {

//         $data = array_map('str_getcsv', $chunk);

//         $data = array_filter($data, fn($row) => count(array_filter($row)) > 0);

//         $batch->add(new ProcessLargeFile($header, $data, $request->type));
//     }

//     return response()->json([
//         'status' => true,
//         'message' => "File uploaded successfully. Processing started.",
//     ]);
// }


public function upload(Request $request)
{
    $validator = Validator::make($request->all(), [
        'file' => 'required|file|mimes:csv|max:102400',
        'type' => 'required|in:exhibitor,visitor,employee',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => false,
            'message' => $validator->errors()->all()
        ]);
    }

    $file = fopen($request->file('file')->getRealPath(), 'r');

    // Read header
    $header = fgetcsv($file);

    $batchData = [];
    $chunkSize = 1000;

    while (($row = fgetcsv($file, 0, ",")) !== false) {

        if ($request->type === 'employee') {

            $email = trim($row[1] ?? '');

            if ($email === '') {
                continue;
            }

            $batchData[] = [
                'name'       => trim($row[0] ?? ''),
                'email'      => $email,
                'phone'      => preg_replace('/\s+/', '', $row[2] ?? ''),
                'position'   => trim($row[3] ?? ''),
                'salary'     => trim($row[4] ?? ''),
                'password'   => \Hash::make('12345678'),
                'role'       => 'employee',
                'status'     => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ];

            if (count($batchData) >= $chunkSize) {
                \App\Models\User::upsert(
                    $batchData,
                    ['email'],
                    ['name','phone','position','salary','role','status','updated_at']
                );
                $batchData = [];
            }

        } else {

            // Clean phone (remove spaces)
            $phone = preg_replace('/\s+/', '', $row[2] ?? '');

            if ($phone === '') {
                continue; // skip empty phone
            }

            $batchData[] = [
                'id'         => (string) \Str::ulid(),
                'name'       => trim($row[0] ?? ''),
                'email'      => trim($row[1] ?? ''),
                'phone'      => $phone,
                'location'   => trim($row[3] ?? ''),
                'state'      => trim($row[4] ?? ''),
                'city'       => trim($row[5] ?? ''),
                'type'       => $request->type,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            if (count($batchData) >= $chunkSize) {
                \App\Models\Contacts::upsert(
                    $batchData,
                    ['phone'],
                    ['name','email','location','state','city','type','updated_at']
                );
                $batchData = [];
            }
        }
    }

    // Insert remaining rows
    if (!empty($batchData)) {

        if ($request->type === 'employee') {
            \App\Models\User::upsert(
                $batchData,
                ['email'],
                ['name','phone','position','salary','role','status','updated_at']
            );
        } else {
           \App\Models\Contacts::upsert(
    $batchData,
    ['phone','type'], // composite unique
    ['name','email','location','state','city','updated_at']
);

        }
    }

    fclose($file);

    return response()->json([
        'status' => true,
        'message' => 'File imported successfully'
    ]);
}



}
