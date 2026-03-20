<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_verifications', function (Blueprint $table) {
            $table->string('document_type')->nullable()->after('id_document_path'); // passport|national_id|drivers_license
            $table->string('document_number')->nullable()->after('document_type');
            $table->date('document_expiry')->nullable()->after('document_number');
            $table->string('nationality')->nullable()->after('document_expiry');
            $table->string('date_of_birth_on_doc')->nullable()->after('nationality');
            $table->enum('kyc_level', ['none','basic','full'])->default('none')->after('date_of_birth_on_doc');
        });
    }

    public function down(): void
    {
        Schema::table('user_verifications', function (Blueprint $table) {
            $table->dropColumn(['document_type','document_number','document_expiry','nationality','date_of_birth_on_doc','kyc_level']);
        });
    }
};
