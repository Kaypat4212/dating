<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Clean up legacy gender/seeking values written by the old admin form
 * that used 'man'/'woman'/'men'/'women' instead of the ENUM values
 * 'male'/'female'/'everyone'.
 */
return new class extends Migration
{
    public function up(): void
    {
        // gender: 'man' → 'male', 'woman' → 'female'
        DB::table('users')->where('gender', 'man')->update(['gender' => 'male']);
        DB::table('users')->where('gender', 'woman')->update(['gender' => 'female']);
        DB::table('users')->where('gender', 'non-binary')->update(['gender' => 'non_binary']);

        // seeking: 'men' → 'male', 'women' → 'female'
        DB::table('users')->where('seeking', 'men')->update(['seeking' => 'male']);
        DB::table('users')->where('seeking', 'women')->update(['seeking' => 'female']);
    }

    public function down(): void
    {
        // Reversing is intentionally a no-op — old values were invalid ENUM entries.
    }
};
