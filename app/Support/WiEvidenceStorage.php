<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class WiEvidenceStorage
{
    public static function storeItemDiscrepancy(UploadedFile $file, int $workInstructionId): string
    {
        return $file->store("wi-evidence/{$workInstructionId}/items", 'public');
    }

    public static function storeCompletion(UploadedFile $file, int $workInstructionId): string
    {
        return $file->store("wi-evidence/{$workInstructionId}", 'public');
    }

    public static function deleteIfExists(?string $path): void
    {
        if ($path !== null && $path !== '' && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    public static function publicUrl(?string $path): ?string
    {
        if ($path === null || $path === '') {
            return null;
        }

        // Use a relative URL so the browser always requests the current host/port.
        // This avoids 404s when APP_URL differs from the host/port used to serve the app.
        return '/storage/' . ltrim($path, '/');
    }
}
