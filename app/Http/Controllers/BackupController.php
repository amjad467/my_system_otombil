<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BackupController extends Controller
{
    private function backupDir(): string
    {
        $dir = storage_path('app/backups');
        if (!File::exists($dir)) {
            File::makeDirectory($dir, 0755, true);
        }
        return $dir;
    }

    public function index()
    {
        $dir = $this->backupDir();
        $files = collect(File::files($dir))
            ->sortByDesc(fn($f) => $f->getMTime())
            ->map(fn($f) => [
                'name' => $f->getFilename(),
                'size' => round($f->getSize() / 1024, 1),
                'date' => date('Y-m-d H:i', $f->getMTime()),
            ])->values();

        return view('admin.backups', ['backups' => $files]);
    }

    public function create()
    {
        $source = database_path('database.sqlite');

        if (!File::exists($source)) {
            return back()->with('error', 'داتابەیس نەدۆزرایەوە.');
        }

        $name = 'backup_' . now()->format('Y-m-d_H-i-s') . '.sqlite';
        File::copy($source, $this->backupDir() . DIRECTORY_SEPARATOR . $name);

        return back()->with('success', 'باکئەپی نوێ بە سەرکەوتوویی دروستکرا.');
    }

    public function download(string $filename): StreamedResponse
    {
        $safeName = basename($filename);

        abort_unless(preg_match('/^backup_[\d\-_]+\.sqlite$/', $safeName), 404);

        $path = $this->backupDir() . DIRECTORY_SEPARATOR . $safeName;

        abort_unless(File::exists($path), 404);

        return response()->download($path);
    }

    public function destroy(string $filename)
    {
        $safeName = basename($filename);

        abort_unless(preg_match('/^backup_[\d\-_]+\.sqlite$/', $safeName), 404);

        $path = $this->backupDir() . DIRECTORY_SEPARATOR . $safeName;

        if (File::exists($path)) {
            File::delete($path);
        }

        return back()->with('success', 'باکئەپ سڕایەوە.');
    }
}
