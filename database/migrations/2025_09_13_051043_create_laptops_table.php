<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('laptops', function (Blueprint $table) {
            $table->id();
            $table->string('device_name');                 // e.g. "Dell Latitude 5410"
            $table->string('image_path')->nullable();      // storage path to uploaded image
            $table->enum('status', ['available','reserved','out','maintenance'])
                  ->default('available')
                  ->index();
            $table->text('notes')->nullable();
            $table->timestamps();

            // Optional: ensure device names are unique in your org
            // $table->unique('device_name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laptops');
    }
};
