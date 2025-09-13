<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('ip_assets', function (Blueprint $table) {
            $table->decimal('latitude', 10, 7)->nullable()->after('notes');
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            $table->string('address', 255)->nullable()->after('longitude');

            $table->index(['latitude', 'longitude'], 'ip_assets_lat_lng_idx');
        });
    }

    public function down(): void
    {
        Schema::table('ip_assets', function (Blueprint $table) {
            $table->dropIndex('ip_assets_lat_lng_idx');
            $table->dropColumn(['latitude', 'longitude', 'address']);
        });
    }
};
