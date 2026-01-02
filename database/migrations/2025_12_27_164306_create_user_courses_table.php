<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('course_id'); // External course reference (flexible for future integration)
            $table->string('course_name')->nullable(); // Stored denormalized for display
            $table->unsignedTinyInteger('progress_percent')->default(0);
            $table->enum('status', ['in_progress', 'completed'])->default('in_progress');
            $table->boolean('is_published')->default(false);
            $table->timestamp('last_activity_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            // Indexes for efficient querying
            $table->index(['user_id', 'is_published']);
            $table->index(['user_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_courses');
    }
};
