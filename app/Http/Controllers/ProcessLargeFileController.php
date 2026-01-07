<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessLargeFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Validator;

class ProcessLargeFileController extends Controller
{
    public function upload(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:csv|max:102400', // max 100 MB
            'type' => 'required|in:exhibitor,visitor',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->all()
            ]);
        }

        $filePath = $request->file('file')->getRealPath();
        $fileContent = file_get_contents($filePath);

        $fileContent = mb_convert_encoding($fileContent, 'UTF-8', 'UTF-8');

        $fileLines = explode("\n", $fileContent);

        $fileLines = array_filter($fileLines, fn($line) => trim($line) !== '');

        $chunks = array_chunk($fileLines, 5000);

        $batch = Bus::batch([])->dispatch();

        foreach ($chunks as $key => $chunk) {
            $data = array_map('str_getcsv', $chunk);

            $data = array_filter($data, fn($row) => count(array_filter($row)) > 0);

            if ($key === 0) {
                $header = array_shift($data);
            }

            $batch->add(new ProcessLargeFile($header ?? [], $data, $request->type));
        }

        return response()->json([
            'status' => true,
            'message' => "File uploaded successfully. Processing started.",
        ]);
    }
}
