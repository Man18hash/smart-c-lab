<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('ip_assets', function (Blueprint $table) {
            $table->string('place_label', 191)->nullable()->after('notes'); // e.g. "Room 301, Engineering"
            $table->decimal('lat', 10, 7)->nullable()->after('place_label');
            $table->decimal('lng', 10, 7)->nullable()->after('lat');
            $table->index(['lat','lng']);
        });
    }

    public function down(): void
    {
        Schema::table('ip_assets', function (Blueprint $table) {
            $table->dropIndex(['lat','lng']);
            $table->dropColumn(['place_label','lat','lng']);
        });
    }
};
