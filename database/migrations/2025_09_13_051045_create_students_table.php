<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('full_name', 120);
            $table->string('grade', 20);
            $table->string('section', 50);
            $table->string('address', 255);
            $table->string('adviser', 120)->nullable();
            $table->string('phone_number', 30)->nullable();
            $table->string('email', 191)->unique();
            $table->enum('status', ['active', 'blocked'])->default('active');
            $table->timestamps();

            $table->index(['grade', 'section']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
