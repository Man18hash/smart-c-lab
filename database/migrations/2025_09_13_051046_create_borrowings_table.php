<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('borrowings', function (Blueprint $table) {
            $table->id();

            // Core relationships
            $table->foreignId('student_id')
                ->constrained('students')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('laptop_id')
                ->constrained('laptops')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('ip_asset_id')
                ->nullable()
                ->constrained('ip_assets')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            // State
            $table->enum('status', ['pending', 'approved', 'declined', 'checked_out', 'returned', 'overdue'])
                  ->default('pending');

            // Meta
            $table->string('purpose', 255)->nullable();

            // Timestamps for lifecycle
            $table->dateTime('requested_at');
            $table->dateTime('approved_at')->nullable();
            $table->dateTime('checked_out_at')->nullable(); // Time OUT
            $table->dateTime('due_at')->nullable();
            $table->dateTime('returned_at')->nullable();    // Time IN

            // Admin actors (nullable; FK to users if you add an admins table)
            $table->unsignedBigInteger('approved_by_admin_id')->nullable();
            $table->unsignedBigInteger('checked_out_by_admin_id')->nullable();
            $table->unsignedBigInteger('checked_in_by_admin_id')->nullable();

            // Snapshots (optional but useful for reports)
            $table->json('student_snapshot_json')->nullable();
            $table->json('device_snapshot_json')->nullable();
            $table->json('ip_snapshot_json')->nullable();

            $table->string('remarks', 255)->nullable();

            $table->timestamps();

            // Indexes for performance/reporting
            $table->index(['student_id', 'status']);
            $table->index(['laptop_id', 'status']);
            $table->index(['ip_asset_id', 'status']);
            $table->index('due_at');
            $table->index('requested_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('borrowings');
    }
};
