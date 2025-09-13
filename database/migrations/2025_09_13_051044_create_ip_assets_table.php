<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ip_assets', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120);
            $table->string('ip_address', 45)->unique(); // IPv4/IPv6 safe
            $table->enum('status', ['free', 'assigned', 'blocked'])->default('free');
            $table->string('notes', 255)->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ip_assets');
    }
};
