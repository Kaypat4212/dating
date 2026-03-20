<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('photos', function (Blueprint $table) {
            $table->json('exif_data')->nullable()->after('is_approved');   // extracted GPS, device, etc.
            $table->boolean('has_gps')->default(false)->after('exif_data'); // quick flag for GPS presence
            $table->string('gps_lat', 30)->nullable()->after('has_gps');
            $table->string('gps_lng', 30)->nullable()->after('gps_lat');
            $table->string('camera_make')->nullable()->after('gps_lng');
            $table->string('camera_model')->nullable()->after('camera_make');
            $table->timestamp('photo_taken_at')->nullable()->after('camera_model');
        });
    }

    public function down(): void
    {
        Schema::table('photos', function (Blueprint $table) {
            $table->dropColumn(['exif_data','has_gps','gps_lat','gps_lng','camera_make','camera_model','photo_taken_at']);
        });
    }
};
