<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
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

    private function metaFile(): string
    {
        return $this->backupDir() . DIRECTORY_SEPARATOR . 'backups_meta.json';
    }

    private function getMeta(): array
    {
        $file = $this->metaFile();
        if (File::exists($file)) {
            $data = json_decode(File::get($file), true);
            return is_array($data) ? $data : [];
        }
        return [];
    }

    private function saveMeta(array $meta): void
    {
        File::put($this->metaFile(), json_encode($meta, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    public function index()
    {
        $dir = $this->backupDir();
        $meta = $this->getMeta();

        $files = collect(File::files($dir))
            ->filter(fn($f) => $f->getExtension() === 'sqlite')
            ->sortByDesc(fn($f) => $f->getMTime())
            ->map(function ($f) use ($meta) {
                $filename = $f->getFilename();
                $creator = $meta[$filename]['creator'] ?? 'سیستەم';
                return [
                    'name'    => $filename,
                    'size'    => round($f->getSize() / 1024, 1),
                    'date'    => date('Y-m-d H:i:s', $f->getMTime()),
                    'creator' => $creator,
                ];
            })->values();

        return view('admin.backups', ['backups' => $files]);
    }

    public function create()
    {
        $source = database_path('database.sqlite');

        if (!File::exists($source)) {
            return back()->with('error', 'فایلی سەرەکی داتابەیس نەدۆزرایەوە.');
        }

        $user = auth()->user();
        $name = 'backup_' . now()->format('Y-m-d_H-i-s') . '.sqlite';
        $dest = $this->backupDir() . DIRECTORY_SEPARATOR . $name;

        File::copy($source, $dest);

        // Record metadata
        $meta = $this->getMeta();
        $meta[$name] = [
            'creator' => $user->name,
            'user_id' => $user->id,
            'time'    => now()->toDateTimeString(),
        ];
        $this->saveMeta($meta);

        // Audit log
        AuditLog::create([
            'user_id'      => $user->id,
            'action'       => 'backup_created',
            'model_type'   => 'Backup',
            'performed_at' => now(),
            'details'      => ['filename' => $name, 'size_kb' => round(filesize($dest) / 1024, 1)],
        ]);

        return back()->with('success', 'باکئەپی نوێ بە سەرکەوتوویی دروستکرا.');
    }

    public function download(string $filename)
    {
        $safeName = basename($filename);
        abort_unless(preg_match('/^backup_[\d\-_]+\.sqlite$/', $safeName), 404);

        $path = $this->backupDir() . DIRECTORY_SEPARATOR . $safeName;
        abort_unless(File::exists($path), 404);

        AuditLog::create([
            'user_id'      => auth()->id(),
            'action'       => 'backup_downloaded',
            'model_type'   => 'Backup',
            'performed_at' => now(),
            'details'      => ['filename' => $safeName],
        ]);

        return response()->download($path);
    }

    public function restore(Request $request, string $filename)
    {
        $user = auth()->user();
        if (!$user->isAdmin()) {
            abort(403);
        }

        // Validate double-confirmation code
        $request->validate([
            'confirm_restore' => 'required|in:RESTORE',
        ], [
            'confirm_restore.in' => 'تکایە دەستەواژەی RESTORE بنووسە بۆ دڵنیابوونەوە.',
        ]);

        $safeName = basename($filename);
        abort_unless(preg_match('/^backup_[\d\-_]+\.sqlite$/', $safeName), 404);

        $path = $this->backupDir() . DIRECTORY_SEPARATOR . $safeName;
        abort_unless(File::exists($path), 404);

        $target = database_path('database.sqlite');

        // Make safety snapshot before restoring
        $safetyName = 'backup_pre_restore_' . now()->format('Y-m-d_H-i-s') . '.sqlite';
        if (File::exists($target)) {
            File::copy($target, $this->backupDir() . DIRECTORY_SEPARATOR . $safetyName);
        }

        File::copy($path, $target);

        AuditLog::create([
            'user_id'      => $user->id,
            'action'       => 'backup_restored',
            'model_type'   => 'Backup',
            'performed_at' => now(),
            'details'      => ['restored_from' => $safeName, 'safety_backup' => $safetyName],
        ]);

        return back()->with('success', 'داتابەیس بە سەرکەوتوویی گەڕێنرایەوە لە باکئەپی ' . $safeName);
    }

    public function destroy(string $filename)
    {
        $safeName = basename($filename);
        abort_unless(preg_match('/^backup_[\d\-_]+\.sqlite$/', $safeName), 404);

        $path = $this->backupDir() . DIRECTORY_SEPARATOR . $safeName;

        if (File::exists($path)) {
            File::delete($path);
        }

        $meta = $this->getMeta();
        unset($meta[$safeName]);
        $this->saveMeta($meta);

        AuditLog::create([
            'user_id'      => auth()->id(),
            'action'       => 'backup_deleted',
            'model_type'   => 'Backup',
            'performed_at' => now(),
            'details'      => ['filename' => $safeName],
        ]);

        return back()->with('success', 'باکئەپ بە سەرکەوتوویی سڕایەوە.');
    }
}
