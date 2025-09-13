<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('borrowings', function (Blueprint $table) {
            // Store the chosen duration in hours (we validate 1–12 in code, but schema allows more if needed)
            $table->unsignedSmallInteger('duration_hours')->default(1)->after('purpose');
            $table->index('duration_hours');
        });
    }

    public function down(): void
    {
        Schema::table('borrowings', function (Blueprint $table) {
            $table->dropIndex(['duration_hours']);
            $table->dropColumn('duration_hours');
        });
    }
};
