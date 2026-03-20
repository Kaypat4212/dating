<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('email_templates', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();          // e.g. "welcome"
            $table->string('name');                   // Display name in admin
            $table->string('subject');                // Subject with {placeholders}
            $table->longText('body');                 // HTML body with {placeholders}
            $table->json('variables')->nullable();    // Available variable names (for hint display)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_templates');
    }
};
