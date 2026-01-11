<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class LogController extends Controller
{
    /**
     * Display logs index page
     */
    public function index()
    {
        $logFiles = $this->getAvailableLogFiles();
        return view('logs.index', compact('logFiles'));
    }

    /**
     * Display specific log file content
     */
    public function show(Request $request, $logFile)
    {
        $validFiles = $this->getAvailableLogFiles();
        
        // Validate log file
        if (!in_array($logFile, $validFiles)) {
            abort(404, 'Log file not found');
        }

        $filePath = storage_path('logs/' . $logFile);
        
        // Check if file exists
        if (!File::exists($filePath)) {
            abort(404, 'Log file does not exist');
        }

        // Get file size
        $fileSize = File::size($filePath);
        
        // Read log file (last 1000 lines for performance)
        $lines = $this->readLogFile($filePath, 1000);
        
        // Get full file content for download
        $fullContent = File::get($filePath);
        
        $logFiles = $this->getAvailableLogFiles();
        
        return view('logs.show', compact('logFile', 'lines', 'fileSize', 'logFiles', 'fullContent'));
    }

    /**
     * Get available log files
     */
    private function getAvailableLogFiles()
    {
        $logDirectory = storage_path('logs');
        $files = File::files($logDirectory);
        
        $logFiles = [];
        foreach ($files as $file) {
            $fileName = $file->getFilename();
            // Only include .log files and exclude .gitignore
            if (pathinfo($fileName, PATHINFO_EXTENSION) === 'log') {
                $logFiles[] = $fileName;
            }
        }
        
        // Sort by modification time (newest first)
        usort($logFiles, function($a, $b) use ($logDirectory) {
            $timeA = File::lastModified($logDirectory . '/' . $a);
            $timeB = File::lastModified($logDirectory . '/' . $b);
            return $timeB - $timeA;
        });
        
        return $logFiles;
    }

    /**
     * Read log file with line limit
     */
    private function readLogFile($filePath, $lines = 1000)
    {
        $content = File::get($filePath);
        $allLines = explode("\n", $content);
        
        // Get last N lines
        $lastLines = array_slice($allLines, -$lines);
        
        return array_reverse($lastLines); // Reverse to show newest first
    }

    /**
     * Download log file
     */
    public function download($logFile)
    {
        $validFiles = $this->getAvailableLogFiles();
        
        // Validate log file
        if (!in_array($logFile, $validFiles)) {
            abort(404, 'Log file not found');
        }

        $filePath = storage_path('logs/' . $logFile);
        
        // Check if file exists
        if (!File::exists($filePath)) {
            abort(404, 'Log file does not exist');
        }

        return response()->download($filePath);
    }

    /**
     * Clear log file
     */
    public function clear($logFile)
    {
        $validFiles = $this->getAvailableLogFiles();
        
        // Validate log file
        if (!in_array($logFile, $validFiles)) {
            return response()->json([
                'status' => false,
                'message' => 'Log file not found'
            ], 404);
        }

        $filePath = storage_path('logs/' . $logFile);
        
        // Check if file exists
        if (!File::exists($filePath)) {
            return response()->json([
                'status' => false,
                'message' => 'Log file does not exist'
            ], 404);
        }

        // Clear file content
        File::put($filePath, '');

        return response()->json([
            'status' => true,
            'message' => 'Log file cleared successfully'
        ]);
    }
}

