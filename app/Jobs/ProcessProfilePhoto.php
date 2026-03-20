<?php

namespace App\Jobs;

use App\Models\Photo;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;

class ProcessProfilePhoto
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public readonly Photo $photo)
    {
    }

    public function handle(): void
    {
        $originalPath = Storage::disk('public')->path($this->photo->path);

        if (! file_exists($originalPath)) {
            return;
        }

        // Derive thumbnail storage path (originals/ → thumbnails/)
        $thumbnailRelative = str_replace('/originals/', '/thumbnails/', $this->photo->path);
        $thumbnailAbsPath  = Storage::disk('public')->path($thumbnailRelative);

        // Ensure thumbnail directory exists
        @mkdir(dirname($thumbnailAbsPath), 0755, true);

        // Cover-crop to 400×400 for thumbnail
        Image::read($originalPath)
            ->cover(400, 400)
            ->save($thumbnailAbsPath);

        // Mark approved and save thumbnail path
        $this->photo->update([
            'thumbnail_path' => $thumbnailRelative,
            'is_approved'    => true,
        ]);
    }
}
