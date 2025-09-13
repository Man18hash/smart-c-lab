<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('borrow_events', function (Blueprint $table) {
            $table->id();

            $table->foreignId('borrowing_id')
                ->constrained('borrowings')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->enum('event_type', [
                'requested','approved','declined','checked_out','returned','auto_overdue'
            ]);

            $table->enum('actor_type', ['student','admin','system']);
            $table->unsignedBigInteger('actor_id')->nullable(); // student/admin ID (if applicable)

            $table->dateTime('event_at');
            $table->json('meta_json')->nullable();

            $table->timestamps();

            $table->index(['borrowing_id', 'event_at']);
            $table->index('event_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('borrow_events');
    }
};
