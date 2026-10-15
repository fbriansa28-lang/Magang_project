<?php

namespace App\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

trait HandlesMediaUploads
{
    protected function storeImage(UploadedFile $file, string $folder): string
    {
        return $file->store($folder, 'public');
    }

    protected function storeAttachment(UploadedFile $file, string $folder): string
    {
        return $file->store($folder, 'public');
    }

    protected function deleteMedia(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}