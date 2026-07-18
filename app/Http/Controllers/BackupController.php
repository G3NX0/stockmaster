<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Artisan;
use Carbon\Carbon;

class BackupController extends Controller
{
    public function index()
    {
        $disk = Storage::disk(config('backup.backup.destination.disks')[0]);
        $files = $disk->files(config('backup.backup.name'));
        
        $backups = [];
        foreach ($files as $file) {
            if (substr($file, -4) == '.zip') {
                $backups[] = [
                    'file_path' => $file,
                    'file_name' => str_replace(config('backup.backup.name') . '/', '', $file),
                    'file_size' => round($disk->size($file) / 1024 / 1024, 2) . ' MB',
                    'last_modified' => Carbon::createFromTimestamp($disk->lastModified($file))->format('d M Y, H:i:s'),
                ];
            }
        }

        $backups = array_reverse($backups);

        return view('settings.backups.index', compact('backups'));
    }

    public function create()
    {
        try {
            // Run backup in background or immediately? For this app, immediate is okay if small.
            Artisan::call('backup:run --only-db');
            return back()->with('success', 'Database backup created successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Backup failed: ' . $e->getMessage());
        }
    }

    public function download($fileName)
    {
        $disk = Storage::disk(config('backup.backup.destination.disks')[0]);
        $filePath = config('backup.backup.name') . '/' . $fileName;

        if ($disk->exists($filePath)) {
            return $disk->download($filePath);
        }

        return back()->with('error', 'File not found.');
    }

    public function destroy($fileName)
    {
        $disk = Storage::disk(config('backup.backup.destination.disks')[0]);
        $filePath = config('backup.backup.name') . '/' . $fileName;

        if ($disk->exists($filePath)) {
            $disk->delete($filePath);
            return back()->with('success', 'Backup file deleted.');
        }

        return back()->with('error', 'File not found.');
    }
}
