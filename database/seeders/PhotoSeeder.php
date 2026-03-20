<?php

namespace Database\Seeders;

use App\Models\Photo;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

/**
 * Downloads portrait placeholder images from randomuser.me and seeds them as
 * approved primary profile photos for every user that currently has no photos.
 *
 * Images are cached on disk so repeated runs don't re-download.
 * If the network is unavailable a graceful skip is printed per user.
 */
class PhotoSeeder extends Seeder
{
    // randomuser.me provides 100 portraits per gender (indices 0–99)
    private const MAX_INDEX = 99;

    public function run(): void
    {
        $dir = 'photos/seeded';
        Storage::disk('public')->makeDirectory($dir);

        $users = User::whereDoesntHave('photos')
            ->where('profile_complete', true)
            ->get();

        if ($users->isEmpty()) {
            $this->command->info('⚡ PhotoSeeder: all users already have photos — nothing to do.');
            return;
        }

        $maleIdx   = 0;
        $femaleIdx = 0;
        $seeded    = 0;
        $skipped   = 0;

        foreach ($users as $user) {
            $isFemale = in_array($user->gender, ['female', 'non_binary', 'other'], true);

            if ($isFemale) {
                $idx      = $femaleIdx % (self::MAX_INDEX + 1);
                $apiUrl   = "https://randomuser.me/api/portraits/women/{$idx}.jpg";
                $filename = "female_{$idx}.jpg";
                $femaleIdx++;
            } else {
                $idx      = $maleIdx % (self::MAX_INDEX + 1);
                $apiUrl   = "https://randomuser.me/api/portraits/men/{$idx}.jpg";
                $filename = "male_{$idx}.jpg";
                $maleIdx++;
            }

            $localPath = "{$dir}/{$filename}";
            $absPath   = storage_path("app/public/{$localPath}");

            // Download once and cache locally
            if (! file_exists($absPath)) {
                try {
                    $ctx = stream_context_create([
                        'http' => [
                            'timeout'       => 10,
                            'ignore_errors' => true,
                        ],
                    ]);
                    $img = @file_get_contents($apiUrl, false, $ctx);
                    if ($img === false || strlen($img) < 1000) {
                        $skipped++;
                        $this->command->warn("  ↳ Could not download portrait for user #{$user->id} ({$user->name}) — skipped.");
                        continue;
                    }
                    file_put_contents($absPath, $img);
                } catch (\Throwable $e) {
                    $skipped++;
                    $this->command->warn("  ↳ Download error for user #{$user->id}: {$e->getMessage()} — skipped.");
                    continue;
                }
            }

            // Create the Photo record
            Photo::create([
                'user_id'        => $user->id,
                'path'           => $localPath,
                'thumbnail_path' => $localPath,
                'is_primary'     => true,
                'is_approved'    => true,
                'sort_order'     => 1,
            ]);

            $seeded++;
        }

        $this->command->info("✅ PhotoSeeder: {$seeded} photos seeded, {$skipped} skipped.");
    }
}
